<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Entity;
use App\Models\Shop;
use App\Notifications\Shop\ShopSyncFailedNotification;
use App\Services\ShopData\ShopDataSyncService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware6ApiConnector\Endpoints\CategoryEndpoint;
use MennenOnline\Shopware6ApiConnector\Endpoints\ProductEndpoint;
use MennenOnline\Shopware6ApiConnector\Enums\Endpoint;
use MennenOnline\Shopware6ApiConnector\Shopware6ApiConnector;
use ReflectionObject;

class SyncShopDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Shop                       $shop,
        protected Shopware6ApiConnector|null $categoryEndpoint = null,
        protected Shopware6ApiConnector|null $productEndpoint = null,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->shop->update(
            [
                'status' => ShopStatusEnum::RUNNING->value
            ]
        );

        try {
            $this->categoryEndpoint = Shopware6ApiConnector::category(
                url          : $this->shop->url,
                client_id    : $this->shop->credentials->api_key,
                client_secret: $this->shop->credentials->api_secret
            );

            $categories = collect($this->categoryEndpoint->getAll(app()->environment('testing') ? 10 : null)->data);

            (new ShopDataSyncService())($this->shop, $this->categoryEndpoint, 'category', $categories);

            $this->productEndpoint = Shopware6ApiConnector::product(client: $this->categoryEndpoint->getClient());

            $products = collect($this->productEndpoint->getAll(app()->environment('testing') ? 10 : null)->data);

            (new ShopDataSyncService())($this->shop, $this->productEndpoint, 'product', $products);
        }catch(Exception|ShopSyncFailedException $exception) {
            $this->shop->update([
                'status' => ShopStatusEnum::FAILED->value
            ]);

            Log::critical("Shop Sync Failed", [
                'exception' => $exception
            ]);

            $this->fail($exception);

            throw $exception;
        }
    }
}
