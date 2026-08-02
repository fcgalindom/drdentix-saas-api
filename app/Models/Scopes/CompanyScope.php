<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $companyId = auth()->user()?->company_id;

        if ($companyId) {
            $builder->where($model->getTable().'.company_id', $companyId);
        }
    }
}
