<?php

namespace App\Jobs\ShopData;

use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use App\Services\ShopData\ShopDataSyncServiceLoader;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSingleShopDataEntity implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Shop $shop,
        protected object $endpoint,
        protected array $model
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ((new ShopDataSyncServiceLoader())($this->shop))($this->shop, new ShopConnectorService(), $this->endpoint->name, $this->model);
    }
}
