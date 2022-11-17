<?php

namespace App\Http\Controllers;

use App\Http\Requests\EndpointStoreRequest;
use App\Http\Requests\EndpointUpdateRequest;
use App\Models\Endpoint;
use App\Models\Shop;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EndpointController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function index(Request $request, Shop $shop)
    {
        $this->authorize('view-any', Endpoint::class);

        $search = $request->get('search', '');

        $endpoints = Endpoint::search($search)
            ->whereShopId($shop->id)
            ->paginate(5)
            ->toArray();

        return Inertia::render('ShopsDetail', [
            'header' => 'Endpoints',
            'shop' => $shop->only('id', 'name', 'url', 'status', 'created_at', 'updated_at'),
            'endpoints' => $endpoints['data']
        ]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $this->authorize('create', Endpoint::class);

        $shops = Shop::pluck('name', 'id');

        return view('app.endpoints.create', compact('shops'));
    }

    /**
     * @param  \App\Http\Requests\EndpointStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EndpointStoreRequest $request)
    {
        $this->authorize('create', Endpoint::class);

        $validated = $request->validated();

        $endpoint = Endpoint::create($validated);

        return redirect()
            ->route('endpoints.edit', $endpoint)
            ->withSuccess(__('crud.common.created'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Endpoint  $endpoint
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Endpoint $endpoint)
    {
        $this->authorize('view', $endpoint);

        return view('app.endpoints.show', compact('endpoint'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Endpoint  $endpoint
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Endpoint $endpoint)
    {
        $this->authorize('update', $endpoint);

        $shops = Shop::pluck('name', 'id');

        return view('app.endpoints.edit', compact('endpoint', 'shops'));
    }

    /**
     * @param  \App\Http\Requests\EndpointUpdateRequest  $request
     * @param  \App\Models\Endpoint  $endpoint
     * @return \Illuminate\Http\Response
     */
    public function update(EndpointUpdateRequest $request, Endpoint $endpoint)
    {
        $this->authorize('update', $endpoint);

        $validated = $request->validated();

        $endpoint->update($validated);

        return redirect()
            ->route('endpoints.edit', $endpoint)
            ->withSuccess(__('crud.common.saved'));
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Endpoint  $endpoint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Endpoint $endpoint)
    {
        $this->authorize('delete', $endpoint);

        $endpoint->delete();

        return redirect()
            ->route('endpoints.index')
            ->withSuccess(__('crud.common.removed'));
    }
}
