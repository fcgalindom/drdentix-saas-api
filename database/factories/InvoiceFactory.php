<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'price' => fake()->numberBetween(30_000, 2_000_000),
            'procedure_id' => null,
            'appointment_id' => null,
        ];
    }
}
