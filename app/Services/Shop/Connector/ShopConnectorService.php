<?php

namespace App\Services\Shop\Connector;

use App\Enums\Shop\ShopTypeEnum;
use App\Models\Shop;
use MennenOnline\Shopware5ApiConnector\Endpoints\Endpoint as Shopware5Endpoint;
use MennenOnline\Shopware6ApiConnector\Endpoints\Endpoint as Shopware6Endpoint;

class ShopConnectorService
{
    public function getConnector(Shop $shop, object $endpointEnum)
    {
        return match ($shop->type) {
            ShopTypeEnum::SHOPWARE6->value => new Shopware6Endpoint(
                url: $shop->url,
                client_id: $shop->credentials->api_key,
                client_secret: $shop->credentials->api_secret,
                endpoint: $endpointEnum
            ),
            ShopTypeEnum::SHOPWARE5->value => new Shopware5Endpoint(
                url: $shop->url,
                username: $shop->credentials->username,
                password: $shop->credentials->password,
                endpoint: $endpointEnum
            )
        };
    }
}
