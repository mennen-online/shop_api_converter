<?php

namespace App\Services\ShopData\Shopware5SyncService;

use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Shopware5ShopDataSyncService implements ShopDataSyncServiceInterface
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

        if(!is_object($element)) {
            $collection->each(function($value, $key) use($entity) {
                $entity->entityFields()->updateOrCreate([
                    'name' => $key
                ]);
            });

            $entity->allShopData()->updateOrCreate([
                'shop_id' => $shop->id,
                'content' => $collection->toJson()
            ]);
        } else {
            collect(get_object_vars($element))->each(function ($value, $key) use ($entity) {
                $entity->entityFields()->updateOrCreate([
                    'name' => $key,
                ]);
            });

            $collection->each(function (object $element) use ($shopApiConnector, $shop, $entity, $endpoint) {
                $id = !property_exists($element, 'id') ? $element->key : $element->id;

                try {
                    $data = $shopApiConnector->getSingle($id)->data;
                }catch(NotFoundHttpException $exception) {
                    Log::warning("Fetching Single $endpoint Resource with ID $id failed - use Collection Data");

                    $data = $element;
                }

                $entity->allShopData()->create([
                    'shop_id' => $shop->id,
                    'content' => $data,
                ]);
            });
        }
    }
}