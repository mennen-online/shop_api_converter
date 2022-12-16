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
use MennenOnline\Shopware5ApiConnector\Exceptions\Connector\EmptyShopware5ResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        try {
            $collection = $connector->getAll(app()->environment('testing') ? 10 : null);

            if ($collection->data) {
                ((new ShopDataSyncServiceLoader())($this->shop))($this->shop, new ShopConnectorService(), $this->endpoint->name, $collection->data);
            } else {
                Log::info($this->endpoint->name.' Response Collection Empty', [
                    'shop' => $this->shop,
                ]);
            }

        } catch(NotFoundHttpException $exception) {
            Log::warning('Endpoint '.$this->endpoint->name.' not found');
        } catch(EmptyShopware5ResponseException $exception) {
            Log::warning('Endpoint ' . $this->endpoint->name.' returned empty response');
        }
    }
}
