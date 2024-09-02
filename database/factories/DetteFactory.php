<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Dette;
use App\Models\Client;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dette>
 */
class DetteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'montant' => $this->faker->randomFloat(2, 100, 1000),
            'montantDu' => $this->faker->randomFloat(2, 100, 1000),
            'client_id' => Client::factory(),
        ];
    }
}
