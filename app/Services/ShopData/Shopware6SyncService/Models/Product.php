<?php

namespace App\Services\ShopData\Shopware6SyncService\Models;

use App\Models\Shop;
use App\Services\ShopData\ShopDataBaseModel;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;

class Product extends ShopDataBaseModel
{
    public function category(Shop $shop)
    {
        return $this->getEntity($shop, EndpointEnum::CATEGORY, $this->categoryId, 'id');
    }

    public function media(Shop $shop)
    {
        return $this->getEntity($shop, EndpointEnum::MEDIA, $this->mediaId, 'id');
    }

    public function price(Shop $shop)
    {
        return $this->getEntity($shop, EndpointEnum::PRODUCT_PRICE, $this->id, 'id');
    }

    public function parent(Shop $shop)
    {
        return $this->getEntity($shop, EndpointEnum::PRODUCT, $this->id, 'id');
    }
}
