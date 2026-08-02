<?php

namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Promotion>
 */
class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'date_start' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'date_end' => fake()->dateTimeBetween('+1 month', '+3 months')->format('Y-m-d'),
            'details' => fake()->paragraph(),
            'discount' => fake()->numberBetween(5, 50),
            'limit_patients' => fake()->numberBetween(10, 100),
            'status' => 1,
        ];
    }
}
