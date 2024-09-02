<?php
namespace App\Policies;

use App\Enums\Role;
use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    public function viewAny(User $user)
    {
        // Permet aux boutiquiers et aux clients de voir tous les articles
        return in_array($user->role->name, [Role::BOUTIQUIER, Role::CLIENT])
            ? Response::allow()
            : Response::deny('You do not have permission to view articles.');
    }

    public function view(User $user, Article $article)
    {
        // Permet aux boutiquiers et aux clients de voir des articles spécifiques
        return in_array($user->role->name, [Role::BOUTIQUIER, Role::CLIENT])
            ? Response::allow()
            : Response::deny('You do not have permission to view this article.');
    }

    public function create(User $user)
    {
        // Permet uniquement aux boutiquiers de créer des articles
        return $user->role->name === Role::BOUTIQUIER
            ? Response::allow()
            : Response::deny('You do not have permission to create articles.');
    }

    public function update(User $user, Article $article)
    {
        // Permet uniquement aux boutiquiers de mettre à jour des articles
        return $user->role->name === Role::BOUTIQUIER
            ? Response::allow()
            : Response::deny('You do not have permission to update this article.');
    }

    public function delete(User $user, Article $article)
    {
        // Permet uniquement aux boutiquiers de supprimer des articles
        return $user->role->name === Role::BOUTIQUIER
            ? Response::allow()
            : Response::deny('You do not have permission to delete this article.');
    }


    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Article $article)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Article $article)
    {
        //
    }
}
