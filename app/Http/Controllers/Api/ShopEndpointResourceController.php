<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ShowShopEndpointResourceRequest;
use App\Models\Endpoint;

class ShopEndpointResourceController extends Controller
{
    public function show(ShowShopEndpointResourceRequest $request, Endpoint $endpoint, string|int|null $id = null) {
        /** TODO:
         * Implementing Logic:
         *
         * Entity ID and Entity Field ID are the key for each related Data, chosen by the Customer
         *
         * Collection of ENTITY[ENTITY FIELD ID] Containing ENTITY[ENTITY_FIELD_ID] as Array
         */
    }
}
