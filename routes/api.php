<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EndpointController;
use App\Http\Controllers\Api\EndpointEntityFieldsController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ShopAllShopDataController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\ShopEndpointResourceController;
use App\Http\Controllers\Api\ShopEndpointsController;
use App\Http\Controllers\Api\ShopEntitiesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')
    ->get('/user', function (Request $request) {
        return $request->user();
    })
    ->name('api.user');

Route::name('api.')
    ->middleware('auth:sanctum')
    ->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);

        Route::apiResource('shops', ShopController::class);

        // Shop All Shop Data
        Route::get('/shops/{shop}/all-shop-data', [
            ShopAllShopDataController::class,
            'index',
        ])->name('shops.all-shop-data.index');
        Route::post('/shops/{shop}/all-shop-data', [
            ShopAllShopDataController::class,
            'store',
        ])->name('shops.all-shop-data.store');

        // Shop Endpoints
        Route::get('/shops/{shop}/endpoints', [
            ShopEndpointsController::class,
            'index',
        ])->name('shops.endpoints.index');
        Route::post('/shops/{shop}/endpoints', [
            ShopEndpointsController::class,
            'store',
        ])->name('shops.endpoints.store');

        // Shop Entities
        Route::get('/shops/{shop}/entities', [
            ShopEntitiesController::class,
            'index',
        ])->name('shops.entities.index');
        Route::post('/shops/{shop}/entities', [
            ShopEntitiesController::class,
            'store',
        ])->name('shops.entities.store');

        Route::apiResource('endpoints', EndpointController::class);

        // Endpoint Entity Fields
        Route::get('/endpoints/{endpoint}/entity-fields', [
            EndpointEntityFieldsController::class,
            'index',
        ])->name('endpoints.entity-fields.index');
        Route::post('/endpoints/{endpoint}/entity-fields/{entityField}', [
            EndpointEntityFieldsController::class,
            'store',
        ])->name('endpoints.entity-fields.store');
        Route::delete('/endpoints/{endpoint}/entity-fields/{entityField}', [
            EndpointEntityFieldsController::class,
            'destroy',
        ])->name('endpoints.entity-fields.destroy');

        Route::get('{endpoint:url}/{id?}', [
            ShopEndpointResourceController::class,
            'show'
        ])->name('customer-endpoint');
    });
