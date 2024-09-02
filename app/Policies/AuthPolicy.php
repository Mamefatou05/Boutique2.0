<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class AuthPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {

    }



    public function register(User $user): bool
    {
        return $user->role === Role::BOUTIQUIER;
    }
}
