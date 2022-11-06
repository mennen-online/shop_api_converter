<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Endpoint;

use App\Models\Shop;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EndpointControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_endpoints()
    {
        $endpoints = Endpoint::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('endpoints.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.endpoints.index')
            ->assertViewHas('endpoints');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_endpoint()
    {
        $response = $this->get(route('endpoints.create'));

        $response->assertOk()->assertViewIs('app.endpoints.create');
    }

    /**
     * @test
     */
    public function it_stores_the_endpoint()
    {
        $data = Endpoint::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('endpoints.store'), $data);

        $this->assertDatabaseHas('endpoints', $data);

        $endpoint = Endpoint::latest('id')->first();

        $response->assertRedirect(route('endpoints.edit', $endpoint));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $response = $this->get(route('endpoints.show', $endpoint));

        $response
            ->assertOk()
            ->assertViewIs('app.endpoints.show')
            ->assertViewHas('endpoint');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $response = $this->get(route('endpoints.edit', $endpoint));

        $response
            ->assertOk()
            ->assertViewIs('app.endpoints.edit')
            ->assertViewHas('endpoint');
    }

    /**
     * @test
     */
    public function it_updates_the_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $shop = Shop::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'shop_id' => $shop->id,
        ];

        $response = $this->put(route('endpoints.update', $endpoint), $data);

        $data['id'] = $endpoint->id;

        $this->assertDatabaseHas('endpoints', $data);

        $response->assertRedirect(route('endpoints.edit', $endpoint));
    }

    /**
     * @test
     */
    public function it_deletes_the_endpoint()
    {
        $endpoint = Endpoint::factory()->create();

        $response = $this->delete(route('endpoints.destroy', $endpoint));

        $response->assertRedirect(route('endpoints.index'));

        $this->assertModelMissing($endpoint);
    }
}
