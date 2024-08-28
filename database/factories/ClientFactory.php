<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nom' => fake()->firstName(),
            'prenom' => fake()->lastName(),
            'telephone' => fake()->regexify('(77|78|76)[0-9]{7}'),
            'adresse' => fake()->address(),
                ];
    }

}
