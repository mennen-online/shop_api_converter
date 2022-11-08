<?php

namespace Database\Factories;

use App\Models\Entity;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use MennenOnline\Shopware6ApiConnector\Enums\EndpointEnum;

class EntityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Entity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => Arr::random(EndpointEnum::cases())->name,
            'shop_id' => Shop::factory(),
        ];
    }
}
