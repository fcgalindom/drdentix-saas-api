<?php

namespace App\Models;

use App\Models\Concerns\HasCompany;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasCompany;

    protected $table = 'patients';

    protected $fillable = ['company_id', 'name', 'city', 'telephone', 'id_user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function activeUser()
    {
        return $this->belongsTo(User::class, 'id_user')->where('state', 'Activo');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
}
