<?php

namespace App\Services\ShopData;

use App\Enums\Shop\ShopTypeEnum;
use App\Models\Shop;
use MennenOnline\Shopware5ApiConnector\Endpoints\Endpoint as Shopware5Endpoint;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as Shopware5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Endpoints\Endpoint as Shopware6Endpoint;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as Shopware6EndpointEnum;

class ShopDataSyncServiceEndpointLoader
{
    public function __invoke(Shop $shop)
    {
        return match ($shop->type) {
            ShopTypeEnum::SHOPWARE5->value => new Shopware5Endpoint(url: $shop->url, client_id: $shop->credentials->api_key, client_secret: $shop->credentials->api_secret),
            ShopTypeEnum::SHOPWARE6->value => new Shopware6Endpoint(url: $shop->url, client_id: $shop->credentials->api_key, client_secret: $shop->credentials->api_secret)
        };
    }

    public function getEndpointEnumCasesForShop(Shop $shop)
    {
        return match ($shop->type) {
            ShopTypeEnum::SHOPWARE5->value => Shopware5EndpointEnum::cases(),
            ShopTypeEnum::SHOPWARE6->value => Shopware6EndpointEnum::cases()
        };
    }
}
