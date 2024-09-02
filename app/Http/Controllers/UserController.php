<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\HttpFoundation\Response as HttpResponse;


class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }


    
    // Liste tous les utilisateurs
    public function index(Request $request)
    {
        // Générer une clé de cache unique basée sur les paramètres de la requête
        $cacheKey = 'users_' . md5(serialize($request->all()));

        // Récupérer les données du cache si elles existent
        $users = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::scope('active'),
                AllowedFilter::scope('role'),
            ])
            ->allowedSorts('created_at');
            
            // if ($request->has('role')) {
            //     $query->role($request->input('role'));
            // }

            // if ($request->has('active')) {
            //     $query->active($request->input('active'));
            // }

            return $query->paginate(10);
        });

        return SendResponse::jsonResponse(
            UserResource::collection($users),
            HttpResponse::HTTP_OK,
            StatutEnum::SUCCESS,
            'Users retrieved successfully'
        );
    }

    // Affiche un utilisateur spécifique
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->jsonResponse(null, 404, 'User not found');
        }

        // Ajout des liens HATEOAS
        $user->links = [
            'self' => route('users.show', ['id' => $user->id]),
            'update' => route('users.update', ['user' => $user->id]),
            'delete' => route('users.destroy', ['user' => $user->id]),
        ];

        return $this->jsonResponse($user, 200, 'User retrieved successfully', $user->links);
    }

    // Crée un nouvel utilisateur
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 422, "Validation Error");
        }

        $user = User::create($request->only([
            "nom",
            "prenom",
            "password",
            "role",
            "login"
        ]));

        Log::info('Created User:', $user->toArray());



        // Pas de liens nécessaires ici
        return $this->jsonResponse($user, 201, "User created successfully");
    }

    // Met à jour un utilisateur existant
    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string|max:255',
            'prenom' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 422, "Validation Error");
        }

        $user->update($request->only(["nom", "prenom", "email", "password", "role", "login"]));

        // Pas de liens nécessaires ici
        return $this->jsonResponse($user, 200, "User updated successfully");
    }

    // Supprime un utilisateur
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        // Pas de liens nécessaires ici
        return $this->jsonResponse(null, 204, "User deleted successfully");
    }
}
