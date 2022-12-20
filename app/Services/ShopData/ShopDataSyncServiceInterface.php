<?php

namespace App\Services\ShopData;

use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;

interface ShopDataSyncServiceInterface
{
    public function __invoke(Shop $shop, ShopConnectorService $shopConnectorService, string $endpoint, array $model);
}
