<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntityCollection;
use App\Http\Resources\EntityResource;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopEntitiesController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Shop $shop)
    {
        $this->authorize('view', $shop);

        $search = $request->get('search', '');

        $entities = $shop
            ->entities()
            ->search($search)
            ->latest()
            ->paginate();

        return new EntityCollection($entities);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop  $shop
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Shop $shop)
    {
        $this->authorize('create', Entity::class);

        $validated = $request->validate([
            'name' => ['required', 'max:255', 'string'],
        ]);

        $entity = $shop->entities()->create($validated);

        return new EntityResource($entity);
    }
}
