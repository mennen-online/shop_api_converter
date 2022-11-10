<?php

namespace App\Providers;

use App\Models\Shop;
use App\Observers\ShopObserver;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use App\Services\ShopData\ShopDataSyncServiceLoader;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ShopDataSyncServiceLoader::class, function() {
            return new ShopDataSyncServiceLoader();
        });

        $this->app->singleton(ShopDataSyncServiceEndpointLoader::class, function() {
            return new ShopDataSyncServiceEndpointLoader();
        });

        Shop::observe(ShopObserver::class);
    }
}
