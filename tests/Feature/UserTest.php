<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{

     use RefreshDatabase;
    public function test_guest_cannot_create_event()
    {
        $response = $this->postJson('/api/events', []);
        $response->assertStatus(401);
    }
    public function test_user_cannot_delete_event()
    {
        
        $user = User::factory()->create(['role' => 'user']);
        $event = Event::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/events/{$event->id}")
            ->assertStatus(403);
    }
}
