<?php

namespace App\Services;

use App\Facades\ClientRepositoryFacade as ClientRepository;
use App\Facades\UserRepositoryFacade as UserRepository;

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
        $uploadService = new UploadService;

        DB::beginTransaction();
        try {
            // Générer le QR code
            $qrCodeBase64 = $uploadService->generateQRCode($data['telephone']);
            $user = null;

            // Créer l'utilisateur si les données sont fournies
            if (isset($data['user'])) {
                $userData = $data['user'];
                $role = RoleModel::firstOrCreate(['name' => 'CLIENT']);
                $photoBase64 = $uploadService->saveImageAsBase64($userData['photo']);
                
                $user = UserRepository::createUser([
                    'nom' => $userData['nom'],
                    'prenom' => $userData['prenom'],
                    'login' => $userData['login'],
                    'password' =>$userData['password'],
                    'role_id' => $role->id,
                    'photo' => $photoBase64
                ]);
            }

            // Créer le client via le repository
            $clientData = [
                'adresse' => $data['addresse'],
                'telephone' => $data['telephone'],
                'surname' => $data['surname'],
                'email' => $data['email'],
                'qr_code_base64' => $qrCodeBase64
            ];

            $client = ClientRepository::createClient($clientData);

            // Associer l'utilisateur au client si créé
            if ($user) {
                $client->user()->associate($user);
                $client->save();  // Sauvegarder les modifications de l'association
            }

            Mail::to($data['email'])->send(new QrCodeEmailService($qrCodeBase64));

            DB::commit();
            return $client;

        } catch (\Exception $e) {
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