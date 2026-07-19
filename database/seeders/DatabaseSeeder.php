<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        User::factory()->create([
            'document' => '1234567890',
            'email' => 'admin@dentix.com',
            'password' => bcrypt('password'),
            'type_user' => 'Administrador',
        ])->assignRole('Administrador');
    }
}
