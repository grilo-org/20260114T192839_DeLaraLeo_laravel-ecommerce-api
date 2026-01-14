<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test health check endpoint returns 200 OK
     *
     * @return void
     */
    public function test_health_check_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'service',
                'timestamp',
                'environment',
            ])
            ->assertJson([
                'status' => 'ok',
            ]);
    }

    /**
     * Test health check endpoint returns correct service name
     *
     * @return void
     */
    public function test_health_check_returns_service_name(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'service',
            ]);

        $this->assertNotEmpty($response->json('service'));
    }

    /**
     * Test health check endpoint returns timestamp
     *
     * @return void
     */
    public function test_health_check_returns_timestamp(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'timestamp',
            ]);

        $this->assertNotEmpty($response->json('timestamp'));
    }
}

