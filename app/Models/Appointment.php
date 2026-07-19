<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'day', 'hour', 'branch_id', 'patient_id',
        'dentist_procedure_id', 'state', 'pay', 'type_state',
    ];

    protected function casts(): array
    {
        return [
            'day' => 'date',
            'pay' => 'float',
        ];
    }

    public function dentistProcedure()
    {
        return $this->belongsTo(DentistProcedure::class, 'dentist_procedure_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'appointment_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('state', ['Activo', 'Recordado']);
    }

    public function scopeNotDeleted($query)
    {
        return $query->where('state', '!=', 'Eliminado');
    }
}
