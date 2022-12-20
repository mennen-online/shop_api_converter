<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShopStoreRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return ShopCollection
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Shop::class);

        $search = $request->get('search', '');

        $shops = Shop::search($search)
            ->latest()
            ->paginate();

        return new ShopCollection($shops);
    }

    /**
     * @param  \App\Http\Requests\ShopStoreRequest  $request
     * @return ShopResource
     */
    public function store(ShopStoreRequest $request)
    {
        $this->authorize('create', Shop::class);

        $validated = $request->validated();

        $shop = $request->user()->shop()->create($validated);

        return new ShopResource($shop);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return ShopResource
     */
    public function show(Request $request, Shop $shop)
    {
        $this->authorize('view', $shop);

        return new ShopResource($shop);
    }

    /**
     * @param  \App\Http\Requests\ShopUpdateRequest  $request
     * @param  \App\Models\Shop  $shop
     * @return ShopResource
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

        return new ShopResource($shop);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Shop $shop)
    {
        $this->authorize('delete', $shop);

        $shop->delete();

        return response()->noContent();
    }
}
