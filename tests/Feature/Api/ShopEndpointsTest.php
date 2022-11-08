<?php

namespace Tests\Feature\Api;

use App\Models\Endpoint;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopEndpointsTest extends TestCase
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
    public function it_gets_shop_endpoints()
    {
        $shop = Shop::factory()->create();
        $endpoints = Endpoint::factory()
            ->count(2)
            ->create([
                'shop_id' => $shop->id,
            ]);

        $response = $this->getJson(route('api.shops.endpoints.index', $shop));

        $response->assertOk()->assertSee($endpoints[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_shop_endpoints()
    {
        $shop = Shop::factory()->create();
        $data = Endpoint::factory()
            ->make([
                'shop_id' => $shop->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.shops.endpoints.store', $shop),
            $data
        );

        $this->assertDatabaseHas('endpoints', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $endpoint = Endpoint::latest('id')->first();

        $this->assertEquals($shop->id, $endpoint->shop_id);
    }
}
