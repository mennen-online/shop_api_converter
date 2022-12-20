<?php

namespace App\Jobs\ShopData;

use App\Models\Shop;
use App\Services\Shop\Connector\ShopConnectorService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use MennenOnline\LaravelResponseModels\Models\BaseModel;

class SyncShopDataEntity implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Shop $shop,
        protected object $endpoint
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $connector = (new ShopConnectorService())->getConnector($this->shop, $this->endpoint);

        $collection = $connector->getAll(app()->environment('testing') ? 10 : null);

        if ($collection->data) {
            $batch = $collection->data->map(fn(BaseModel $model) => new ProcessSingleShopDataEntity($this->shop, $this->endpoint, $model->getAttributes()));
            Bus::batch($batch->toArray())
                ->name($this->shop->name . ' Entity Processor for ' . $this->endpoint->name)
                ->onQueue('sync')
                ->allowFailures()
                ->dispatch();
        } else {
            Log::info($this->endpoint->name.' Response Collection Empty', [
                'shop' => $this->shop,
            ]);
        }
    }
}
