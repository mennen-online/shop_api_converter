<?php

namespace Tests\Feature\Services;

use App\Enums\Shop\ShopStatusEnum;
use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\Shop\ShopSyncFailedNotification;
use App\Observers\ShopObserver;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use MennenOnline\Shopware5ApiConnector\Endpoints\Endpoint as Shopware5Endpoint;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as Shopware5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Endpoints\Endpoint as Shopware6Endpoint;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as Shopware6EndpointEnum;
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
        Notification::fake();

        $shopObserverMock = $this->mock(ShopObserver::class);

        $shopObserverMock->shouldReceive('created')->once();

        $shopObserverMock->shouldReceive('updated')->times(1);

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
        Notification::fake();

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

        foreach (Shopware6EndpointEnum::cases() as $case) {
            Http::fake([
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl(Shopware6EndpointEnum::OAUTH_TOKEN) => Http::response([
                    'token_type' => 'Bearer',
                    'expires_in' => 600,
                    'access_token' => 'my-access-token',
                ]),
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl($case).'?limit=10' => Http::response(
                    '{"total":9,"data":[{"afterCategoryId":null,"parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":3,"mediaId":null,"name":"Bakery products","breadcrumb":["Catalogue #1","Food","Bakery products"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"19ca405790ff4f07aac8c599d4317868","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Bakery products"],"name":"Bakery products","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"19ca405790ff4f07aac8c599d4317868","customFields":null,"apiAlias":"category"},{"afterCategoryId":"8de9b484c54f441c894774e5f57e485c","parentId":"a515ae260223466f8e37471d279e6406","autoIncrement":8,"mediaId":null,"name":"Men","breadcrumb":["Catalogue #1","Clothing","Men"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|a515ae260223466f8e37471d279e6406|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"2185182cbbd4462ea844abeb2a438b33","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing","Men"],"name":"Men","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.045+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"2185182cbbd4462ea844abeb2a438b33","customFields":null,"apiAlias":"category"},{"afterCategoryId":"a515ae260223466f8e37471d279e6406","parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":9,"mediaId":null,"name":"Free time & electronics","breadcrumb":["Catalogue #1","Free time & electronics"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"251448b91bc742de85643f5fccd89051","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Free time & electronics"],"name":"Free time & electronics","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.045+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"251448b91bc742de85643f5fccd89051","customFields":null,"apiAlias":"category"},{"afterCategoryId":"19ca405790ff4f07aac8c599d4317868","parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":4,"mediaId":null,"name":"Fish","breadcrumb":["Catalogue #1","Food","Fish"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"48f97f432fd041388b2630184139cf0e","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Fish"],"name":"Fish","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"48f97f432fd041388b2630184139cf0e","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":2,"mediaId":null,"name":"Food","breadcrumb":["Catalogue #1","Food"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":false,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"77b959cf66de4c1590c7f9b7da3982f3","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food"],"name":"Food","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.042+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"77b959cf66de4c1590c7f9b7da3982f3","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":"a515ae260223466f8e37471d279e6406","autoIncrement":7,"mediaId":null,"name":"Women","breadcrumb":["Catalogue #1","Clothing","Women"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|a515ae260223466f8e37471d279e6406|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"8de9b484c54f441c894774e5f57e485c","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing","Women"],"name":"Women","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.044+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"8de9b484c54f441c894774e5f57e485c","customFields":null,"apiAlias":"category"},{"afterCategoryId":"77b959cf66de4c1590c7f9b7da3982f3","parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":6,"mediaId":null,"name":"Clothing","breadcrumb":["Catalogue #1","Clothing"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":true,"childCount":2,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"a515ae260223466f8e37471d279e6406","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing"],"name":"Clothing","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.044+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"a515ae260223466f8e37471d279e6406","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":null,"autoIncrement":1,"mediaId":null,"name":"Catalogue #1","breadcrumb":["Catalogue #1"],"path":null,"level":1,"active":true,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"695477e02ef643e5a016b83ed4cdf63a","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"a74cfacc8ce2416f8ee843ccc931c3fe","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1"],"name":"Catalogue #1","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:23:16.661+00:00","updatedAt":"2022-03-03T06:28:09.042+00:00","extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"a74cfacc8ce2416f8ee843ccc931c3fe","customFields":null,"apiAlias":"category"},{"afterCategoryId":"48f97f432fd041388b2630184139cf0e","parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":5,"mediaId":null,"name":"Sweets","breadcrumb":["Catalogue #1","Food","Sweets"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"bb22b05bff9140f3808b1cff975b75eb","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Sweets"],"name":"Sweets","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"bb22b05bff9140f3808b1cff975b75eb","customFields":null,"apiAlias":"category"}],"success":null,"errors":null}'
                ),
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl($case).'/*' => Http::response(
                    '{"total":null,"data":{"afterCategoryId":null,"parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":2,"mediaId":null,"name":"Food","breadcrumb":["Catalogue #1","Food"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":false,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"77b959cf66de4c1590c7f9b7da3982f3","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food"],"name":"Food","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.042+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"19ca405790ff4f07aac8c599d4317868","customFields":null,"apiAlias":"category"},"success":null,"errors":null}'
                ),
            ]);
        }

        SyncShopDataJob::dispatch($shop, new ShopDataSyncServiceEndpointLoader());

        foreach (Shopware6EndpointEnum::cases() as $endpointEnum) {
            Http::fake([
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl(Shopware6EndpointEnum::OAUTH_TOKEN) => Http::response([
                    'token_type' => 'Bearer',
                    'expires_in' => 600,
                    'access_token' => 'my-access-token',
                ]),
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl($case).'?limit=10' => Http::response(
                    '{"total":9,"data":[{"afterCategoryId":null,"parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":3,"mediaId":null,"name":"Bakery products","breadcrumb":["Catalogue #1","Food","Bakery products"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"19ca405790ff4f07aac8c599d4317868","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Bakery products"],"name":"Bakery products","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"19ca405790ff4f07aac8c599d4317868","customFields":null,"apiAlias":"category"},{"afterCategoryId":"8de9b484c54f441c894774e5f57e485c","parentId":"a515ae260223466f8e37471d279e6406","autoIncrement":8,"mediaId":null,"name":"Men","breadcrumb":["Catalogue #1","Clothing","Men"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|a515ae260223466f8e37471d279e6406|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"2185182cbbd4462ea844abeb2a438b33","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing","Men"],"name":"Men","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.045+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"2185182cbbd4462ea844abeb2a438b33","customFields":null,"apiAlias":"category"},{"afterCategoryId":"a515ae260223466f8e37471d279e6406","parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":9,"mediaId":null,"name":"Free time & electronics","breadcrumb":["Catalogue #1","Free time & electronics"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"251448b91bc742de85643f5fccd89051","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Free time & electronics"],"name":"Free time & electronics","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.045+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"251448b91bc742de85643f5fccd89051","customFields":null,"apiAlias":"category"},{"afterCategoryId":"19ca405790ff4f07aac8c599d4317868","parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":4,"mediaId":null,"name":"Fish","breadcrumb":["Catalogue #1","Food","Fish"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"48f97f432fd041388b2630184139cf0e","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Fish"],"name":"Fish","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"48f97f432fd041388b2630184139cf0e","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":2,"mediaId":null,"name":"Food","breadcrumb":["Catalogue #1","Food"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":false,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"77b959cf66de4c1590c7f9b7da3982f3","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food"],"name":"Food","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.042+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"77b959cf66de4c1590c7f9b7da3982f3","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":"a515ae260223466f8e37471d279e6406","autoIncrement":7,"mediaId":null,"name":"Women","breadcrumb":["Catalogue #1","Clothing","Women"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|a515ae260223466f8e37471d279e6406|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"8de9b484c54f441c894774e5f57e485c","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing","Women"],"name":"Women","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.044+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"8de9b484c54f441c894774e5f57e485c","customFields":null,"apiAlias":"category"},{"afterCategoryId":"77b959cf66de4c1590c7f9b7da3982f3","parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":6,"mediaId":null,"name":"Clothing","breadcrumb":["Catalogue #1","Clothing"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":true,"childCount":2,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"a515ae260223466f8e37471d279e6406","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Clothing"],"name":"Clothing","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.044+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"a515ae260223466f8e37471d279e6406","customFields":null,"apiAlias":"category"},{"afterCategoryId":null,"parentId":null,"autoIncrement":1,"mediaId":null,"name":"Catalogue #1","breadcrumb":["Catalogue #1"],"path":null,"level":1,"active":true,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"695477e02ef643e5a016b83ed4cdf63a","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"a74cfacc8ce2416f8ee843ccc931c3fe","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1"],"name":"Catalogue #1","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:23:16.661+00:00","updatedAt":"2022-03-03T06:28:09.042+00:00","extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"a74cfacc8ce2416f8ee843ccc931c3fe","customFields":null,"apiAlias":"category"},{"afterCategoryId":"48f97f432fd041388b2630184139cf0e","parentId":"77b959cf66de4c1590c7f9b7da3982f3","autoIncrement":5,"mediaId":null,"name":"Sweets","breadcrumb":["Catalogue #1","Food","Sweets"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|77b959cf66de4c1590c7f9b7da3982f3|","level":3,"active":true,"childCount":0,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"bb22b05bff9140f3808b1cff975b75eb","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food","Sweets"],"name":"Sweets","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.043+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"bb22b05bff9140f3808b1cff975b75eb","customFields":null,"apiAlias":"category"}],"success":null,"errors":null}'
                ),
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl($case).'/19ca405790ff4f07aac8c599d4317868' => Http::response(
                    '{"total":null,"data":{"afterCategoryId":null,"parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":2,"mediaId":null,"name":"Food","breadcrumb":["Catalogue #1","Food"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":false,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"77b959cf66de4c1590c7f9b7da3982f3","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food"],"name":"Food","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.042+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"19ca405790ff4f07aac8c599d4317868","customFields":null,"apiAlias":"category"},"success":null,"errors":null}'
                ),
                '*/'.Shopware6EndpointEnum::convertEndpointToUrl($case).'/2185182cbbd4462ea844abeb2a438b33' => Http::response(
                    '{"total":null,"data":{"afterCategoryId":null,"parentId":"a74cfacc8ce2416f8ee843ccc931c3fe","autoIncrement":2,"mediaId":null,"name":"Food","breadcrumb":["Catalogue #1","Food"],"path":"|a74cfacc8ce2416f8ee843ccc931c3fe|","level":2,"active":false,"childCount":3,"visibleChildCount":0,"displayNestedProducts":true,"parent":null,"children":null,"translations":null,"media":null,"products":null,"nestedProducts":null,"tags":null,"cmsPageId":"9758f8c82a2b4ebfa2a513c3fa498eb4","cmsPage":null,"productStreamId":null,"productStream":null,"slotConfig":null,"navigationSalesChannels":null,"footerSalesChannels":null,"serviceSalesChannels":null,"linkType":null,"linkNewTab":null,"internalLink":null,"externalLink":null,"visible":true,"type":"page","productAssignmentType":"product","description":null,"metaTitle":null,"metaDescription":null,"keywords":null,"mainCategories":null,"seoUrls":null,"_uniqueIdentifier":"77b959cf66de4c1590c7f9b7da3982f3","versionId":"0fa91ce3e96a4bc2be4bd9ce752c3425","translated":{"breadcrumb":["Catalogue #1","Food"],"name":"Food","customFields":{},"slotConfig":null,"linkType":null,"internalLink":null,"externalLink":null,"linkNewTab":null,"description":null,"metaTitle":null,"metaDescription":null,"keywords":null},"createdAt":"2022-03-03T06:28:09.042+00:00","updatedAt":null,"extensions":{"foreignKeys":{"apiAlias":null,"extensions":[]}},"id":"2185182cbbd4462ea844abeb2a438b33","customFields":null,"apiAlias":"category"},"success":null,"errors":null}'
                ),
            ]);

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
            SyncShopDataJob::dispatchSync($shop, new ShopDataSyncServiceEndpointLoader());
        } catch(Exception $exception) {
            Notification::assertSentTo($user, ShopSyncFailedNotification::class);

            $shop->refresh();

            $this->assertSame($shop->status, ShopStatusEnum::FAILED->value);
        }
    }
}
