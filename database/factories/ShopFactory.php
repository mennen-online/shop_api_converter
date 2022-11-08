<?php

namespace Database\Factories;

use App\Enums\Shop\ShopStatusEnum;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShopFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Str::random(),
            'status' => ShopStatusEnum::NOT_SYNCED->value,
            'url' => $this->faker->url,
            'credentials' => [
                'api_key' => Str::random(),
                'api_secret' => Str::random(),
            ],
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
