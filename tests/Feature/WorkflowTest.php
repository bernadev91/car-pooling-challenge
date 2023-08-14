<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkflowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the normal scenario with the sample data provided
     */
    public function test_normal(): void
    {
        $response = $this->get('/status');
        $response->assertStatus(200);

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

        $response = $this->postJson('/journey', [
            'id' => 1,
            'people' => 4,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/locate', ['ID' => 1]);
        $response->assertStatus(200);

        $this->assertNotEmpty($response->json());
    }

    /**
     * Test the scenario when there are no cars large enough to
     * accommodate this group
     */
    public function test_doesnt_fit(): void
    {
        $response = $this->get('/status');
        $response->assertStatus(200);

        $sample_data = [
            [
                'id' => 1,
                'seats' => 4
            ],
        ];

        $response = $this->putJson('/cars', $sample_data);
        $response->assertStatus(200);

        $response = $this->postJson('/journey', [
            'id' => 1,
            'people' => 5,
        ]);
        $response->assertStatus(400);

        $response = $this->post('/locate', ['ID' => 1]);
        $response->assertStatus(404);
    }

    /**
     * Test the scenario when a group has to wait
     * accommodate this group
     */
    public function test_after_dropoff(): void
    {
        $response = $this->get('/status');
        $response->assertStatus(200);

        $sample_data = [
            [
                'id' => 1,
                'seats' => 4
            ],
        ];

        $response = $this->putJson('/cars', $sample_data);
        $response->assertStatus(200);

        $response = $this->postJson('/journey', [
            'id' => 1,
            'people' => 4,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/locate', ['ID' => 1]);
        $response->assertStatus(200);

        $this->assertNotEmpty($response->json());

        $response = $this->postJson('/journey', [
            'id' => 2,
            'people' => 4,
        ]);
        $response->assertStatus(200);

        $response = $this->post('/locate', ['ID' => 2]);
        $response->assertStatus(204);

        $response = $this->post('/dropoff', ['ID' => 1]);
        $response->assertStatus(200);

        $response = $this->post('/locate', ['ID' => 2]);
        $response->assertStatus(200);

        $this->assertNotEmpty($response->json());
    }
}