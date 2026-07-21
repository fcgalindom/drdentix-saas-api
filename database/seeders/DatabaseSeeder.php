<?php

namespace Database\Seeders;

use App\Models\Dentist;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $admin = User::create([
            'document' => 'admin',
            'email' => 'admin@dental.com',
            'password' => Hash::make('admin123'),
            'type_user' => 'Administrator',
            'state' => 'Activo',
        ]);
        $admin->assignRole('Administrador');

        $dentistUser = User::create([
            'document' => 'dentist01',
            'email' => 'dentist@dental.com',
            'password' => Hash::make('dentist123'),
            'type_user' => 'Dentist',
            'birth' => '1985-06-15',
            'state' => 'Activo',
        ]);
        $dentistUser->assignRole('Dentist');

        Dentist::create([
            'name' => 'Dr. Juan Pérez',
            'city' => 'Medellín',
            'id_user' => $dentistUser->id,
        ]);

        $patientUser = User::create([
            'document' => 'patient01',
            'email' => 'patient@dental.com',
            'password' => Hash::make('patient123'),
            'type_user' => 'Patient',
            'birth' => '1990-05-20',
            'state' => 'Activo',
        ]);
        $patientUser->assignRole('Patient');

        Patient::create([
            'name' => 'María García',
            'city' => 'Bogotá',
            'telephone' => '+573009876543',
            'id_user' => $patientUser->id,
        ]);
    }
}
