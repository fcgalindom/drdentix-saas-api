<?php

namespace Database\Factories;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    public function definition(): array
    {
        $hour = fake()->numberBetween(7, 17);
        $minute = fake()->randomElement(['00', '30']);

        return [
            'company_id' => 1,
            'day' => fake()->dateTimeBetween('-3 months', '+3 months')->format('Y-m-d'),
            'hour' => sprintf('%02d:%s', $hour, $minute),
            'branch_id' => null,
            'patient_id' => null,
            'dentist_procedure_id' => null,
            'state' => fake()->randomElement(['Activo', 'Activo', 'Activo', 'Recordado']),
            'pay' => fake()->randomFloat(2, 50_000, 2_000_000),
            'type_state' => fake()->numberBetween(0, 2),
        ];
    }
}
