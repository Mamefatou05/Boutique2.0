<?php
// app/Policies/ClientPolicy.php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut afficher les clients.
     */
    

    /**
     * Détermine si l'utilisateur peut créer un client.
     */
    public function store(User $user)
    {
        return $user->role->name === Role::BOUTIQUIER; 
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un client.
     */
    public function update(User $user, Client $client)
    {
        return $user->role->name ===  Role::BOUTIQUIER;
    }

    /**
     * Détermine si l'utilisateur peut supprimer un client.
     */
    public function delete(User $user, Client $client)
    {
        return $user->role->name ===  Role::BOUTIQUIER;
    }
    public function viewAny(User $user)
    {
        return $user->role->name ===  Role::BOUTIQUIER;
    }
    public function viewOne(User $user,  Client $client)
    {
        // Permet aux boutiquiers et aux clients de voir des articles spécifiques
        return in_array($user->role->name, [Role::BOUTIQUIER, Role::CLIENT])
            ? Response::allow()
            : Response::deny('You do not have permission to view this article.');
    }
    public function viewDette(User $user, Client $client)
    {
        return $user->role->name === Role::BOUTIQUIER || $user->id === $client->id;
    }
    
    public function viewUser(User $user, Client $client)
    {
        return $user->role->name === Role::BOUTIQUIER || $user->id === $client->id;
    }
    

    
}
