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
        User::create([
            'document'  => 'admin',
            'email'     => 'admin@dental.com',
            'password'  => \Illuminate\Support\Facades\Hash::make('admin123'),
            'type_user' => 'Administrator',
            'state'     => 'Activo',
        ]);
    }
}
