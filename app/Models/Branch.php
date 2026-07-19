<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['address', 'name', 'contact', 'city', 'state'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'branch_id');
    }
}
