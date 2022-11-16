<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EndpointCollection;
use App\Http\Resources\EndpointResource;
use App\Models\Endpoint;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopEndpointsController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return EndpointCollection
     */
    public function index(Request $request, Shop $shop)
    {
        $this->authorize('view', $shop);

        $search = $request->get('search', '');

        $endpoints = $shop
            ->endpoints()
            ->search($search)
            ->latest()
            ->paginate();

        return new EndpointCollection($endpoints);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return EndpointResource
     */
    public function store(Request $request, Shop $shop)
    {
        $this->authorize('create', Endpoint::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
            'url' => ['required', 'url'],
            'entity_id' => ['required', 'exists:entities,id'],
            'entity_field_id' => ['required', 'exists:entity_fields,id'],
        ]);

        $endpoint = $shop->endpoints()->create($validated);

        return new EndpointResource($endpoint);
    }
}
