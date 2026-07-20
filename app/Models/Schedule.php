<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'hour_start', 'hour_end', 'break', 'break_start',
        'break_end', 'attend', 'day', 'dentist_id',
    ];

    protected function casts(): array
    {
        return [
            'break' => 'boolean',
            'attend' => 'boolean',
        ];
    }

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id');
    }
}
