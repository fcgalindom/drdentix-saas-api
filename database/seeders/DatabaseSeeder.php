<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $company = Company::create(['name' => 'Dentix Principal']);

        User::factory()->create([
            'company_id' => $company->id,
            'document' => '1234567890',
            'email' => 'admin@dentix.com',
            'password' => bcrypt('password'),
            'type_user' => 'Administrador',
        ])->assignRole('Administrador');
    }
}
