<?php

namespace Tests\Feature\Api;

use App\Models\Endpoint;
use App\Models\Entity;
use App\Models\EntityField;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShopEndpointsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed');

        $this->actingAs(User::first());
    }

    /**
     * @test
     */
    public function it_gets_shop_endpoints()
    {
        $shop = Shop::factory()->shopware6()->create();
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
        $shop = Shop::factory()->shopware6()->create();
        $entity = Entity::factory()->for($shop)->create();
        $entityField = EntityField::factory()->for($entity)->create();
        $data = Endpoint::factory()
            ->for($shop)
            ->make(
                [
                    'entity_id' => $entity->id,
                    'entity_field_id' => $entityField->id,
                ]
            )
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
