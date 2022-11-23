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
use Illuminate\Support\Facades\Log;
use MennenOnline\Shopware5ApiConnector\Enums\EndpointEnum as SW5EndpointEnum;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum as SW6EndpointEnum;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SyncShopDataEndpointJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected Shop $shop,
        protected SW5EndpointEnum|SW6EndpointEnum $endpointEnum
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
        $connector = (new ShopConnectorService())->getConnector($this->shop, $this->endpointEnum);

        try {
            $collection = $connector->getAll(app()->environment('testing') ? 10 : null);
            if ($collection->data) {
                ((new ShopDataSyncServiceLoader())($this->shop))($this->shop, new ShopConnectorService(), $this->endpointEnum->name, $collection->data);
            }
        } catch(NotFoundHttpException $exception) {
            Log::warning('Endpoint '.$this->endpointEnum->name.' not found');
            $this->fail($exception);
        }
    }
}
