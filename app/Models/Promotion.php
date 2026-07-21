<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasCompany;

    protected $fillable = ['company_id', 'date_start', 'date_end', 'details', 'discount', 'limit_patients', 'status'];

    protected function casts(): array
    {
        return [
            'date_start' => 'date',
            'date_end' => 'date',
        ];
    }
}
