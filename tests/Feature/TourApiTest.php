<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\TourTranslation;
use App\Models\TourOperatorTranslation;
use Illuminate\Support\Str;

class TourApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the tour API endpoint returns the correct data structure
     * after removing the schedule-based logic.
     *
     * @return void
     */
    public function test_tour_api_returns_correct_structure_and_availability()
    {
        // 1. Arrange
        // Manually create a TourOperator and its translation
        $operator = TourOperator::create([
            'name' => 'Amazing Tours',
            'slug' => 'amazing-tours',
            'email' => 'contact@amazingtours.com',
            'status' => 'active',
        ]);
        TourOperatorTranslation::create([
            'tour_operator_id' => $operator->id,
            'locale' => 'fr',
            'name' => 'Tours Incroyables',
            'description' => "Description de l'opérateur."
        ]);

        // Manually create an available tour
        $availableTour = Tour::create([
            'tour_operator_id' => $operator->id,
            'slug' => 'available-tour-' . Str::uuid(),
            'type' => 'mixed',
            'status' => 'active',
            'max_participants' => 10,
            'current_participants' => 5,
            'price' => 100,
        ]);
        TourTranslation::create([
            'tour_id' => $availableTour->id,
            'locale' => 'fr',
            'title' => 'Tour Disponible',
            'description' => 'Une description.'
        ]);

        // Manually create a full tour
        $fullTour = Tour::create([
            'tour_operator_id' => $operator->id,
            'slug' => 'full-tour-' . Str::uuid(),
            'type' => 'mixed',
            'status' => 'active',
            'max_participants' => 10,
            'current_participants' => 10,
            'price' => 100,
        ]);
        TourTranslation::create([
            'tour_id' => $fullTour->id,
            'locale' => 'fr',
            'title' => 'Tour Complet',
            'description' => 'Une description.'
        ]);

        // 2. Act & Assert for Available Tour
        $responseAvailable = $this->getJson('/api/tours/' . $availableTour->id);

        $responseAvailable->assertStatus(200)
            ->assertJsonPath('data.tour.id', $availableTour->id)
            ->assertJsonPath('data.tour.available_spots', 5)
            ->assertJsonMissingPath('data.tour.next_available_date');

        // 3. Act & Assert for Full Tour
        $responseFull = $this->getJson('/api/tours/' . $fullTour->id);

        $responseFull->assertStatus(200)
            ->assertJsonPath('data.tour.id', $fullTour->id)
            ->assertJsonPath('data.tour.available_spots', 0)
            ->assertJsonMissingPath('data.tour.next_available_date');
    }
}
