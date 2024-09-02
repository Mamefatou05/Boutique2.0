<?php

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Utilisez l'énumération pour définir les valeurs possibles
        return [
            'name' => $this->faker->randomElement([
                Role::BOUTIQUIER(),
                Role::ADMIN(),
                Role::CLIENT(),
            ]),
        ];
    }
}
