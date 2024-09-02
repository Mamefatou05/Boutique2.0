<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserService
{
  
    
        public function createUser(array $userData): User
        {    
            // Créez et retournez l'utilisateur
            return User::create($userData);
        }
    
}
