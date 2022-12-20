<?php

namespace Tests\Feature\Api;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShopTest extends TestCase
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
    public function it_gets_shops_list()
    {
        Shop::all()->each(fn (Shop $shop) => $shop->delete());

        $shops = Shop::factory()
            ->shopware6()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.shops.index'));

        $response->assertOk()->assertSee($shops[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_shop()
    {
        $data = Shop::factory()
            ->shopware6()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.shops.store'), $data);

        $dbData = $data;

        unset($dbData['credentials']);

        unset($dbData['summary']);

        unset($dbData['entities']);

        $this->assertDatabaseHas('shops', $dbData);

        $response->assertStatus(201)->assertJsonFragment($dbData);
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
            'url' => $this->faker->url,
            'type' => $shop->type,
            'credentials' => json_encode([
                'api_key' => Str::random(),
                'api_secret' => Str::random(),
            ]),
            'user_id' => $user->id,
        ];

        $response = $this->putJson(route('api.shops.update', $shop), $data);

        $data['id'] = $shop->id;

        $dbData = $data;

        unset($dbData['credentials']);

        $this->assertDatabaseHas('shops', $dbData);

        $response->assertOk()->assertJsonFragment($dbData);
    }

    /**
     * @test
     */
    public function it_deletes_the_shop()
    {
        $shop = Shop::factory()->shopware6()->create();

        $response = $this->deleteJson(route('api.shops.destroy', $shop));

        $this->assertModelMissing($shop);

        $response->assertNoContent();
    }
}
