<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\TokenService;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->authorizeResource(User::class, 'user');

    }

    public function login(LoginRequest $request): JsonResponse
    {
        Log::info('Login Request Data:', $request->all());
    
        $credentials = $request->validated();
    
        if (!Auth::attempt($credentials)) {
            return SendResponse::jsonResponse(null, 401, StatutEnum::FAILURE, 'Invalid credentials');
        }
    
        $user = Auth::user();
        if (!$user instanceof User) {
            return SendResponse::jsonResponse(null, 500, StatutEnum::FAILURE, 'Authentication failed, user not found');
        }
    
        // Appel à TokenService pour générer les tokens
        $tokens = $this->tokenService->generateTokens($user);
    
        return SendResponse::jsonResponse([
            'user' => $user,
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => $tokens['expires_at'],
        ], 200, StatutEnum::SUCCESS, 'Login successful');
    }
    
    public function register(RegisterRequest $request): JsonResponse
    {
        // Démarrer la transaction
        DB::beginTransaction();

        try {
            // Créer un utilisateur
            $user = User::create([
                'login' => $request->input('login'),
                'password' =>$request->input('password'),
                'nom' => $request->input('nom'),
                'prenom' => $request->input('prenom'),
                'role' => Role::CLIENT,
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
    

    public function refreshToken(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');
        Log::info('Refreshing token with: ' . $refreshToken);
    
        $tokens = $this->tokenService->refreshToken($refreshToken);
    
        if ($tokens instanceof JsonResponse) {
            return $tokens; // Return error response if refresh failed
        }
    
        return SendResponse::jsonResponse([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => $tokens['expires_at'],
        ], 200, StatutEnum::SUCCESS, 'Token refreshed successfully');
    }
    
    public function logout(): JsonResponse
    {
        $user = Auth::user();

        $this->tokenService->revokeTokens($user->id);

        return SendResponse::jsonResponse(null, 200, StatutEnum::SUCCESS, 'Logout successful');
    }
}
