<?php

namespace Database\Factories;

use App\Models\Endpoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class EndpointFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Endpoint::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'shop_id' => \App\Models\Shop::factory(),
        ];
    }
}
