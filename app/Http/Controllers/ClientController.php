<?php

namespace App\Http\Controllers;

use App\Facades\ClientServiceFacade;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    // public function __construct()
    // {
    //     $this->authorizeResource(Client::class, 'client');
    // }

    public function index(Request $request)
    {
        $clients = ClientServiceFacade::getAllClients($request);
        return ClientResource::collection($clients);
    }

    public function Find($id)
    {
        $client = ClientServiceFacade::getClientById($id);
        $this->authorize('viewOne', $client);
        return new ClientResource($client);
    }
    

    public function store(StoreClientRequest $request)
    {
        $client = ClientServiceFacade::createClient($request->validated());
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, $id)
    {
        $client = ClientServiceFacade::updateClient($id, $request->validated());
        return new ClientResource($client);
    }

    public function showDettes($id)
    {
        $client = ClientServiceFacade::getClientById($id, ['dettes']);
        
        // Appel explicite à la méthode de politique
        $this->authorize('viewDette', $client);

        return $client;
    }

    public function showUser($id)
    {
        $client = ClientServiceFacade::getClientById($id, ['user']);
        
        // Appel explicite à la méthode de politique
        $this->authorize('viewUser', $client);

       return new ClientResource ($client);
    }

    public function showByTel(Request $request)
    {

        $client = ClientServiceFacade::getClientsByTelephone($request);

        return new ClientResource($client);
    }

    public function destroy($id)
    {
        ClientServiceFacade::deleteClient($id);
        return null;
    }
}