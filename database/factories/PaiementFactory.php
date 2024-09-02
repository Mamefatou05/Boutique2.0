<?php

namespace Database\Factories;

use App\Models\Dette;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'montant' => $this->faker->randomFloat(2, 50, 500),
            'date' => $this->faker->date(),
            'dette_id' => Dette::factory(),
        ];
    }
}
