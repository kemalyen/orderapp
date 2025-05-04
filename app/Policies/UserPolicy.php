<?php

namespace App\Policies;
 
use App\Models\User;
use Illuminate\Auth\Access\Response;


class UserPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('Portal Admin') || $user->hasRole('Account Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return ($user->hasRole('Portal Admin'))|| $user->hasRole('Account Admin')
            ? Response::allow()
            : Response::deny('You do not have access to view this resource.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $userModel): Response
    {
        return ($user->hasRole('Portal Admin'))|| $user->hasRole('Account Admin')
            ? Response::allow()
            : Response::deny('You do not have access to view this order.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return ($user->hasRole('Portal Admin') || $user->hasRole('Account Admin'))
            ? Response::allow()
            : Response::deny('You do not have access to create an order.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $userModel): Response
    {
        return ($user->hasRole('Portal Admin'))|| $user->hasRole('Account Admin')
            ? Response::allow()
            : Response::deny('You do not have access to update this account.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $userModel): bool
    {
        return ($user->hasRole('Portal Admin'))
            ?  true 
            :  false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $userModel): bool
    {
        return ($user->hasRole('Portal Admin'))
            ?  true 
            :  false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $userModel): bool
    {
        return ($user->hasRole('Portal Admin'))
            ?  true 
            :  false;
    }
}