<?php

namespace Tests\Feature\Api;

use App\Models\Entity;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShopEntitiesTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::first());
    }

    /**
     * @test
     */
    public function it_gets_shop_entities()
    {
        $shop = Shop::factory()->shopware6()->create();
        $entities = Entity::factory()
            ->count(2)
            ->create([
                'shop_id' => $shop->id,
            ]);

        $response = $this->getJson(route('api.shops.entities.index', $shop));

        $response->assertOk()->assertSee($entities[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_shop_entities()
    {
        $shop = Shop::factory()->shopware6()->create();
        $data = Entity::factory()
            ->make([
                'shop_id' => $shop->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.shops.entities.store', $shop),
            $data
        );

        unset($data['name']);
        unset($data['shop_id']);

        $this->assertDatabaseHas('entities', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $entity = Entity::latest('id')->first();

        $this->assertEquals($shop->id, $entity->shop_id);
    }
}
