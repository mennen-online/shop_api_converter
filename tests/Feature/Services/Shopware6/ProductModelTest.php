<?php

namespace Tests\Feature\Services\Shopware6;

use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use App\Services\ShopData\Shopware5SyncService\Models\Articles;
use App\Services\ShopData\Shopware5SyncService\Models\Categories;
use App\Services\ShopData\Shopware5SyncService\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum;
use Tests\TestCase;

class ProductModelTest extends TestCase
{
    use RefreshDatabase;

    protected Shop $shop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $user = User::first();

        $this->actingAs($user);

        $this->shop = Shop::factory()->shopware5()
            ->for($user)
            ->create([
                'name' => 'Test',
                'url' => env('SW5_CUSTOMER_URL', 'http://localhost'),
                'credentials' => [
                    'api_key' => env('SW5_CLIENT_ID', 'my-client-id'),
                    'api_secret' => env('SW5_CLIENT_SECRET', 'my-client-secret'),
                ],
            ]);

        SyncShopDataJob::dispatch($this->shop, new ShopDataSyncServiceEndpointLoader());
    }

    /**
     * @test
     */
    public function it_receives_the_articles_model_in_shopware5()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::ARTICLES->name)->first();

        $shopData = $entity->allShopData()->first();

        $this->assertInstanceOf(Articles::class, $shopData->content);
    }

    /**
     * @test
     */
    public function it_can_receive_category_from_product_model_in_shopware_5()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::ARTICLES->name)->first();

        $shopData = $entity->allShopData()->first();

        $collection = $shopData->content->categories($this->shop);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertInstanceOf(Categories::class, $collection->first());
    }

    /**
     * @test
     */
    public function it_can_receive_media_from_product_model_in_shopware5()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::ARTICLES->name)->first();

        $shopData = $entity->allShopData()
            ->inRandomOrder()
            ->first();

        $collection = $shopData->content->media($this->shop);

        $this->assertInstanceOf(Collection::class, $collection);

        if ($collection->first()) {
            $this->assertInstanceOf(Media::class, $collection->first());
        }
    }
}
