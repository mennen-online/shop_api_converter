<?php

namespace App\Http\Controllers;

use App\Enums\Shop\ShopStatusEnum;
use App\Enums\Shop\ShopTypeEnum;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Models\Shop;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
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
    public function store(Request $request)
    {
        $this->authorize('create', Shop::class);

        $shopInfo = json_decode($request->getContent());

        $shopType = null;
        $credentials = null;
        $route = redirect()->route('shops.index');

        switch ($shopInfo->shopType) {
            case 'shopware5':
                $shopType = ShopTypeEnum::SHOPWARE5->value;
                $credentials = [
                    'username' => $shopInfo->shopUsername,
                    'password' => $shopInfo->shopApiToken,

                ];
                break;
            case 'shopware6':
                $shopType = ShopTypeEnum::SHOPWARE6->value;
                $credentials = [
                    'api_key' => $shopInfo->shopClientID,
                    'api_secret' => $shopInfo->shopClientSecret,
                ];
                break;
        }

        if (Shop::where('url', $shopInfo->shopUrl)->exists()) {
            return $route->with([
                'message' => [
                    'title' => 'Error!',
                    'text' => 'Shop creation failed!',
                    'type' => 'error',
                ],
            ]);
        }

        Shop::updateOrCreate(
            [
                'name' => $shopInfo->shopName,
                'url' => $shopInfo->shopUrl,
                'type' => $shopType,
                'status' => ShopStatusEnum::NOT_SYNCED->value,
                'user_id' => $request->user()->id,
                'credentials' => $credentials,
            ]
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
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        $this->authorize('create', Shop::class);

        $users = User::pluck('name', 'id');

        return view('app.shops.create', compact('users'));
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
                'shop' => $shop->only('id', 'name', 'type', 'url', 'status', 'created_at', 'updated_at'),
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
