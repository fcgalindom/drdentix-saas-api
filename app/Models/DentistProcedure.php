<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class DentistProcedure extends Model
{
    use HasCompany;

    protected $table = 'dentist_procedures';

    protected $fillable = ['company_id', 'procedure_id', 'dentist_id'];

    public function dentist()
    {
        return $this->belongsTo(Dentist::class, 'dentist_id');
    }

    public function procedure()
    {
        return $this->belongsTo(Procedure::class, 'procedure_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'dentist_procedure_id');
    }
}
