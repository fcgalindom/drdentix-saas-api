<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Dentix El Poblado',
                'address' => 'Carrera 43A # 1A Sur - 45, Edificio Forum, Local 102',
                'contact' => '(+57) 604 312 45 67',
                'city' => 'Medellín',
            ],
            [
                'name' => 'Dentix Centro',
                'address' => 'Calle 12 # 4 - 25, Edificio Coltejer, Piso 3',
                'contact' => '(+57) 601 342 78 90',
                'city' => 'Bogotá',
            ],
            [
                'name' => 'Dentix La 93',
                'address' => 'Carrera 11A # 93 - 52, Piso 5',
                'contact' => '(+57) 601 618 23 45',
                'city' => 'Bogotá',
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create([
                ...$branch,
                'state' => 'Activo',
            ]);
        }
    }
}
