<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Company;

class TenantResolver
{
    public function handle($request, Closure $next)
    {
        // get subdomain (abc from abc.localhost)
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // find company by domain/subdomain
        $company = Company::where('domain', $subdomain.'.localhost')->first();

        if (! $company) {
            abort(404, 'Company not found');
        }

        // share company info globally
        app()->instance('company', $company);

        return $next($request);
    }
}

