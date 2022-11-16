<?php

namespace Database\Factories;

use App\Models\Endpoint;
use App\Models\Entity;
use App\Models\EntityField;
use App\Models\Shop;
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
        $shop = Shop::factory()->shopware6();

        $entity = Entity::factory()->for($shop);

        $entityField = EntityField::factory()->for($entity);

        return [
            'name' => $this->faker->name,
            'url' => $this->faker->url,
            'shop_id' => $shop,
            'entity_id' => $entity,
            'entity_field_id' => $entityField,
        ];
    }
}
