<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Procedure extends Model
{
    use HasCompany;

    protected $fillable = ['company_id', 'name', 'duration', 'state'];

    public function dentists()
    {
        return $this->belongsToMany(Dentist::class, 'dentist_procedures', 'procedure_id', 'dentist_id');
    }

    public function dentistProcedures()
    {
        return $this->hasMany(DentistProcedure::class, 'procedure_id');
    }
}
