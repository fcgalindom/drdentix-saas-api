<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'document' => fake()->unique()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'type_user' => fake()->randomElement(['Administrator', 'Dentist', 'Patient']),
            'birth' => fake()->date(),
            'photo' => '/images/default.jpg',
            'state' => 'Activo',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_user' => 'Administrator',
        ]);
    }

    public function dentist(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_user' => 'Dentist',
        ]);
    }

    public function patient(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_user' => 'Patient',
        ]);
    }
}
