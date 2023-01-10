<?php

namespace Tests\Feature\Controllers;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ShopControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::first() ?? User::factory()->create(['email' => 'admin@admin.com'])
        );

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
        $shops = Shop::factory()->shopware6()
            ->count(5)
            ->create();

        $response = $this->get(route('shops.index'));

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Shops')
            ->has('shops')
        );
    }

    /**
     * @test
     */
    public function it_stores_the_shop()
    {
        $data = Shop::factory()->shopware6()
            ->for(User::first())
            ->make()
            ->toArray();

        $data['credentials'] = (array) $data['credentials'];

        $response = $this->post(route('shops.store'), $data);

        unset($data['credentials']);

        $this->assertDatabaseHas('shops', $data);

        $shop = Shop::latest('id')->first();

        $response->assertRedirect(route('shops.index'));

        $this->get(route('shops.index'))->assertInertia(fn (Assert $page) => $page
            ->component('Shops')
        );
    }

    /**
     * @test
     */
    public function it_updates_the_shop()
    {
        $shop = Shop::factory()->shopware6()->create();

        $user = User::factory()->create();

        $data = [
            'name' => $this->faker->name,
            'type' => $shop->type,
            'url' => $this->faker->url,
            'credentials' => $shop->credentials,
            'user_id' => $user->id,
        ];

        $data['credentials'] = json_encode($data['credentials']);

        $response = $this->put(route('shops.update', $shop), $data);

        $data['id'] = $shop->id;

        unset($data['credentials']);

        $this->assertDatabaseHas('shops', $data);

        $response->assertRedirect(route('shops.edit', $shop));
    }

    /**
     * @test
     */
    public function it_deletes_the_shop()
    {
        $shop = Shop::factory()->shopware6()->create();

        $response = $this->delete(route('shops.destroy', $shop));

        $response->assertRedirect(route('shops.index'));

        $this->assertModelMissing($shop);
    }
}
