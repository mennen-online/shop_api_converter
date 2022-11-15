<?php

namespace App\Services\ShopData;

use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use Illuminate\Support\Collection;

interface ShopDataSyncServiceInterface
{
    public function __invoke(Shop $shop, ShopConnectorService $shopConnectorService, string $endpoint, Collection $collection);
}
