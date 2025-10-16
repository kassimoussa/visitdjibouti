<?php

namespace Tests\Feature;

use App\Models\AppUser;
use App\Models\Event;
use App\Models\EventTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = AppUser::factory()->create();
    }

    private function createEventWithTranslation(array $attributes): Event
    {
        $event = Event::factory()->create($attributes);
        EventTranslation::factory()->create(['event_id' => $event->id, 'locale' => 'fr']);
        return $event;
    }

    /**
     * Test registration for a free event with multiple participants.
     * This test will fail initially because the participant count is not updated correctly.
     */
    public function test_registration_for_free_event_updates_participant_count_correctly()
    {
        // Arrange
        $event = $this->createEventWithTranslation([
            'price' => 0,
            'max_participants' => 10,
            'current_participants' => 2,
        ]);
        $reservationData = ['participants_count' => 3];

        // Act
        $this->actingAs($this->user, 'sanctum')
             ->postJson("/api/events/{$event->id}/register", $reservationData);

        // Assert
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'current_participants' => 5 // 2 initial + 3 reserved
        ]);
    }

    /**
     * Test registration for a paid event updates participant count correctly.
     */
    public function test_registration_for_paid_event_updates_count_correctly()
    {
        // Arrange
        $event = $this->createEventWithTranslation([
            'price' => 50,
            'max_participants' => 10,
            'current_participants' => 4,
        ]);
        $reservationData = ['participants_count' => 2];

        // Act
        $this->actingAs($this->user, 'sanctum')
             ->postJson("/api/events/{$event->id}/register", $reservationData);

        // Assert: The count should now be correctly updated
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'current_participants' => 6 // 4 initial + 2 reserved
        ]);
    }

    /**
     * Test cancellation correctly decrements participant count.
     * This test will fail initially because the count is decremented by 1 instead of the reservation's participant_count.
     */
    public function test_cancellation_decrements_participant_count_correctly()
    {
        // Arrange
        $event = $this->createEventWithTranslation([
            'max_participants' => 20,
            'current_participants' => 8,
        ]);
        // Manually create a reservation for this user with 4 people
        $reservation = $event->reservations()->create([
            'app_user_id' => $this->user->id,
            'number_of_people' => 4,
            'status' => 'confirmed',
            'reservation_date' => $event->start_date, // Add reservation date
        ]);

        // Act: Cancel the registration
        $this->actingAs($this->user, 'sanctum')
             ->deleteJson("/api/events/{$event->id}/registration");

        // Assert
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'current_participants' => 4 // 8 initial - 4 cancelled
        ]);
    }

    /**
     * Test user cannot register for a full event.
     */
    public function test_user_cannot_register_for_full_event()
    {
        // Arrange
        $event = $this->createEventWithTranslation([
            'max_participants' => 5,
            'current_participants' => 4,
        ]);
        // Try to book 2 spots when only 1 is left
        $reservationData = ['participants_count' => 2];

        // Act
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/events/{$event->id}/register", $reservationData);

        // Assert
        $response->assertStatus(422);
    }
}
