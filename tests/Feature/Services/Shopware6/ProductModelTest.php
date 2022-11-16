<?php

namespace Tests\Feature\Services\Shopware6;

use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use App\Services\ShopData\Shopware6SyncService\Models\Categories;
use App\Services\ShopData\Shopware6SyncService\Models\Images;
use App\Services\ShopData\Shopware6SyncService\Models\Products;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;
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

        $this->shop = Shop::factory()->shopware6()
            ->for($user)
            ->create([
                'name' => 'Test',
                'url' => env('SW6_CUSTOMER_URL', 'http://localhost'),
                'credentials' => [
                    'api_key' => env('SW6_CLIENT_ID', 'my-client-id'),
                    'api_secret' => env('SW6_CLIENT_SECRET', 'my-client-secret'),
                ],
            ]);

        SyncShopDataJob::dispatch($this->shop, new ShopDataSyncServiceEndpointLoader());
    }

    /**
     * @test
     */
    public function it_receives_the_articles_model_in_shopware5()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::PRODUCT->name)->first();

        if (! $entity) {
            $this->markTestSkipped('Shopware 6 seems to be empty');
        }

        $shopData = $entity->allShopData()->first();

        $this->assertInstanceOf(Products::class, $shopData->content);
    }

    /**
     * @test
     */
    public function it_can_receive_category_from_product_model_in_shopware6()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::PRODUCT->name)->first();

        if (! $entity) {
            $this->markTestSkipped('Shopware 6 seems to be empty');
        }

        $shopData = $entity->allShopData()->first();

        $collection = $shopData->content->categories($this->shop);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertInstanceOf(Categories::class, $collection->first());
    }

    /**
     * @test
     */
    public function it_can_receive_media_from_product_model_in_shopware6()
    {
        $entity = $this->shop->entities()->whereName(EndpointEnum::PRODUCT->name)->first();

        if (! $entity) {
            $this->markTestSkipped('Shopware 6 seems to be empty');
        }

        $shopData = $entity->allShopData()
            ->inRandomOrder()
            ->first();

        $collection = $shopData->content->images($this->shop);

        $this->assertInstanceOf(Collection::class, $collection);

        if ($collection->first()) {
            $this->assertInstanceOf(Images::class, $collection->first());
        }
    }
}
