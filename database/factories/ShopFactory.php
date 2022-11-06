<?php

namespace Database\Factories;

use App\Enums\Shop\ShopStatusEnum;
use App\Models\Shop;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $this->faker->name,
            'status' => ShopStatusEnum::NOT_SYNCED->value,
            'url' => $this->faker->url,
            'credentials' => [
                'api_key' => Str::random(),
                'api_secret' => Str::random()
            ],
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
