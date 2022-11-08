<?php

namespace Tests\Feature\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShopControllerTest extends TestCase
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

    protected function castToJson($json)
    {
        if (is_array($json)) {
            $json = addslashes(json_encode($json));
        } elseif (is_null($json) || is_null(json_decode($json))) {
            throw new \Exception(
                'A valid JSON string was not provided for casting.'
            );
        }

        return \DB::raw("CAST('{$json}' AS JSON)");
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_shops()
    {
        $shops = Shop::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('shops.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.shops.index')
            ->assertViewHas('shops');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_shop()
    {
        $response = $this->get(route('shops.create'));

        $response->assertOk()->assertViewIs('app.shops.create');
    }

    /**
     * @test
     */
    public function it_stores_the_shop()
    {
        $data = Shop::factory()
            ->make()
            ->toArray();

        $data['credentials'] = (array) $data['credentials'];

        $response = $this->post(route('shops.store'), $data);

        $data['credentials'] = $this->castToJson($data['credentials']);

        $dbData = $data;

        unset($dbData['credentials']);

        $this->assertDatabaseHas('shops', $dbData);

        $shop = Shop::latest('id')->first();

        $response->assertRedirect(route('shops.edit', $shop));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_shop()
    {
        $shop = Shop::factory()->create();

        $response = $this->get(route('shops.show', $shop));

        $response
            ->assertOk()
            ->assertViewIs('app.shops.show')
            ->assertViewHas('shop');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_shop()
    {
        $shop = Shop::factory()->create();

        $response = $this->get(route('shops.edit', $shop));

        $response
            ->assertOk()
            ->assertViewIs('app.shops.edit')
            ->assertViewHas('shop');
    }

    /**
     * @test
     */
    public function it_updates_the_shop()
    {
        $shop = Shop::factory()->create();

        $user = User::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'credentials' => [],
            'user_id' => $user->id,
        ];

        $data['credentials'] = json_encode($data['credentials']);

        $response = $this->put(route('shops.update', $shop), $data);

        $data['id'] = $shop->id;

        $this->assertDatabaseHas('shops', $data);

        $response->assertRedirect(route('shops.edit', $shop));
    }

    /**
     * @test
     */
    public function it_deletes_the_shop()
    {
        $shop = Shop::factory()->create();

        $response = $this->delete(route('shops.destroy', $shop));

        $response->assertRedirect(route('shops.index'));

        $this->assertModelMissing($shop);
    }
}
