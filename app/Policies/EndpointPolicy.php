<?php

namespace App\Policies;

use App\Models\Endpoint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EndpointPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the endpoint can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list endpoints');
    }

    /**
     * Determine whether the endpoint can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function view(User $user, Endpoint $model)
    {
        return $user->hasPermissionTo('view endpoints');
    }

    /**
     * Determine whether the endpoint can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create endpoints');
    }

    /**
     * Determine whether the endpoint can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function update(User $user, Endpoint $model)
    {
        return $user->hasPermissionTo('update endpoints');
    }

    /**
     * Determine whether the endpoint can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function delete(User $user, Endpoint $model)
    {
        return $user->hasPermissionTo('delete endpoints');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete endpoints');
    }

    /**
     * Determine whether the endpoint can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function restore(User $user, Endpoint $model)
    {
        return false;
    }

    /**
     * Determine whether the endpoint can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\Endpoint  $model
     * @return mixed
     */
    public function forceDelete(User $user, Endpoint $model)
    {
        return false;
    }
}
