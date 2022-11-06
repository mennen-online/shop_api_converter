<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Shop;
use App\Models\Entity;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShopEntitiesTest extends TestCase
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
    public function it_gets_shop_entities()
    {
        $shop = Shop::factory()->create();
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
        $shop = Shop::factory()->create();
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
