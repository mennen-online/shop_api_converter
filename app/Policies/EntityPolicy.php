<?php

namespace App\Policies;

use App\Models\Entity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the entity can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list entities');
    }

    /**
     * Determine whether the entity can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function view(User $user, Entity $model)
    {
        return $user->hasPermissionTo('view entities');
    }

    /**
     * Determine whether the entity can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create entities');
    }

    /**
     * Determine whether the entity can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function update(User $user, Entity $model)
    {
        return $user->hasPermissionTo('update entities');
    }

    /**
     * Determine whether the entity can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function delete(User $user, Entity $model)
    {
        return $user->hasPermissionTo('delete entities');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete entities');
    }

    /**
     * Determine whether the entity can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function restore(User $user, Entity $model)
    {
        return false;
    }

    /**
     * Determine whether the entity can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Entity  $model
     * @return mixed
     */
    public function forceDelete(User $user, Entity $model)
    {
        return false;
    }
}
