<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Détermine si l'utilisateur peut effectuer une action.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    private function isAdmin(User $user): bool
    {
        return $user->role->name === Role::ADMIN;
    }

    /**
     * Détermine si l'utilisateur peut voir tous les utilisateurs.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Détermine si l'utilisateur peut voir un utilisateur spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Détermine si l'utilisateur peut créer un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Détermine si l'utilisateur peut mettre à jour un utilisateur spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    /**
     * Détermine si l'utilisateur peut supprimer un utilisateur spécifique.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return $this->isAdmin($user);
    }

    public function register(User $user): bool
    {
        return $user->role->name === Role::BOUTIQUIER;
    }
}
