<?php

namespace Database\Factories;

use App\Models\DentistProcedure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DentistProcedure>
 */
class DentistProcedureFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'procedure_id' => null,
            'dentist_id' => null,
        ];
    }
}
