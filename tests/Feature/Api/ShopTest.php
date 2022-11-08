<?php

namespace Tests\Feature\Api;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopTest extends TestCase
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
    public function it_gets_shops_list()
    {
        $shops = Shop::factory()
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
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.shops.store'), $data);

        $dbData = $data;

        unset($dbData['credentials']);

        $this->assertDatabaseHas('shops', $dbData);

        $response->assertStatus(201)->assertJsonFragment($dbData);
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
        $shop = Shop::factory()->create();

        $response = $this->deleteJson(route('api.shops.destroy', $shop));

        $this->assertModelMissing($shop);

        $response->assertNoContent();
    }
}
