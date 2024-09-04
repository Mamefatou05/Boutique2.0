<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use App\Models\User;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;
use Illuminate\Support\Facades\Log;

class TokenService
{
    protected $tokenRepository;
    protected $refreshTokenRepository;

    public function __construct(TokenRepository $tokenRepository, RefreshTokenRepository $refreshTokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Generate access and refresh tokens for a user.
     *
     * @param User $user
     * @return array
     */
      public function generateTokens(User $user)
    {
        // Définir l'expiration du token
        $expiresAt = Carbon::now()->addSeconds(config('auth.personal_access_token_expiration', 60));
    
        // Générer le token d'accès avec les informations personnalisées
// Generate the access token with custom claims
    $tokenResult = $user->createToken('Personal Access Token');     
       $accessToken = $tokenResult->accessToken;
    
        Log::info('Token generated', [
            'token' => $accessToken,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    
        // Stocker le refresh token dans Redis
        $refreshToken = Str::random(64);
        Redis::set('refresh_token_' . $user->id, json_encode([
            'token' => $refreshToken,
            'expires_at' => $expiresAt->timestamp,
        ]));
        Redis::expireat('refresh_token_' . $user->id, $expiresAt->timestamp);
    
        return [
            'access_token' => $accessToken, // Retourner la chaîne du token
            'access_token' => $accessToken, // Retourner la chaîne du token
            'refresh_token' => $refreshToken,
            'expires_at' => $expiresAt->toDateTimeString(),
        ];
    }

    /**
     * Refresh the access token using a refresh token.
     *
     * @param string $refreshToken
     * @return array
     */
    public function refreshToken(string $refreshToken)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error refreshing token: ' . $e->getMessage());
            return response()->json(['message' => 'Unable to refresh token'], 500);
        }
    }
    
    /**
     * Get user ID by refresh token.
     *
     * @param string $refreshToken
     * @return int|null
     */
    private function getUserIdByRefreshToken(string $refreshToken)
    {
        // Debugging output to ensure the correct token is received
        Log::info('Received refresh token: ' . $refreshToken);
        
        // Retrieve all keys that match the pattern
        $redis = Redis::connection();
        $keys = Redis::keys('laravel_database_refresh_token_*');
      
                dd($keys);
        
        Log::info('Keys found in Redis: ' . print_r($keys, true));
        
        foreach ($keys as $key) {
            // Get the stored token JSON string
            $storedTokenJson = Redis::get($key);
            Log::info('Stored token for key ' . $key . ': ' . $storedTokenJson);
    
            // Decode the JSON string
            $storedToken = json_decode($storedTokenJson, true);
            
            // Debugging output to see the decoded array
            Log::info('Decoded stored token: ' . print_r($storedToken, true));
    
            // Compare the stored token with the given refresh token
            if (is_array($storedToken) && isset($storedToken['token']) && $storedToken['token'] === $refreshToken) {
                $userId = str_replace('laravel_database_refresh_token_', '', $key);
                Log::info('Token matched for user ID: ' . $userId);
                return (int)$userId;
            }
        }
        
        Log::error('Refresh token not found');
        return null;
    }
    
    

    
    /**
     * Revoke all tokens by user ID.
     *
     * @param int $userId
     */
    private function revokeTokensByUserId(int $userId)
    {
        try {
            // Supprimer le refresh token de Redis
            Redis::del('refresh_token_' . $userId);
    
            // Révoquer tous les tokens d'accès
            $tokens = $this->tokenRepository->forUser($userId);
            foreach ($tokens as $token) {
                $this->tokenRepository->revokeAccessToken($token->id);
            }
        } catch (\Exception $e) {
            Log::error('Error revoking tokens: ' . $e->getMessage());
        }
    }

    /**
     * Revoke all tokens for a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeTokens(int $userId)
    {
        try {
            $this->revokeTokensByUserId($userId);
            return response()->json(['message' => 'Tokens revoked successfully']);
        } catch (\Exception $e) {
            Log::error('Error revoking tokens: ' . $e->getMessage());
            return response()->json(['message' => 'Unable to revoke tokens'], 500);
        }
    }
}
