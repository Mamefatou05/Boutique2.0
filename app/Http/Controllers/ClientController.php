<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\User;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Role as ModelsRole;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class ClientController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Client::class, 'client');
    }


    /**
     * Lister les clients avec filtrage, tri et pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        // Générer une clé de cache unique basée sur les paramètres de la requête
        $cacheKey = 'clients_' . md5(serialize($request->all()));

       // Récupérer les données du cache si elles existent
       $clients = Cache::remember($cacheKey, 3600, function () use ($request) {
        return QueryBuilder::for(Client::class)
            ->allowedFilters([
                AllowedFilter::exact('telephone'),
                AllowedFilter::scope('compte'),
                AllowedFilter::scope('active'),
            ])
            ->paginate(10);
    });

        return SendResponse::jsonResponse(
            ClientResource::collection($clients),
            HttpResponse::HTTP_OK,
            StatutEnum::SUCCESS,
            'Clients retrieved successfully'
        );
    }


    /**
     * Afficher les détails d'un client spécifique.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        // Générer une clé de cache unique pour la demande
        $cacheKey = "client_{$id}";
        
        // Utiliser le cache pour stocker et récupérer les données
        $client = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Client::with('user', 'dettes')->find($id);
        });

        if (!$client) {
            return SendResponse::jsonResponse(null, HttpResponse::HTTP_NOT_FOUND, StatutEnum::FAILURE, 'Client not found');
        }

        return (new ClientResource($client))
            ->additional([
                'message' => 'Client details fetched successfully',
            ])
            ->response()
            ->setStatusCode(HttpResponse::HTTP_OK);
    }

    public function showDettes($id): JsonResponse
    {
        $cacheKey = "client_{$id}_dettes";
        
        $client = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Client::with('dettes')->find($id);
        });

        if (!$client) {
            return SendResponse::jsonResponse(null, HttpResponse::HTTP_NOT_FOUND, StatutEnum::FAILURE, 'Client not found');
        }

        return (new ClientResource($client))
            ->additional([
                'message' => 'Client and debts details fetched successfully',
            ])
            ->response()
            ->setStatusCode(HttpResponse::HTTP_OK);
    }

    public function showUser($id): JsonResponse
    {
        $cacheKey = "client_{$id}_user";
        
        $client = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($id) {
            return Client::with('user')->find($id);
        });

        if (!$client) {
            return SendResponse::jsonResponse(null, HttpResponse::HTTP_NOT_FOUND, StatutEnum::FAILURE, 'Client not found');
        }

        return (new ClientResource($client))
            ->additional([
                'message' => 'Client and user details fetched successfully',
            ])
            ->response()
            ->setStatusCode(HttpResponse::HTTP_OK);
    }
    /**
     * Créer un nouveau client.
     *
     * @param StoreClientRequest $request
     * @return JsonResponse
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        // Commencer la transaction
        DB::beginTransaction();
    
        try {
            // Extraire les données validées de la requête
            $validated = $request->validated();
    
            // Initialiser l'utilisateur
            $user = null;
    
            // Vérifier si les données utilisateur sont présentes
            if (isset($validated['user'])) {
                // Extraire les données utilisateur depuis les données validées
                $userData = $validated['user'];
    
                // Vérifier si le rôle existe ou le créer
                $role = ModelsRole::firstOrCreate(['name' => $userData['role']]);
    
                // Créer un nouvel utilisateur avec les données extraites
                $user = User::create([
                    'nom' => $userData['nom'],
                    'prenom' => $userData['prenom'],
                    'login' => $userData['login'],
                    'password' => bcrypt($userData['password']),
                    'role_id' => $role->id,
                ]);
            }
    
            // Créer le client
            $client = new Client([
                'telephone' => $validated['telephone'],
                'adresse' => $validated['address'],
                'surname' => $validated['surname'],
                'active' => $validated['active'] ?? 0, // Assurez-vous que 'active' est défini, par défaut à 0
            ]);
    
            // Associer le client à l'utilisateur s'il existe
            if ($user) {
                $client->user()->associate($user);
            }
    
            // Sauvegarder le client
            $client->save();
    
            // Commit la transaction
            DB::commit();
    
            // Utiliser ClientResource pour transformer le modèle en une réponse formatée
            $clientResource = new ClientResource($client);
    
            // Passer la ressource à SendResponse
            return SendResponse::jsonResponse(
                $clientResource->resolve(), // Utilisez `resolve()` pour obtenir les données du modèle
                HttpResponse::HTTP_CREATED, // Utilisation de la constante HTTP_CREATED
                StatutEnum::SUCCESS,
                'Client created successfully',
            );
    
        } catch (\Exception $e) {
            // Rollback la transaction en cas d'erreur
            DB::rollBack();
    
            // Utiliser SendResponse pour gérer les erreurs
            return SendResponse::jsonResponse(
                null,
                HttpResponse::HTTP_INTERNAL_SERVER_ERROR, // Utilisation de la constante HTTP_INTERNAL_SERVER_ERROR
                StatutEnum::FAILURE,
                'An error occurred: ' . $e->getMessage()
            );
        }
    }
    /**
     * Mettre à jour un client existant.
     *
     * @param UpdateClientRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, $id): JsonResponse
    {
        // Récupérez le client
        $client = Client::find($id);
        if (!$client) {
            return $this->jsonResponse(null, HttpResponse::HTTP_NOT_FOUND, 'Client not found');
        }

        // Les données ont déjà été validées par UpdateClientRequest
        $clientData = $request->validated();

        try {
            // Mettez à jour les données du client
            $client->update($clientData);

            // Invalider le cache spécifique au client
            Cache::tags('clients')->forget('client:' . $id);

            // Optionnel : Invalider le cache général des clients
            // Cache::tags('clients')->flush();

            return (new ClientResource($client))
                ->additional([
                    'message' => 'Client updated successfully',
                    'links' => [
                        'self' => route('clients.show', ['client' => $client->id]),
                        'update' => route('clients.update', ['client' => $client->id]),
                        'delete' => route('clients.destroy', ['client' => $client->id]),
                    ],
                ])
                ->response()
                ->setStatusCode(HttpResponse::HTTP_OK); // Utilisation de la constante HTTP_OK
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'An unexpected error occurred.'], HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'Server Error');
        }
    }

    /**
     * Supprimer un client existant.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        // Récupérez le client
        $client = Client::find($id);
        if (!$client) {
            return $this->jsonResponse(null, HttpResponse::HTTP_NOT_FOUND, 'Client not found');
        }

        try {
            // Supprimez le client
            $client->delete();

            // Invalider le cache spécifique au client
            Cache::tags('clients')->forget('client:' . $id);

            // Invalider le cache général des clients
            Cache::tags('clients')->flush();

            return $this->jsonResponse(null, HttpResponse::HTTP_NO_CONTENT, 'Client deleted successfully');
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'An unexpected error occurred.'], HttpResponse::HTTP_INTERNAL_SERVER_ERROR, 'Server Error');
        }
    }
}
