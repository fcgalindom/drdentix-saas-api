<?php

namespace Database\Factories;

use App\Models\Dentist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dentist>
 */
class DentistFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name' => 'Dr. '.fake()->firstName().' '.fake()->lastName(),
            'city' => fake()->randomElement(['Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Bucaramanga']),
            'id_user' => null,
        ];
    }
}
