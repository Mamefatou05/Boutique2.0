<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ClientRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Services\UserService;

class ClientController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }public function show($id): JsonResponse
    {
        $client = Client::find($id);
        if (!$client) {
            return $this->jsonResponse(null, 404, 'Client not found');
        }
    
        $this->authorize('view', $client);
    
        $links = [
            'self' => route('clients.show', ['id' => $client->id]),
            'update' => route('clients.update', ['id' => $client->id]),
            'delete' => route('clients.destroy', ['id' => $client->id]),
        ];
    
        return $this->jsonResponse($client, 200, 'Client details fetched successfully', $links);
    }
    

    public function store(ClientRequest $request): JsonResponse
    {
        // Vérifiez les autorisations avant de créer un client
        $this->authorize('create', Client::class);

        $clientData = $request->validated();

        // Créez un utilisateur si les données utilisateur sont présentes
        if (isset($clientData['user'])) {
            $userData = $clientData['user'];
            $userData ['role'] = 'CLIENTS' ;

            try {
                $user = $this->userService->createUser($userData);
                $clientData['user_id'] = $user->id;
            } catch (\Illuminate\Validation\ValidationException $e) {
                return $this->jsonResponse($e->errors(), 422, 'Validation Error');
            }
        }

        // Créez le client
        $client = Client::create($clientData);

        $links = [
            'self' => route('clients.show', ['id' => $client->id]),
            'update' => route('clients.update', ['id' => $client->id]),
            'delete' => route('clients.destroy', ['id' => $client->id]),
        ];

        return $this->jsonResponse($client, 201, 'Client created successfully', $links);
    }

    public function index(): JsonResponse
    {

        $this->authorize('viewAny', Client::class);

        $clients = QueryBuilder::for(Client::class)
            ->allowedFilters(['nom', 'email'])
            ->allowedSorts('nom', 'created_at')
            ->paginate(10);

        $links = [
            'self' => route('clients.index'),
            'create' => route('clients.store'),
        ];

        return $this->jsonResponse($clients, 200, 'Clients fetched successfully', $links);
    }
}
