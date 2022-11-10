<?php

namespace App\Services\ShopData\Shopware6SyncService;

use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceInterface;
use Illuminate\Support\Collection;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;

class Shopware6ShopDataSyncService implements ShopDataSyncServiceInterface
{
    public function __invoke(Shop $shop, ShopConnectorService $shopConnectorService, string $endpoint, Collection $collection) {
        $shopApiConnector = $shopConnectorService->getConnector($shop, collect(EndpointEnum::cases())->filter(
            function (EndpointEnum $endpointEnum) use ($endpoint) {
                if ($endpointEnum->name === $endpoint) {
                    return $endpoint;
                }
            })->first());

        $entity = $shop->entities()->whereName($endpoint)->firstOrCreate([
            'name' => $endpoint,
        ]);

        $element = $collection->first();

        if (!$element) {
            throw new ShopSyncFailedException("First Element for $endpoint is null", 419);
        }

        collect(get_object_vars($element))->each(function ($value, $key) use ($entity) {
            $entity->entityFields()->updateOrCreate([
                'name' => $key,
            ]);
        });

        $collection->each(function (object $element) use ($shopApiConnector, $shop, $entity) {
            $data = $shopApiConnector->getSingle($element->id)->data;
            $entity->allShopData()->create([
                'shop_id' => $shop->id,
                'content' => $data,
            ]);
        });
    }
}
