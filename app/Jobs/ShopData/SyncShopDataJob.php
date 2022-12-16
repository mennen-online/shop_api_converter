<?php

namespace App\Jobs\ShopData;

use App\Enums\Shop\ShopStatusEnum;
use App\Models\Shop;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

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

        $batch = collect($this->endpointLoader->getEndpointEnumCasesForShop($this->shop))
            ->map(fn($endpoint) => new SyncShopDataEntity($this->shop, $endpoint));

        $shop = $this->shop;

        Bus::batch($batch->toArray())
            ->onQueue('sync')
            ->allowFailures()
            ->name($this->shop->name . ' Sync Batch')
            ->finally(function() use($shop) {
            $shop->update([
                'status' => ShopStatusEnum::FINISHED->value
            ]);
        })->dispatch();
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
