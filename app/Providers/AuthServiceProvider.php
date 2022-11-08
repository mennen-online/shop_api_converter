<?php

namespace App\Providers;

use App\Models\Endpoint;
use App\Models\Entity;
use App\Models\EntityField;
use App\Models\Shop;
use App\Models\ShopData;
use App\Models\User;
use App\Policies\EndpointPolicy;
use App\Policies\EntityFieldPolicy;
use App\Policies\EntityPolicy;
use App\Policies\ShopDataPolicy;
use App\Policies\ShopPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Endpoint::class => EndpointPolicy::class,
        EntityField::class => EntityFieldPolicy::class,
        Entity::class => EntityPolicy::class,
        ShopData::class => ShopDataPolicy::class,
        Shop::class => ShopPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        // Automatically finding the Policies
        Gate::guessPolicyNamesUsing(function ($modelClass) {
            return 'App\\Policies\\'.class_basename($modelClass).'Policy';
        });

        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permission checks using can()
        Gate::before(function ($user, $ability) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });
    }
}
