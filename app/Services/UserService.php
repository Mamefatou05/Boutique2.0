<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserService
{
    public function createUser(array $userData): User
    {
        $validator = Validator::make($userData, [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'login' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $userData['password'] = bcrypt($userData['password']);

        return User::create($userData);
    }
}
