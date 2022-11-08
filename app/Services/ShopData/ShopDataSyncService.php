<?php

namespace App\Services\ShopData;

use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Shop;
use Illuminate\Support\Collection;
use MennenOnline\Shopware6ApiConnector\Shopware6ApiConnector;

class ShopDataSyncService
{
    public function __invoke(Shop $shop, Shopware6ApiConnector $shopware6ApiConnector, string $entityName, Collection $collection)
    {
        $entity = $shop->entities()->whereName($entityName)->firstOrCreate([
            'name' => $entityName,
        ]);

        $element = $collection->first();

        if (! $element) {
            throw new ShopSyncFailedException("First Element for $entityName is null", 419);
        }

        collect(get_object_vars($element))->each(function ($value, $key) use ($entity) {
            $entity->entityFields()->updateOrCreate([
                'name' => $key,
            ]);
        });

        $collection->each(function (object $element) use ($shopware6ApiConnector, $shop, $entity) {
            $data = $shopware6ApiConnector->getSingle($element->id)->data;
            $entity->allShopData()->create([
                'shop_id' => $shop->id,
                'content' => $data,
            ]);
        });
    }
}
