<?php
namespace App\Http\Controllers;

use App\Enums\StatutEnum;
use App\Helpers\SendResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // Ajouter un log pour vérifier les données reçues
        Log::info('Login Request Data:', $request->all());
    
        $credentials = $request->validated();
    
        try {
            // Tentative de génération du token
            if (!$token = JWTAuth::attempt($credentials)) {
                return SendResponse::jsonResponse(null, 401, StatutEnum::FAILURE, 'Invalid credentials');
            }
    
            // Récupérer l'utilisateur authentifié
            $user = Auth::user();
    
            // Créer un nouveau token avec des revendications personnalisées
            $token = JWTAuth::fromUser($user, [
                'role' => $user->role,
                'user_id' => $user->id,
            ]);
    
        } catch (JWTException $e) {
            // En cas d'échec lors de la création du token
            Log::error('JWT Exception:', ['exception' => $e->getMessage()]);
            return $this->jsonResponse(null, 500, 'Could not create token');
        }
    
        return SendResponse::jsonResponse([
            'user' => $user,
            'token' => $token,
        ], 200, StatutEnum::SUCCESS, 'Login successful');
    }
    

    public function refreshToken(Request $request): JsonResponse
    {
        $token = $request->input('refresh_token');

        try {
            $newToken = JWTAuth::refresh($token);
        } catch (JWTException $e) {
            return $this->jsonResponse(null, 500, 'Could not refresh token');
        }

        return SendResponse::jsonResponse(['token' => $newToken], 200, StatutEnum::SUCCESS, 'Token refreshed successfully');
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
        } catch (JWTException $e) {
            return $this->jsonResponse(null, 500, 'Could not log out');
        }

        return SendResponse::jsonResponse(null, 200, StatutEnum::SUCCESS, 'Logout successful');
    }

   
}
