<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EventTest extends TestCase
{

    use RefreshDatabase;
    public function test_admin_can_create_event()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $category = Category::factory()->create();

        $payload = [
            'title' => 'Tech Conference',
            'desc' => 'Annual tech event',
            'date' => '2026-05-01',
            'available_seats' => 100,
            'category_id' => $category->id,
        ];

        $response = $this
            ->actingAs($admin, 'sanctum')
            ->postJson('/api/events', $payload);

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Event created successfully',
            ]);

        $this->assertDatabaseHas('events', [
            'title' => 'Tech Conference',
            'category_id' => $category->id,
        ]);
    }

    public function test_user_cannot_create_event()
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $category = Category::factory()->create();

        $payload = [
            'title' => 'Unauthorized Event',
            'desc' => 'Should fail',
            'date' => '2026-06-01',
            'available_seats' => 10,
            'category_id' => $category->id,
        ];

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/events', $payload);

        $response->assertStatus(403);
    }
}
