<?php

namespace App\Services\ShopData\Shopware6SyncService\Models;

use App\Models\Shop;
use MennenOnline\LaravelResponseModels\Models\BaseModel;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;

class Product extends BaseModel
{
    public function category(Shop $shop) {
        return $this->getEntity($shop, EndpointEnum::CATEGORY, $this->categoryId, 'id');
    }

    public function media(Shop $shop) {
        return $this->getEntity($shop, EndpointEnum::MEDIA, $this->mediaId, 'id');
    }

    public function price(Shop $shop) {
        return $this->getEntity($shop, EndpointEnum::PRODUCT_PRICE, $this->id, 'id');
    }

    public function parent(Shop $shop) {
        return $this->getEntity($shop, EndpointEnum::PRODUCT, $this->id, 'id');
    }

    private function getEntity(Shop $shop, EndpointEnum $endpointEnum, int|string $id, string $field) {
        return $shop->allShopData()->whereHas(
            'entity', function($query) use($endpointEnum) {
                return $query->whereName($endpointEnum->name);
        }
        )->where(str('content->')->append($field)->toString(), $id)->get();
    }
}