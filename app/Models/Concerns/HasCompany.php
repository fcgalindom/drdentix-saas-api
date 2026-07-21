<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCompany
{
    protected static function bootHasCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($model) {
            if (! $model->company_id) {
                $model->company_id = auth()->user()->company_id
                    ?? Company::value('id')
                    ?? Company::create(['name' => 'Default'])->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
