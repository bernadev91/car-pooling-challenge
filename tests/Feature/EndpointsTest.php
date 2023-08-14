<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EndpointsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_status(): void
    {
        $response = $this->get('/status');

        $response->assertStatus(200);
    }

    /**
     * Check that the endpoint to store the list of cars works correctly.
     */
    public function test_cars(): void
    {
        $this->createCars();
    }
    
    /**
     * Check that the endpoint to store a waiting group works correctly.
     */
    public function test_journey(): void
    {
        $this->createCars();
        $this->createJourney();
    }

    /**
     * Check that the endpoint to drop off a group works correctly.
     */
    public function test_dropoff(): void
    {
        $this->createCars();
        $this->createJourney();

        $response = $this->post('/dropoff', ['ID' => 1]);
        $response->assertStatus(200);
    }

    /**
     * Check that the endpoint to store a waiting group works correctly.
     */
    public function test_locate(): void
    {
        $this->createCars();
        $this->createJourney();

        $response = $this->post('/locate', ['ID' => 1]);
        $response->assertStatus(200);

        $this->assertNotEmpty($response->json());
    }

    /**
     * Internal method to create a journey for the test scenario.
     */
    private function createJourney()
    {
        $response = $this->postJson('/journey', [
            'id' => 1,
            'people' => 4,
        ]);
        $response->assertStatus(200);
    }
    /**
     * Internal method to create some cars for testing.
     */
    private function createCars()
    {
        $sample_data = [
            [
                'id' => 1,
                'seats' => 4
            ],
            [
                'id' => 2,
                'seats' => 6
            ]
        ];

        $response = $this->putJson('/cars', $sample_data);
        $response->assertStatus(200);
    }
}
