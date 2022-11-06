<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EntityField;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntityFieldPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the entityField can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list entityfields');
    }

    /**
     * Determine whether the entityField can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function view(User $user, EntityField $model)
    {
        return $user->hasPermissionTo('view entityfields');
    }

    /**
     * Determine whether the entityField can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create entityfields');
    }

    /**
     * Determine whether the entityField can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function update(User $user, EntityField $model)
    {
        return $user->hasPermissionTo('update entityfields');
    }

    /**
     * Determine whether the entityField can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function delete(User $user, EntityField $model)
    {
        return $user->hasPermissionTo('delete entityfields');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete entityfields');
    }

    /**
     * Determine whether the entityField can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function restore(User $user, EntityField $model)
    {
        return false;
    }

    /**
     * Determine whether the entityField can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\EntityField  $model
     * @return mixed
     */
    public function forceDelete(User $user, EntityField $model)
    {
        return false;
    }
}
