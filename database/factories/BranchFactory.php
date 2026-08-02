<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    private static array $branchNames = [
        'Sede Principal',
        'Sede Norte',
        'Sede Sur',
        'Sede Centro',
        'Sede Occidente',
        'Sede Oriente',
    ];

    private static int $nameIndex = 0;

    public function definition(): array
    {
        $name = self::$branchNames[self::$nameIndex % count(self::$branchNames)];
        self::$nameIndex++;

        return [
            'company_id' => 1,
            'name' => $name,
            'address' => fake()->streetAddress(),
            'contact' => fake()->phoneNumber(),
            'city' => fake()->randomElement(['Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Bucaramanga']),
            'state' => 'Activo',
        ];
    }
}
