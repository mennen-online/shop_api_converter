<?php

namespace App\Services\ShopData\Shopware5SyncService;

use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Shopware5ShopDataSyncService implements ShopDataSyncServiceInterface
{
    public function __invoke(Shop $shop, ShopConnectorService $shopConnectorService, string $endpoint, array $model)
    {
        $shopApiConnector = $shopConnectorService->getConnector($shop, collect(EndpointEnum::cases())->filter(
            function (EndpointEnum $endpointEnum) use ($endpoint) {
                if ($endpointEnum->name === $endpoint) {
                    return $endpoint;
                }
            })->first());

        $entity = $shop->entities()->whereName($endpoint)->firstOrCreate([
            'name' => $endpoint,
        ]);

        collect(array_keys($model))->each(function ($value, $key) use ($entity) {
            $entity->entityFields()->updateOrCreate([
                'name' => $key,
            ]);
        });

        $attributes = $model;

        $id = !Arr::has($attributes, 'id') ? Arr::get($attributes, 'key') : Arr::get($attributes, 'id');

        try {
            $data = $shopApiConnector->getSingle($id)->data;
        } catch (NotFoundHttpException $exception) {
            Log::warning("Fetching Single $endpoint Resource with ID $id failed - use Collection Data");

            $data = $model;
        }

        $entity->allShopData()->updateOrCreate([
            'shop_id' => $shop->id,
            'content' => $data,
        ]);
    }
}
