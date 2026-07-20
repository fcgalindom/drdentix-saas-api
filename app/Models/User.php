<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'document', 'email', 'password', 'type_user',
        'birth', 'verify_birth', 'verify_email', 'photo', 'state',
    ];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verify_email' => 'datetime',
            'birth' => 'date',
            'password' => 'hashed',
        ];
    }

    public function dentist()
    {
        return $this->hasOne(Dentist::class, 'id_user');
    }

    public function patient()
    {
        return $this->hasOne(Patient::class, 'id_user');
    }

    public function isActive(): bool
    {
        return $this->state === 'Activo';
    }
}
