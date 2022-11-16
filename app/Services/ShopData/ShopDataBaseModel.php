<?php

namespace App\Services\ShopData;

use App\Models\Shop;
use Illuminate\Support\Collection;
use MennenOnline\LaravelResponseModels\Models\BaseModel;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as Shopware5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as Shopware6EndpointEnum;

abstract class ShopDataBaseModel extends BaseModel
{
    protected function getEntity(Shop $shop, Shopware5EndpointEnum|Shopware6EndpointEnum $endpointEnum, Collection|int|string $id, string $field): \Illuminate\Database\Eloquent\Collection
    {
        $query = $shop->allShopData()->whereHas(
            'entity', function ($query) use ($endpointEnum) {
                return $query->whereName($endpointEnum->name);
            }
        );

        $query = $id instanceof Collection
            ? $query->whereIn($field, $id->toArray())
            : $query->where($field, $id);

        return $query->get();
    }
}
