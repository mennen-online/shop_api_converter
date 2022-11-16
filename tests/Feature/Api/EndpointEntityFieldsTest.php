<?php

namespace Tests\Feature\Api;

use App\Models\Endpoint;
use App\Models\EntityField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndpointEntityFieldsTest extends TestCase
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
    public function it_gets_endpoint_entity_fields()
    {
        $endpoint = Endpoint::factory()->create();
        $entityField = EntityField::factory()->create();

        $endpoint->entityFields()->attach($entityField);

        $response = $this->getJson(
            route('api.endpoints.entity-fields.index', $endpoint)
        );

        $response->assertOk()->assertSee($entityField->name);
    }

    /**
     * @test
     */
    public function it_can_attach_entity_fields_to_endpoint()
    {
        $endpoint = Endpoint::factory()->create();
        $entityField = EntityField::factory()->create();

        $response = $this->postJson(
            route('api.endpoints.entity-fields.store', [
                $endpoint,
                $entityField,
            ])
        );

        $response->assertNoContent();

        $this->assertTrue(
            $endpoint
                ->entityFields()
                ->where('entity_fields.id', $entityField->id)
                ->exists()
        );
    }

    /**
     * @test
     */
    public function it_can_detach_entity_fields_from_endpoint()
    {
        $endpoint = Endpoint::factory()->create();
        $entityField = EntityField::factory()->create();

        $response = $this->deleteJson(
            route('api.endpoints.entity-fields.store', [
                $endpoint,
                $entityField,
            ])
        );

        $response->assertNoContent();

        $this->assertFalse(
            $endpoint
                ->entityFields()
                ->where('entity_fields.id', $entityField->id)
                ->exists()
        );
    }
}
