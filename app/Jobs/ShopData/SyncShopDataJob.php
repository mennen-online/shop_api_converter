<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use App\Services\ShopData\ShopDataSyncServiceLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
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
            } else {
                Log::info($endpoint->name . " Response Collection Empty", [
                    'shop' => $this->shop
                ]);
            }
        }
    }

    public function failed($exception) {
        $this->shop->update([
            'status' => ShopStatusEnum::FAILED->value,
        ]);

        Log::critical('Shop Sync Failed', [
            'exception' => $exception,
        ]);
    }
}
