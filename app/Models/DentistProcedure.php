<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DentistProcedure extends Model
{
    protected $table = 'dentist_procedures';

    protected $fillable = ['procedure_id', 'dentist_id'];

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
