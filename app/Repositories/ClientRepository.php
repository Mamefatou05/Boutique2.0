<?php

namespace App\Repositories;

use App\Enums\Role as EnumsRole;
use App\Enums\StatutEnum;
use App\Models\Client;
use App\Models\User;
use App\Models\Role;
use App\Services\UploadService;
use App\Services\UserQrCodeEmail;
use Illuminate\Support\Facades\Mail;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ClientRepository implements ClientRepositoryInterface
{


    public function getAllClients($request)
    {
        return Client::filter($request);

    }

    public function getClientById($id, array $relations = [])
    {
        $query = Client::query();
    
        if (!empty($relations)) {
            $query->with($relations);
        }
    
        return $query->findOrFail($id);
    }
    
    // public function createClient($data)
    // {
    //     $user = null;
    //     $qrCodeBase64 = $this->uploadService->generateQRCode($data['telephone']); // Génération du QR code en base64

    //     if (isset($data['user'])) {
    //         $userData = $data['user'];
    //         $role = Role::firstOrCreate(['name' => 'CLIENT']);
            
    //         // Utilisation du service d'upload pour gérer la photo
    //         $photoPath = $this->uploadService->uploadFile($userData['photo'], 'photos');
    //         $photoBase64 = $this->uploadService->saveImageAsBase64($userData['photo']);
            
    //         $user = User::create([
    //             'nom' => $userData['nom'],
    //             'prenom' => $userData['prenom'],
    //             'login' => $userData['login'],
    //             'password' => $userData['password'],
    //             'role_id' => $role->id,
    //             'active' => $data['active'] ?? 0,
    //             'photo' => $photoBase64 ,// Sauvegarde de l'image en base64

    //         ]);
    //     }

    //     $client = new Client([
    //         'adresse' => $data['addresse'],
    //         'telephone' => $data['telephone'],
    //         'surname' => $data['surname'],
    //         'email' => $data['email'],
    //         'qr_code_base64' => $qrCodeBase64 // Sauvegarde du QR code en base64
    //     ]);

    //     // Mail::to($client->email)->send(new UserQrCodeEmail($qrCodeBase64));

    //     if ($user) {
    //         $client->user()->associate($user);
    //     }

    //     $client->save();
    //     return $client;
    // }


    public function createClient($data)
    {
        // Créer et retourner un nouveau client
        return Client::create($data);
    }


    public function updateClient($id, $data)
    {
        $client = Client::findOrFail($id);
        $client->update($data);
        return $client;
    }

    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
    }
}