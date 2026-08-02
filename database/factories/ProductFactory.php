<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    private static array $activePrinciples = [
        'Amoxicilina', 'Ibuprofeno', 'Paracetamol', 'Clorhexidina', 'Lidocaína',
        'Naproxeno', 'Diclofenaco', 'Ketorolaco', 'Tramadol', 'Metronidazol',
        'Clindamicina', 'Azitromicina', 'Doxiciclina', 'Fluoruro de sodio', 'Povidona yodada',
    ];

    private static array $pharmaceuticalForms = [
        'Tableta', 'Cápsula', 'Jarabe', 'Suspensión', 'Solución inyectable',
        'Crema', 'Gel', 'Enjuague bucal', 'Barniz', 'Polvo',
    ];

    public function definition(): array
    {
        return [
            'company_id' => 1,
            'active_principle' => fake()->randomElement(self::$activePrinciples),
            'concentration' => fake()->randomElement(['500mg', '200mg', '100mg', '50mg', '250mg', '0.12%', '2%', '5%']),
            'amount' => fake()->numberBetween(10, 500),
            'pharmaceutical_form' => fake()->randomElement(self::$pharmaceuticalForms),
            'commercial_presentation' => fake()->randomElement(['Caja x 10', 'Caja x 20', 'Caja x 30', 'Frasco x 60ml', 'Frasco x 120ml', 'Tubo x 50g']),
            'medication_unit' => fake()->randomElement(['Unidad', 'Mililitro', 'Gramo']),
            'batch' => strtoupper(fake()->bothify('LOTE-###-??')),
            'health_register_invima' => 'INVIMA-'.fake()->numerify('####-######'),
            'expiration_date' => fake()->dateTimeBetween('+6 months', '+3 years')->format('Y-m-d'),
            'semaphore' => fake()->randomElement(['Verde', 'Amarillo', 'Rojo']),
            'date_of_admission' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ];
    }
}
