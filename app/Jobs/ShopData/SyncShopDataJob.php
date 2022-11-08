<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Shop;
use App\Services\ShopData\ShopDataSyncService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware6ApiConnector\Endpoints\Endpoint;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;

class SyncShopDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Shop $shop,
        protected Endpoint|null $endpoint = null,
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->shop->update(
            [
                'status' => ShopStatusEnum::RUNNING->value,
            ]
        );

        try {
            foreach (EndpointEnum::cases() as $endpoint) {
                $this->endpoint = new Endpoint(
                    url: $this->shop->url,
                    client_id: $this->shop->credentials->api_key,
                    client_secret: $this->shop->credentials->api_secret,
                    endpoint: $endpoint
                );

                $response = $this->endpoint->getAll(app()->environment('testing') ? 10 : null);

                if ($response?->total > 0) {
                    $collection = collect($response->data);

                    (new ShopDataSyncService())($this->shop, $this->endpoint, $endpoint->name, $collection);
                }
            }
        } catch(Exception|ShopSyncFailedException $exception) {
            $this->shop->update([
                'status' => ShopStatusEnum::FAILED->value,
            ]);

            Log::critical('Shop Sync Failed', [
                'exception' => $exception,
            ]);

            $this->fail($exception);

            throw $exception;
        }
    }
}
