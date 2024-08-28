<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateur avec rôle de Boutiquier
        User::create([
            'prenom' => 'Diallo',
            'nom' => 'Boutique',
            'email' => 'boutiquier@gmail.com',
            'password' => 'passer',
            'role' => Role::BOUTIQUIER
        ]);

        // Utilisateur avec rôle d'Admin
        User::create([
            'prenom' => 'Ba',
            'nom' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('passer'),
            'role' => Role::ADMIN
        ]);

         // Créer un admin boutiquier
         User::factory()->admin()->create();

         // Créer un boutiquier
         User::factory()->boutiquier()->create();
 
         // Créer 3 clients
         User::factory()->count(3)->create()->each(function ($user) {
             // Chaque utilisateur est un client
             $user->client()->create(Client::factory()->make()->toArray());
         });
     

    }
}
