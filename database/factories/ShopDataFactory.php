<?php

namespace Database\Factories;

use App\Models\ShopData;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopDataFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShopData::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'content' => json_encode([
                'type' => $this->faker->text(10),
            ]),
            'entity_id' => \App\Models\Entity::factory(),
            'shop_id' => \App\Models\Shop::factory(),
        ];
    }
}
