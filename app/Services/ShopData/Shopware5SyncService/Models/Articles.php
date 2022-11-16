<?php

namespace App\Services\ShopData\Shopware5SyncService\Models;

use App\Models\Shop;
use App\Services\ShopData\ShopDataBaseModel;
use Illuminate\Support\Collection;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum;

/**
 * @property array $categories
 * @property array $media
 * @property array $images
 */
class Articles extends ShopDataBaseModel
{
    public function categories(Shop $shop): Collection
    {
        return $this->getEntity($shop, EndpointEnum::CATEGORIES, collect($this->categories)->pluck('id'), 'content->id')->mapInto(Categories::class);
    }

    public function media(Shop $shop): Collection
    {
        return $this->getEntity($shop, EndpointEnum::MEDIA, collect($this->images)->pluck('id'), 'content->id');
    }
}
