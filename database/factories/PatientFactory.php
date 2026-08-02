<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name' => fake()->firstName().' '.fake()->lastName(),
            'city' => fake()->randomElement(['Medellín', 'Bogotá', 'Cali', 'Barranquilla', 'Bucaramanga']),
            'telephone' => fake()->phoneNumber(),
            'id_user' => null,
        ];
    }
}
