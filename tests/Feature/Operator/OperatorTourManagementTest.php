<?php

namespace Tests\Feature\Operator;

use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\TourOperatorUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperatorTourManagementTest extends TestCase
{
    use RefreshDatabase;

    private $tourOperator;
    private $operatorUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a tour operator and a user for it
        $this->tourOperator = TourOperator::factory()->create();
        $this->operatorUser = TourOperatorUser::factory()->create([
            'tour_operator_id' => $this->tourOperator->id,
        ]);
    }

    /**
     * Test operator can view the create tour page.
     */
    public function test_operator_can_view_create_tour_page()
    {
        $response = $this->actingAs($this->operatorUser, 'operator')->get(route('operator.tours.create'));
        $response->assertStatus(200);
        $response->assertViewIs('operator.tours.create');
    }

    /**
     * Test operator can create a new tour.
     */
    public function test_operator_can_create_a_tour()
    {
        $tourData = [
            'status' => 'active',
            'type' => 'mixed',
            'price' => 10000,
            'max_participants' => 20,
            'translations' => [
                'fr' => [
                    'title' => 'Nouveau Tour de Test',
                    'description' => 'Description du nouveau tour.',
                ],
            ],
        ];

        $response = $this->actingAs($this->operatorUser, 'operator')
                         ->post(route('operator.tours.store'), $tourData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tours', [
            'tour_operator_id' => $this->tourOperator->id,
            'price' => 10000,
        ]);
        $this->assertDatabaseHas('tour_translations', [
            'title' => 'Nouveau Tour de Test',
        ]);
    }

    /**
     * Test operator can update their own tour.
     */
    public function test_operator_can_update_own_tour()
    {
        $tour = Tour::factory()->create(['tour_operator_id' => $this->tourOperator->id]);

        $updateData = [
            'status' => 'inactive',
            'type' => 'cultural',
            'price' => 15000,
            'translations' => [
                'fr' => [
                    'title' => 'Titre du Tour Mis à Jour',
                    'description' => 'Description mise à jour.',
                ],
            ],
        ];

        $response = $this->actingAs($this->operatorUser, 'operator')
                         ->put(route('operator.tours.update', $tour), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'status' => 'inactive',
            'price' => 15000,
        ]);
        $this->assertDatabaseHas('tour_translations', [
            'tour_id' => $tour->id,
            'title' => 'Titre du Tour Mis à Jour',
        ]);
    }

    /**
     * Test operator cannot update a tour from another operator.
     */
    public function test_operator_cannot_update_another_operators_tour()
    {
        // Create another operator and their tour
        $otherTourOperator = TourOperator::factory()->create();
        $otherTour = Tour::factory()->create(['tour_operator_id' => $otherTourOperator->id]);

        $updateData = [
            'status' => 'active',
            'type' => 'adventure',
            'price' => 999,
            'translations' => [
                'fr' => [
                    'title' => 'Titre Piraté',
                    'description' => 'Description piratée.',
                ],
            ],
        ];

        $response = $this->actingAs($this->operatorUser, 'operator')
                         ->put(route('operator.tours.update', $otherTour), $updateData);

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseMissing('tour_translations', [
            'title' => 'Titre Piraté',
        ]);
    }
}