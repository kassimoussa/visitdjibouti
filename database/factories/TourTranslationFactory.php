<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TourTranslationFactory extends Factory
{
    protected $model = TourTranslation::class;

    public function definition()
    {
        return [
            'tour_id' => Tour::factory(),
            'locale' => 'fr',
            'title' => $this->faker->sentence(5),
            'description' => $this->faker->paragraph(4),
        ];
    }
}
