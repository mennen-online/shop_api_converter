<?php

namespace Tests\Feature\Api;

use App\Models\Shop;
use App\Models\ShopData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopAllShopDataTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['email' => 'admin@admin.com']);

        Sanctum::actingAs($user, [], 'web');

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_shop_all_shop_data()
    {
        $allShopData = ShopData::factory()
            ->count(2)
            ->for(Shop::factory())
            ->create();

        $response = $this->getJson(
            route('api.shops.all-shop-data.index', $allShopData->first()->shop)
        );

        $response->assertOk()->assertSee($allShopData[0]->id);
    }

    /**
     * @test
     */
    public function it_stores_the_shop_all_shop_data()
    {
        $data = ShopData::factory()
            ->for(Shop::factory())
            ->make()
            ->toArray();

        $shop = Shop::find($data['shop_id']);

        $response = $this->postJson(
            route('api.shops.all-shop-data.store', $data['shop_id']),
            $data
        );

        unset($data['shop_id']);
        unset($data['entity_id']);
        unset($data['content']);

        $this->assertDatabaseHas('shop_data', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $shopData = ShopData::latest('id')->first();

        $this->assertEquals($shop->id, $shopData->shop_id);
    }
}
