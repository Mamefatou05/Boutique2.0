<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Services\AuthentificationServiceInterface;
use Illuminate\Http\Request;
use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Client;
use App\Models\Role as RoleModel;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthentificationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
        {
            Log::info('Login Request Data:', $request->all());
    
            // Démarrer la transaction
            DB::beginTransaction();
    
            try {
    
                // Vérifier si le rôle existe ou le créer
                $role = RoleModel::firstOrCreate(['name' => Role::CLIENT]);
    
                // Initialisation du chemin de la photo
                $photoPath = null;
    
                if ($request->hasFile('photo')) {
                    // Sauvegarder la photo dans le répertoire public/storage
                    $photoPath = $request->file('photo')->store('photos', 'public');
                }
    
                // Créer un utilisateur
                $user = User::create([
                    'login' => $request->input('login'),
                    'password' => bcrypt($request->input('password')),
                    'nom' => $request->input('nom'),
                    'prenom' => $request->input('prenom'),
                    'role_id' => $role->id,
                    'photo' => $photoPath,
                    'active' => false
    
                ]);
    
                // Mettre à jour le client avec le user_id
                $client = Client::find($request->input('clientid'));
                $client->user_id = $user->id;
                $client->save();
    
                // Commit transaction
                DB::commit();
    
                // Réponse succès
                return SendResponse::jsonResponse(
                    [
                        'client' => $client,
                        'user' => $user,
                    ],
                    HttpResponse::HTTP_CREATED, // Code HTTP pour la création
                    StatutEnum::SUCCESS,
                    'User created and client updated successfully.'
                );
            } catch (\Exception $e) {
                // Rollback transaction en cas d'erreur
                DB::rollBack();
    
                // Réponse erreur
                return SendResponse::jsonResponse(
                    [
                        'message' => $e->getMessage(),
                    ],
                    HttpResponse::HTTP_BAD_REQUEST, // Code HTTP pour une requête incorrecte
                    StatutEnum::FAILURE,
                    'Failed to create user and update client.'
                );
            }
        }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();


        $result = $this->authService->authenticate($credentials);

        if ($result) {
            return SendResponse::jsonResponse(
                $result,
                200,
                StatutEnum::SUCCESS,
                'Login successful'
            );
        }

        return SendResponse::jsonResponse(
            null,
            401,
            StatutEnum::FAILURE,
            'Invalid credentials'
        );
    }

    public function logout()
    {
        $this->authService->logout();
        return SendResponse::jsonResponse(
            null,
            200,
            StatutEnum::SUCCESS,
            'Logout successful'
        );
    }

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->input('refresh_token');
        $result = $this->authService->refreshToken($refreshToken);

        if ($result) {
            return SendResponse::jsonResponse(
                $result,
                200,
                StatutEnum::SUCCESS,
                'Token refreshed successfully'
            );
        }

        return SendResponse::jsonResponse(
            null,
            400,
            StatutEnum::FAILURE,
            'Failed to refresh token'
        );
    }
}
        