<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\TokenService;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    protected $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    public function authenticate(array $credentials)
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $tokens = $this->tokenService->generateTokens($user);
            return [
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'expires_at' => $tokens['expires_at'],
            ];
        }
        return null;
    }

    public function logout()
    {
        $user = Auth::user();
        return $this->tokenService->revokeTokens($user->id);
    }

    public function refreshToken(string $refreshToken)
    {
        return $this->tokenService->refreshToken($refreshToken);
    }
}