<?php

namespace Tests\Feature\Api;

use App\Models\Endpoint;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndpointTest extends TestCase
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
    public function it_gets_endpoints_list()
    {
        $endpoints = Endpoint::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.endpoints.index'));

        $response->assertOk()->assertSee($endpoints[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_endpoint()
    {
        $data = Endpoint::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.endpoints.store'), $data);

        $this->assertDatabaseHas('endpoints', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $shop = Shop::factory()->shopware6()->create();

        $data = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'shop_id' => $shop->id,
            'entity_id' => $endpoint->entity_id,
            'entity_field_id' => $endpoint->entity_field_id,
        ];

        $response = $this->putJson(
            route('api.endpoints.update', $endpoint),
            $data
        );

        $data['id'] = $endpoint->id;

        $this->assertDatabaseHas('endpoints', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $response = $this->deleteJson(
            route('api.endpoints.destroy', $endpoint)
        );

        $this->assertModelMissing($endpoint);

        $response->assertNoContent();
    }
}
