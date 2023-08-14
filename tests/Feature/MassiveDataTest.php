<?php

namespace Tests\Feature;

use App\Models\Journey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MassiveDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating 10^4 cars
     */
    public function test_massive_cars(): void
    {
        $response = $this->get('/status');
        $response->assertStatus(200);

        $sample_data = [];
        for ($i=0; $i < pow(10, 4); $i++)
        {
            $sample_data[] = [
                'id' => $i+1,
                'seats' => mt_rand(4, 6),
            ];
        }

        $response = $this->putJson('/cars', $sample_data);
        $response->assertStatus(200);
    }
    /**
     * Test creating 10^4 cars and 10^5 journeys
     */
    public function test_massive_journeys()
    {
        // $this->markTestSkipped('slow test. 145 seconds.');

        $response = $this->get('/status');
        $response->assertStatus(200);

        $sample_data = [];
        for ($i=0; $i < pow(10, 4); $i++)
        {
            $sample_data[] = [
                'id' => $i+1,
                'seats' => mt_rand(4, 6),
            ];
        }

        $response = $this->putJson('/cars', $sample_data);
        $response->assertStatus(200);

        for ($i=0; $i < pow(10, 5); $i++)
        {
            $journey = [
                'id' => $i+1,
                'people' => mt_rand(1, 6),
            ];

            $response = $this->postJson('/journey', $journey);
            $response->assertStatus(200);
        }
    }
}
