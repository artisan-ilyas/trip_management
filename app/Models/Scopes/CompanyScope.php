<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\App;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (App::has('tenant')) {
            $tenant = App::make('tenant');
            $builder->where($model->getTable() . '.company_id', $tenant->id);
        }
    }
}
