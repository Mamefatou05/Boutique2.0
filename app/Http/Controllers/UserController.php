<?php
namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\HasApiTokens;

class UserController extends Controller
{
    use HasApiTokens, Notifiable;


    // Liste tous les utilisateurs
    public function index(): JsonResponse
    {
        $users = User::all();
        
        // Ajout des liens HATEOAS si nécessaire
        $users->each(function ($user) {
            $user->links = [
                'self' => route('users.show', ['id' => $user->id]),
                'update' => route('users.update', ['user' => $user->id]),
                'delete' => route('users.destroy', ['user' => $user->id]),
            ];
        });

        // Passer `links` seulement s'il y en a
        return $this->jsonResponse($users, 200, 'Users retrieved successfully', $users->first()->links ?? []);
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 422, "Validation Error");
        }

        $user = User::create($request->only([
            "nom", "prenom", "email", "password", "role" , "login"
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

        $user->update($request->only(["nom", "prenom", "email", "password", "role" , "login"]));

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
