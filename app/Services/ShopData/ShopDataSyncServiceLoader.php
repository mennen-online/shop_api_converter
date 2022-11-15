<?php

namespace App\Services\ShopData;

use App\Models\Shop;

class ShopDataSyncServiceLoader
{
    public function __invoke(Shop $shop): ShopDataSyncServiceInterface
    {
        $namespace = str($shop->type)->camel()->ucfirst()->append('SyncService')->toString();
        $syncService = str($shop->type)->camel()->ucfirst()->append('ShopDataSyncService')->toString();
        $className = 'App\\Services\\ShopData\\'.$namespace.'\\'.$syncService;
        if (class_exists($className)) {
            return new $className($shop);
        }
    }
}
