<?php

namespace App\Observers;

use App\Enums\Shop\ShopStatusEnum;
use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Notifications\Shop\ShopSyncFailedNotification;
use App\Notifications\Shop\ShopSyncQueuedNotification;
use App\Notifications\Shop\ShopSyncStartedNotification;

class ShopObserver
{
    /**
     * Handle the Shop "created" event.
     *
     * @param  \App\Models\Shop  $shop
     * @return void
     */
    public function created(Shop $shop)
    {
        $shop->update([
            'status' => ShopStatusEnum::NOT_SYNCED->value
        ]);

        if(!app()->environment('testing')) {
            SyncShopDataJob::dispatch($shop);

            $shop->update([
                'status' => ShopStatusEnum::QUEUED->value
            ]);
        }
    }

    /**
     * Handle the Shop "updated" event.
     *
     * @param  \App\Models\Shop  $shop
     * @return void
     */
    public function updated(Shop $shop)
    {
        match($shop->status) {
            ShopStatusEnum::QUEUED->value => $shop->user->notify(new ShopSyncQueuedNotification($shop)),
            ShopStatusEnum::RUNNING->value => $shop->user->notify(new ShopSyncStartedNotification($shop)),
            ShopStatusEnum::FAILED->value => $shop->user->notify(new ShopSyncFailedNotification($shop)),
            default => null
        };
    }

    /**
     * Handle the Shop "deleted" event.
     *
     * @param  \App\Models\Shop  $shop
     * @return void
     */
    public function deleted(Shop $shop)
    {
        //
    }

    /**
     * Handle the Shop "restored" event.
     *
     * @param  \App\Models\Shop  $shop
     * @return void
     */
    public function restored(Shop $shop)
    {
        //
    }

    /**
     * Handle the Shop "force deleted" event.
     *
     * @param  \App\Models\Shop  $shop
     * @return void
     */
    public function forceDeleted(Shop $shop)
    {
        //
    }
}
