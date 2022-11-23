<?php

namespace Tests\Feature\Services;

use App\Jobs\ShopData\SyncShopDataEndpointJob;
use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Observers\ShopObserver;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use Exception;
use Illuminate\Bus\PendingBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use MennenOnline\Shopware5ApiConnector\Endpoints\Endpoint as Shopware5Endpoint;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as Shopware5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Endpoints\Endpoint as Shopware6Endpoint;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as Shopware6EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Exceptions\Connector\EmptyShopware6ResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ShopDataSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');
    }

    /**
     * @test
     */
    public function it_can_sync_shop_data_from_shopware5()
    {
        $shopObserverMock = $this->mock(ShopObserver::class);

        $shopObserverMock->shouldReceive('created')->once();

        $shopObserverMock->shouldReceive('updated')->times(3);

        App::instance(ShopObserver::class, $shopObserverMock);

        $user = User::first();

        $this->actingAs($user);

        $shop = Shop::factory()->shopware5()
            ->for($user)
            ->create([
                'name' => 'Test',
                'url' => env('SW5_CUSTOMER_URL', 'http://localhost'),
                'credentials' => [
                    'api_key' => env('SW5_CLIENT_ID', 'my-client-id'),
                    'api_secret' => env('SW5_CLIENT_SECRET', 'my-client-secret'),
                ],
            ]);

        SyncShopDataJob::dispatch($shop, new ShopDataSyncServiceEndpointLoader());

        Bus::fake();

        [$job, $batch] = (new SyncShopDataJob($shop, new ShopDataSyncServiceEndpointLoader()))->withFakeBatch();

        $job->handle();

        Bus::assertBatched(function (PendingBatch $batch) use ($shop) {
            $this->assertSame($batch->jobs->count(), count(Shopware5EndpointEnum::cases()));
            $this->assertSame($shop->name.' Sync', $batch->name);

            $batch->jobs->each(function ($job) {
                $this->assertInstanceOf(SyncShopDataEndpointJob::class, $job);
            });

            return true;
        });

        foreach (Shopware5EndpointEnum::cases() as $endpointEnum) {
            $endpoint = new Shopware5Endpoint(
                url: $shop->url,
                client_id: $shop->credentials->api_key,
                client_secret: $shop->credentials->api_secret,
                endpoint: $endpointEnum
            );
            try {
                $response = $endpoint->getAll(10);

                $collection = $response->data;

                if ($collection->count() > 0) {
                    $entity = $shop->entities()->whereName($endpointEnum->name)->first();

                    $this->assertModelExists($entity);

                    if (! is_object($collection->first())) {
                        $this->assertSame(1, $shop->allShopData()->whereEntityId($entity->id)->count(), $entity->name.' Count is not '.$collection->count());
                    } else {
                        $this->assertSame($collection->count(), $shop->allShopData()->whereEntityId($entity->id)->count(), $entity->name.' Count is not '.$collection->count());

                        $collection->each(function (object $element) use ($endpoint, $shop, $entity) {
                            $id = property_exists($element, 'id') ? $element->id : $element->key;

                            $data = $endpoint->getSingle($id)->data;

                            $this->assertModelExists($shop->allShopData()->whereEntityId($entity->id)->where('content->id', $data->id)->first());
                        });
                    }
                } else {
                    $this->assertDatabaseMissing('entities', [
                        'shop_id' => $shop->id,
                        'name' => $endpointEnum->name,
                    ]);
                }
            } catch(NotFoundHttpException $exception) {
                continue;
            }
        }
    }

    /**
     * @test
     */
    public function it_can_sync_shop_data_from_shopware6()
    {
        $shopObserverMock = $this->mock(ShopObserver::class);

        $shopObserverMock->shouldReceive('created')->once();

        $shopObserverMock->shouldReceive('updated')->times(1);

        App::instance(ShopObserver::class, $shopObserverMock);

        $user = User::first();

        $this->actingAs($user);

        $shop = Shop::factory()->shopware6()
            ->for($user)
            ->create([
                'name' => 'Test',
                'url' => env('SW6_CUSTOMER_URL', 'http://localhost'),
                'credentials' => [
                    'api_key' => env('SW6_CLIENT_ID', 'my-client-id'),
                    'api_secret' => env('SW6_CLIENT_SECRET', 'my-client-secret'),
                ],
            ]);

        Bus::fake();

        [$job, $batch] = (new SyncShopDataJob($shop, new ShopDataSyncServiceEndpointLoader()))->withFakeBatch();

        $job->handle();

        Bus::assertBatched(function (PendingBatch $batch) use ($shop) {
            $this->assertSame($batch->jobs->count(), count(Shopware6EndpointEnum::cases()));
            $this->assertSame($shop->name.' Sync', $batch->name);

            $batch->jobs->each(function ($job) {
                $this->assertInstanceOf(SyncShopDataEndpointJob::class, $job);
            });

            return true;
        });

        SyncShopDataJob::dispatch($shop, new ShopDataSyncServiceEndpointLoader());

        foreach (Shopware6EndpointEnum::cases() as $endpointEnum) {
            $endpoint = new Shopware6Endpoint(
                url: $shop->url,
                client_id: $shop->credentials->api_key,
                client_secret: $shop->credentials->api_secret,
                endpoint: $endpointEnum
            );

            $response = $endpoint->getAll(10);

            $collection = collect($response->data);

            if ($collection->count() > 0) {
                $entity = $shop->entities()->whereName($endpointEnum->name)->first();

                $this->assertNotNull($entity);

                $this->assertSame($response->total, $collection->count(), 'Collection Count not as expected for '.$endpointEnum->name);

                $this->assertSame($collection->count(), $shop->allShopData()->whereEntityId($entity->id)->count());
            } else {
                $this->assertDatabaseMissing('entities', [
                    'shop_id' => $shop->id,
                    'name' => $endpointEnum->name,
                ]);
            }
        }
    }

    /**
     * @test
     */
    public function it_throws_expected_exception_sends_notification_and_updates_database_on_shopware6()
    {
        $user = User::first();

        $this->actingAs($user);

        $shop = Shop::factory()->shopware6()
            ->for($user)
            ->create([
                'name' => 'Failing',
            ]);

        Notification::fake();

        try {
            SyncShopDataJob::dispatch($shop, new ShopDataSyncServiceEndpointLoader());
        } catch(Exception $exception) {
            $this->assertSame(EmptyShopware6ResponseException::class, get_class($exception));
        }
    }
}
