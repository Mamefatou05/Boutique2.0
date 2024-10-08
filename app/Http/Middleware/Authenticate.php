<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Check if the request expects JSON response
        if ($request->expectsJson()) {
            return null; // No redirection for JSON requests
        }

        // Default redirection to login page for non-JSON requests
        return route('login');
    }
}

