<?php

namespace App\Policies;

use App\Models\ShopData;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShopDataPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the shopData can view any models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('list allshopdata');
    }

    /**
     * Determine whether the shopData can view the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function view(User $user, ShopData $model)
    {
        return $user->hasPermissionTo('view allshopdata');
    }

    /**
     * Determine whether the shopData can create models.
     *
     * @param  App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('create allshopdata');
    }

    /**
     * Determine whether the shopData can update the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function update(User $user, ShopData $model)
    {
        return $user->hasPermissionTo('update allshopdata');
    }

    /**
     * Determine whether the shopData can delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function delete(User $user, ShopData $model)
    {
        return $user->hasPermissionTo('delete allshopdata');
    }

    /**
     * Determine whether the user can delete multiple instances of the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermissionTo('delete allshopdata');
    }

    /**
     * Determine whether the shopData can restore the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function restore(User $user, ShopData $model)
    {
        return false;
    }

    /**
     * Determine whether the shopData can permanently delete the model.
     *
     * @param  App\Models\User  $user
     * @param  App\Models\ShopData  $model
     * @return mixed
     */
    public function forceDelete(User $user, ShopData $model)
    {
        return false;
    }
}
