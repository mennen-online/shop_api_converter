<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EndpointStoreRequest;
use App\Http\Requests\EndpointUpdateRequest;
use App\Http\Resources\EndpointCollection;
use App\Http\Resources\EndpointResource;
use App\Models\Endpoint;
use Illuminate\Http\Request;

class EndpointController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view-any', Endpoint::class);

        $search = $request->get('search', '');

        $endpoints = Endpoint::search($search)
            ->latest()
            ->paginate();

        return new EndpointCollection($endpoints);
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

        return new EndpointResource($endpoint);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Endpoint  $endpoint
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Endpoint $endpoint)
    {
        $this->authorize('view', $endpoint);

        return new EndpointResource($endpoint);
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

        return new EndpointResource($endpoint);
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

        return response()->noContent();
    }
}
