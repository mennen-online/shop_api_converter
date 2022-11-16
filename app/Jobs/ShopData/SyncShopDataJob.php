<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Exceptions\Shop\ShopSyncFailedException;
use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use App\Services\ShopData\ShopDataSyncServiceLoader;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware6ApiConnector\Exceptions\Connector\EmptyShopware6ResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        protected ShopDataSyncServiceEndpointLoader $endpointLoader
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
            foreach ($this->endpointLoader->getEndpointEnumCasesForShop($this->shop) as $endpoint) {
                $connector = (new ShopConnectorService())->getConnector($this->shop, $endpoint);

                try {
                    $collection = $connector->getAll(app()->environment('testing') ? 10 : null);
                } catch(NotFoundHttpException $exception) {
                    Log::warning("Endpoint $endpoint->name not found");

                    continue;
                }

                if ($collection->data) {
                    ((new ShopDataSyncServiceLoader())($this->shop))($this->shop, new ShopConnectorService(), $endpoint->name, $collection->data);
                }
            }
        } catch(Exception|ShopSyncFailedException|EmptyShopware6ResponseException $exception) {
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
