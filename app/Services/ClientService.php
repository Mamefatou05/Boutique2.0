<?php

namespace App\Services;

use App\Facades\ClientRepositoryFacade as ClientRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ClientRepositoryInterface;

class ClientService implements ClientServiceInterface
{

// protected $clientRepository;


    // public function __construct(ClientRepositoryInterface $clientRepository){
    //     $this->clientRepository = $clientRepository;
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
        DB::beginTransaction();
        try {
            $client = ClientRepository::createClient($data);
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