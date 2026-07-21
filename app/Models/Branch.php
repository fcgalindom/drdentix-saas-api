<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasCompany;

    protected $fillable = ['company_id', 'address', 'name', 'contact', 'city', 'state'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'branch_id');
    }
}
