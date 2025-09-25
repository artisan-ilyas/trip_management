<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;

class TenantResolver
{
    public function handle($request, Closure $next)
    {
        // Resolve tenant from slug (public) or auth (admin)
        $company = null;

        if ($request->has('company')) {
            $company = Company::where('slug', $request->get('company'))->first();
        } elseif (auth()->check()) {
            $company = auth()->user()->company ?? null;
        }

        if ($company) {
            app()->instance('tenant', $company);

            // Force tenant connection (for now same DB, later per-tenant)
            config(['database.default' => 'tenant']);
        }

        return $next($request);
    }
}
