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
            'password' => Hash::make('passer'), // Assurez-vous de hasher le mot de passe
            'role' => Role::BOUTIQUIER,
            'login' => 'boutiquier' // Ajoutez une valeur pour le champ `login`
        ]);

        // Utilisateur avec rôle d'Admin
        User::create([
            'prenom' => 'Ba',
            'nom' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('passer'),
            'role' => Role::ADMIN,
            'login' => 'admin' // Ajoutez une valeur pour le champ `login`
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

// curl -X POST http://localhost:3000/oauth/token -H "Content-Type: application/json" -d '{
//     "grant_type": "password",
//     "client_id": "9ce80a1e-c1dd-4d69-9b9f-9cb0c8fa106f",
//     "client_secret": "RrOiKpszAdBk78GW0PqQqcpI4jmLobqlbiURRld6",
//     "username": "Coly",
//     "password": "C0mpl3xP@ssw0rd2024!",
//     "scope": ""
// }'
