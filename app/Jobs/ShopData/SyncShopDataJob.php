<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Models\Shop;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as SW5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as SW6EndpointEnum;

class SyncShopDataJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $batch = collect($this->endpointLoader->getEndpointEnumCasesForShop($this->shop))->map(function (SW5EndpointEnum|SW6EndpointEnum $endpoint) {
            return new SyncShopDataEndpointJob($this->shop, $endpoint);
        });

        $shop = $this->shop;

        Bus::batch($batch)
            ->name($this->shop->name.' Sync')
            ->allowFailures()
            ->catch(function (Exception $exception) use ($shop) {
                $shop->update([
                    'status' => ShopStatusEnum::FAILED->value,
                ]);

                Log::critical('Shop Sync Failed', [
                    'exception' => $exception,
                ]);
            })
            ->finally(function () use ($shop) {
                $shop->update([
                    'status' => ShopStatusEnum::FINISHED->value,
                ]);
            })
            ->dispatch();
    }
}
