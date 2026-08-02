<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'hour_start' => '08:00',
            'hour_end' => '18:00',
            'break' => true,
            'break_start' => '12:00',
            'break_end' => '13:00',
            'attend' => true,
            'day' => fake()->numberBetween(1, 6),
            'dentist_id' => null,
        ];
    }
}
