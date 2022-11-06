<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use App\Models\ShopData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopDataResource;
use App\Http\Resources\ShopDataCollection;

class ShopAllShopDataController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Shop $shop)
    {
        $this->authorize('view', $shop);

        $search = $request->get('search', '');

        $allShopData = $shop
            ->allShopData()
            ->search($search)
            ->latest()
            ->paginate();

        return new ShopDataCollection($allShopData);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Shop $shop
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Shop $shop)
    {
        $this->authorize('create', ShopData::class);

        $validated = $request->validate([
            'entity_id' => ['required', 'exists:entities,id'],
            'content' => ['required', 'max:255', 'json'],
        ]);

        $shopData = $shop->allShopData()->create($validated);

        return new ShopDataResource($shopData);
    }
}
