<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'active_principle', 'concentration', 'amount', 'pharmaceutical_form',
        'commercial_presentation', 'medication_unit', 'batch',
        'health_register_invima', 'expiration_date', 'semaphore', 'date_of_admission',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'date_of_admission' => 'date',
        ];
    }
}
