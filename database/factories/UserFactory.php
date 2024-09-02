<?php
namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'prenom' => $this->faker->firstName,
            'nom' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $this->faker->password, // Utilisez Hash::make si vous voulez un mot de passe hashé
            'role' => $this->faker->randomElement(['BOUTIQUIER', 'ADMIN']),
            'login' => $this->faker->userName, // Utilisez un format valide
        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => 'ADMIN',
        ]);
    }

    public function boutiquier()
    {
        return $this->state([
            'role' => 'BOUTIQUIER',
        ]);
    }
}
