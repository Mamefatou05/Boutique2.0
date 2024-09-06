<?php

namespace App\Services;

use App\Enums\Role;
use App\Events\PhotoEvent;
use App\Events\SendQrCodeEmailEvent;
use App\Facades\ClientRepositoryFacade as ClientRepository;
use App\Facades\UserRepositoryFacade as UserRepository;
use App\Models\Client;
use App\Models\Role as RoleModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ClientRepositoryInterface;
use Illuminate\Support\Facades\Mail;

class ClientService implements ClientServiceInterface
{

    // protected $clientRepository;


    // public function __construct(ClientRepositoryInterface $clientRepository){
    //     $this->clientRepository = $clientRepository;
    // }
    // protected $uploadService;

    // public function __construct(UploadServiceInterface $uploadService)
    // {
    //     $this->uploadService = $uploadService;
    // }




    public function getAllClients($request)
    {

        $cacheKey = 'clients_' . md5(serialize($request->all()));
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $filters = [
                'telephone' => $request->input('telephone'),
                'adresse' => $request->input('adresse'),
                'surname' => $request->input('surname'),
                'email' => $request->input('email'),
                'compte' => $request->input('compte'),

            ];

            $CLient = ClientRepository::getAllClients($filters)->paginate(10);

            return $CLient;
        });
    }

    public function getClientById($id, array $relations = [])
    {
        $cacheKey = "client_{$id}";
        return Cache::tags('clients')->remember($cacheKey, now()->addMinutes(10), function () use ($id, $relations) {
            $client = ClientRepository::getClientById($id, $relations);
            return $client;
        });
    }

    public function getClientsByTelephone($request)
    {
        $telephone = $request->input('telephone');

        $cacheKey = "client_by_telephone_{$telephone}";

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($telephone) {

            $filters = [
                'telephone' => $telephone
            ];
            $client = ClientRepository::getAllClients($filters)->first();

            return $client;
        });
    }




    public function createClient($data)
    {
        // Instancier le service pour gérer l'upload et le QR code
        $uploadService = new UploadCloudService;

        DB::beginTransaction();
        try {
            // Générer le QR code sous forme de base64 à partir du numéro de téléphone
            $qrCodeBase64 = $uploadService->generateQRCode($data['telephone']);
            $user = null;

            // Créer l'utilisateur si les données utilisateur sont fournies
            if (isset($data['user'])) {
                $userData = $data['user'];
                // Créer l'utilisateur dans la base de données (l'Observer gère l'upload de la photo)
                $user = UserRepository::createUser($userData);
            }

            $client = new Client();
            $client->fill([
                'adresse' => $data['addresse'],
                'telephone' => $data['telephone'],
                'surname' => $data['surname'],
                'email' => $data['email'],
                'qr_code_base64' => $qrCodeBase64
            ]);


            // Si l'utilisateur est créé, l'associer au client avant de sauvegarder
            $client->user()->associate($user ?? null);

            // Sauvegarder le client dans la base de données (avec ou sans utilisateur associé)
            $client->save();
            // Déclencher l'événement pour envoyer un email avec le QR Code
            event(new SendQrCodeEmailEvent($client, $user));

            DB::commit();
            return $client;
        } catch (\Exception $e) {
            // En cas d'échec, annuler la transaction
            DB::rollBack();
            throw $e;
        }
    }

    public function updateClient($id, $data)
    {
        $client = ClientRepository::updateClient($id, $data);
        Cache::tags('clients')->forget('client:' . $id);
        return $client;
    }

    public function deleteClient($id)
    {
        ClientRepository::deleteClient($id);
        Cache::tags('clients')->forget('client:' . $id);
        Cache::tags('clients')->flush();
    }
}
