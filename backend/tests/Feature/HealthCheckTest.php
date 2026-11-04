<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_check_returns_200_when_services_are_up(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
                'services' => [
                    'database' => [
                        'status' => 'up',
                    ],
                    'cache' => [
                        'status' => 'up',
                    ],
                ],
            ])
            ->assertJsonStructure([
                'status',
                'timestamp',
                'system' => ['php_version', 'app_env'],
                'services' => [
                    'database' => ['status', 'message'],
                    'cache' => ['status', 'driver', 'message'],
                ],
            ]);
    }

    public function test_health_check_returns_503_when_database_fails(): void
    {
        DB::shouldReceive('connection->getPdo')
            ->once()
            ->andThrow(new \PDOException('Database connection lost.'));

        $response = $this->getJson('/health');

        $response->assertStatus(503)
            ->assertJson([
                'status' => 'unhealthy',
                'services' => [
                    'database' => [
                        'status' => 'down',
                    ],
                ],
            ]);
    }
}
