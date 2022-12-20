<?php

namespace App\Http\Controllers;

use App\Enums\Shop\ShopStatusEnum;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Jobs\ShopData\SyncShopDataJob;
use App\Models\Shop;
use App\Models\User;
use App\Services\ShopData\ShopDataSyncServiceEndpointLoader;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ShopController extends Controller
{
    /**
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Shop::class);

        $search = $request->get('search', '');

        $shops = Shop::search($search)
            ->latest()
            ->orderBy('id')
            ->select('id', 'name', 'type', 'url', 'status', 'created_at', 'updated_at')
            ->paginate(12)
            ->toArray();

        return Inertia::render('Shops', ['shops' => $shops])->with(['shop']);
    }

    /**
     * @param  ShopStoreRequest  $request
     * @return RedirectResponse
     */
    public function store(ShopStoreRequest $request)
    {
        $this->authorize('create', Shop::class);

        $route = redirect()->route('shops.index');

        $request->user()->shop()->updateOrCreate(
            array_merge(
                $request->validated(),
                [
                    'status' => ShopStatusEnum::NOT_SYNCED->value,
                ]
            )
        );

        return $route->with([
            'message' => [
                'title' => 'Success!',
                'text' => 'Shop created successfully',
                'type' => 'success',
            ],
        ]);
    }

    /**
     * @param  Request  $request
     * @param  Shop  $shop
     * @return \Inertia\Response
     */
    public function show(Request $request, Shop $shop)
    {
        try {
            $this->authorize('view', $shop);

            return Inertia::render('ShopsDetail', [
                'header' => 'Shop Information',
                'shop' => $shop->toArray(),
            ]);
        } catch (Exception $e) {
            Log::critical($e);
        }
    }

    /**
     * @param  Request  $request
     * @param  Shop  $shop
     * @return Response
     */
    public function edit(Request $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $users = User::pluck('name', 'id');

        return view('app.shops.edit', compact('shop', 'users'));
    }

    /**
     * @param  ShopUpdateRequest  $request
     * @param  Shop  $shop
     * @return Response
     */
    public function update(ShopUpdateRequest $request, Shop $shop)
    {
        $this->authorize('update', $shop);

        $validated = $request->validated();
        $validated['credentials'] = json_decode(
            $validated['credentials'],
            true
        );

        $shop->update($validated);

        return redirect()
            ->route('shops.edit', $shop)
            ->withSuccess(__('crud.common.saved'));
    }

    public function sync(Request $request, Shop $shop) {
        SyncShopDataJob::dispatch($shop, new ShopDataSyncServiceEndpointLoader());

        $shop->update([
            'status' => ShopStatusEnum::QUEUED->value,
        ]);

        return to_route('shops.index');
    }

    /**
     * @param  Request  $request
     * @param  Shop  $shop
     * @return Response
     */
    public function destroy(Request $request, Shop $shop)
    {
        $this->authorize('delete', $shop);

        $shop->delete();

        return redirect()
            ->route('shops.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
