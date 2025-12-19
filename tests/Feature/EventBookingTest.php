<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventBookingTest extends TestCase
{
    use RefreshDatabase;


    

    public function test_user_can_book_event()
    {
        $user = User::factory()->create();

        $event = Event::factory()->create([
            'available_seats' => 10,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/events/{$event->id}/book");

        $response
            ->assertStatus(201)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $this->assertEquals(9, $event->fresh()->available_seats);
    }


    public function test_cannot_book_event_when_no_seats_left()
    {
        $user = User::factory()->create();

        $event = Event::factory()->create([
            'available_seats' => 0,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/events/{$event->id}/book");

        $response
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
            ]);
    }


    public function test_user_cannot_book_same_event_twice()
    {
        $user = User::factory()->create();

        $event = Event::factory()->create([
            'available_seats' => 10,
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->postJson("/api/events/{$event->id}/book");

        $response->assertStatus(409);
    }
}
