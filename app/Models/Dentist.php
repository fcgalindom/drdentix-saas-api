<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Dentist extends Model
{
    use HasCompany;

    protected $fillable = ['company_id', 'name', 'city', 'id_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function procedures()
    {
        return $this->belongsToMany(Procedure::class, 'dentist_procedures', 'dentist_id', 'procedure_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'dentist_id');
    }

    public function activeSchedules()
    {
        return $this->hasMany(Schedule::class, 'dentist_id')->where('attend', true);
    }

    public function dentistProcedures()
    {
        return $this->hasMany(DentistProcedure::class, 'dentist_id');
    }
}
