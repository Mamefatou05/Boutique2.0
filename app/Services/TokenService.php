<?php
namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class TokenService
{
    protected $tokenRepository;
    protected $refreshTokenRepository;

    public function __construct(TokenRepository $tokenRepository, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    public function generateTokens(User $user)
    {
        // Générer le token d'accès
        $accessToken = $user->createToken('Personal Access Token')->accessToken;
        $refreshToken = Str::random(64);
        $expiresAt = Carbon::now()->addDays(7);

        // Stocker le refresh token dans Redis
        Redis::set('refresh_token_'.$user->id, json_encode([
            'token' => $refreshToken,
            'expires_at' => $expiresAt->timestamp,
        ]));
        Redis::expireat('refresh_token_'.$user->id, $expiresAt->timestamp);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    public function refreshToken($refreshToken)
    {
        // Rechercher le token de rafraîchissement dans Redis
        $userId = $this->getUserIdByRefreshToken($refreshToken);
    
        if (!$userId) {
            return response()->json(['message' => 'Invalid refresh token'], 401);
        }
    
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Révoquer les anciens tokens
        $this->revokeTokensByUserId($userId);
    
        // Générer de nouveaux tokens
        return $this->generateTokens($user);
    }
    
    private function getUserIdByRefreshToken($refreshToken)
    {
        // Rechercher l'utilisateur associé au refresh token
        foreach (Redis::keys('refresh_token_*') as $key) {
            $storedToken = json_decode(Redis::get($key), true);
            if ($storedToken['token'] === $refreshToken) {
                return str_replace('refresh_token_', '', $key);
            }
        }
    
        return null;
    }
    
    private function revokeTokensByUserId($userId)
    {
        // Supprimer le refresh token du Redis
        Redis::del('refresh_token_'.$userId);
    
        // Révoquer tous les tokens d'accès de l'utilisateur
        $tokens = $this->tokenRepository->forUser($userId);
        foreach ($tokens as $token) {
            $this->tokenRepository->revokeAccessToken($token->id);
        }
    }

    public function revokeTokens($userId)
    {
        // Révoquer tous les tokens d'accès
        $this->revokeTokensByUserId($userId);

        // Révoquer le token de rafraîchissement
        Redis::del('refresh_token_'.$userId);

        return response()->json(['message' => 'Tokens revoked successfully']);
    }
}
