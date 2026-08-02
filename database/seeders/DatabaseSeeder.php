<?php

namespace Database\Seeders;

use App\Models\Company;
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
        $this->call(DemoDataSeeder::class);

        $companyId = Company::value('id');

        $admin = User::create([
            'company_id' => $companyId,
            'document' => 'admin',
            'email' => 'admin@dental.com',
            'password' => Hash::make('admin123'),
            'type_user' => 'Administrator',
            'state' => 'Activo',
        ]);
        $admin->assignRole('Administrador');

        $dentistUser = User::create([
            'company_id' => $companyId,
            'document' => 'dentist01',
            'email' => 'dentist@dental.com',
            'password' => Hash::make('dentist123'),
            'type_user' => 'Dentist',
            'birth' => '1985-06-15',
            'state' => 'Activo',
        ]);
        $dentistUser->assignRole('Dentist');

        Dentist::create([
            'company_id' => $companyId,
            'name' => 'Dr. Juan Pérez',
            'city' => 'Medellín',
            'id_user' => $dentistUser->id,
        ]);

        $patientUser = User::create([
            'company_id' => $companyId,
            'document' => 'patient01',
            'email' => 'patient@dental.com',
            'password' => Hash::make('patient123'),
            'type_user' => 'Patient',
            'birth' => '1990-05-20',
            'state' => 'Activo',
        ]);
        $patientUser->assignRole('Patient');

        Patient::create([
            'company_id' => $companyId,
            'name' => 'María García',
            'city' => 'Bogotá',
            'telephone' => '+573009876543',
            'id_user' => $patientUser->id,
        ]);
    }
}
