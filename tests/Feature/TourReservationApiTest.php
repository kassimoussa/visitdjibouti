<?php

namespace Tests\Feature;

use App\Models\AppUser;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\TourOperatorTranslation;
use App\Models\TourTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TourReservationApiTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $operator;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user to act as the one making the reservation
        $this->user = AppUser::factory()->create();

        // Create a tour operator
        $this->operator = TourOperator::create([
            'name' => 'Operator', 'slug' => 'operator', 'email' => 'op@test.com', 'status' => 'active'
        ]);
        TourOperatorTranslation::create([
            'tour_operator_id' => $this->operator->id, 'locale' => 'fr', 'name' => 'Opérateur'
        ]);
    }

    /**
     * Test that a user can successfully book a tour with available spots.
     */
    public function test_user_can_book_an_available_tour()
    {
        // Arrange: Create a tour with available spots
        $tour = Tour::create([
            'tour_operator_id' => $this->operator->id,
            'slug' => 'available-tour', 'type' => 'mixed', 'status' => 'active',
            'max_participants' => 10,
            'current_participants' => 5,
            'price' => 100,
        ]);
        TourTranslation::create(['tour_id' => $tour->id, 'locale' => 'fr', 'title' => 'Tour Test', 'description' => 'Description pour le tour de test.']);

        $reservationData = ['number_of_people' => 2];

        // Act: Make the reservation request
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/tour-reservations/{$tour->id}/register", $reservationData);

        // Assert
        $response->assertStatus(201)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('reservation.tour_id', $tour->id)
                 ->assertJsonPath('reservation.app_user_id', $this->user->id)
                 ->assertJsonPath('reservation.number_of_people', 2);

        $this->assertDatabaseHas('tour_reservations', [
            'tour_id' => $tour->id,
            'app_user_id' => $this->user->id,
            'number_of_people' => 2,
        ]);

        // This assertion will fail initially because the code doesn't update the count
        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'current_participants' => 7 // 5 initial + 2 reserved
        ]);
    }

    /**
     * Test that a user cannot book a tour that is full.
     * This test is expected to FAIL initially.
     */
    public function test_user_cannot_book_a_full_tour()
    {
        // Arrange: Create a tour with no available spots
        $tour = Tour::create([
            'tour_operator_id' => $this->operator->id,
            'slug' => 'full-tour', 'type' => 'mixed', 'status' => 'active',
            'max_participants' => 10,
            'current_participants' => 10, // Tour is full
            'price' => 100,
        ]);
        TourTranslation::create(['tour_id' => $tour->id, 'locale' => 'fr', 'title' => 'Tour Complet', 'description' => 'Description pour le tour complet.']);

        $reservationData = ['number_of_people' => 1];

        // Act: Make the reservation request
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/tour-reservations/{$tour->id}/register", $reservationData);

        // Assert: This assertion will FAIL initially. The code should return a 422 or 400 error.
        $response->assertStatus(422)
                 ->assertJsonPath('success', false);

        $this->assertDatabaseMissing('tour_reservations', [
            'tour_id' => $tour->id,
        ]);

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'current_participants' => 10 // Count should not have changed
        ]);
    }
}
