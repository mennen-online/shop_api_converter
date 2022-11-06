<?php

namespace Tests\Feature\Services;

use App\Enums\Shop\ShopStatusEnum;
use App\Exceptions\Shop\ShopSyncFailedException;
use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\Shop\ShopSyncFailedNotification;
use App\Notifications\Shop\ShopSyncFinishedNotification;
use App\Notifications\Shop\ShopSyncQueuedNotification;
use App\Observers\ShopObserver;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use MennenOnline\Shopware6ApiConnector\Shopware6ApiConnector;
use Tests\TestCase;

class ShopDataSyncServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_sync_shop_data() {
        Notification::fake();

        $user = User::first();

        $shopObserverMock = $this->mock(ShopObserver::class);

        $shopObserverMock->shouldReceive('created')->once();

        $shopObserverMock->shouldReceive('updated')->times(1);

        App::instance(ShopObserver::class, $shopObserverMock);

        $shop = Shop::factory()
            ->for($user)
            ->create([
                'name' => 'Test',
                'url' => env('SW6_CUSTOMER_URL'),
                'credentials' => [
                    'api_key' => env('SW6_CLIENT_ID'),
                    'api_secret' => env('SW6_CLIENT_SECRET')
                ]
            ]);

        SyncShopDataJob::dispatch($shop);

        $categoryEndpoint = Shopware6ApiConnector::category(
            url: $shop->url,
            client_id: $shop->credentials->api_key,
            client_secret: $shop->credentials->api_secret
        );

        $categories = collect($categoryEndpoint->getAll(10)->data);

        $categoryEntity = $shop->entities()->whereName('category')->first();

        $this->assertNotNull($categoryEntity);

        $this->assertSame(10, $categories->count());

        $categories->each(function(object $category) use($shop, $categoryEntity) {
            $this->assertModelExists($shop->allShopData()->where('entity_id', $categoryEntity->id)->where('content->id', $category->id)->first());
        });

        $productEndpoint = Shopware6ApiConnector::product(
            client: $categoryEndpoint->getClient()
        );

        $products = collect($productEndpoint->getAll(10)->data);

        $productEntity = $shop->entities()->whereName('product')->first();

        $this->assertNotNull($productEntity);

        $this->assertSame(10, $products->count());

        $this->assertGreaterThan(0, $products->count());

        $products->each(function(object $product) use($shop, $productEntity) {
            $this->assertModelExists($shop->allShopData()->where('entity_id', $productEntity->id)->where('content->id', $product->id)->first());
        });
    }

    /**
     * @test
     */
    public function it_throws_expected_exception_sends_notification_and_updates_database() {
        $user = User::first();

        $shop = Shop::factory()
            ->for($user)
            ->create([
                'name' => 'Failing',
                'url' => 'http://localhost'
            ]);

        Notification::fake();

        try {
            SyncShopDataJob::dispatchSync($shop);
        }catch(Exception $exception) {
            $this->assertTrue( $exception instanceof ShopSyncFailedException);

            Notification::assertSentTo($user, ShopSyncFailedNotification::class);

            $shop->refresh();

            $this->assertSame($shop->status, ShopStatusEnum::FAILED->value);
        }
    }
}
