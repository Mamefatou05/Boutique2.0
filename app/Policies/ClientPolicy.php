<?php
// app/Policies/ClientPolicy.php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut afficher les clients.
     */
    public function view(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER'; 
    }

    /**
     * Détermine si l'utilisateur peut créer un client.
     */
    public function create(User $user)
    {
        return $user->role->name === 'BOUTIQUIER'; 
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un client.
     */
    public function update(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER';
    }

    /**
     * Détermine si l'utilisateur peut supprimer un client.
     */
    public function delete(User $user, Client $client)
    {
        return $user->role->name === 'BOUTIQUIER';
    }
    public function viewAny(User $user)
    {
        return $user->role->name === 'BOUTIQUIER';
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
