<?php

namespace Database\Factories;

use App\Models\EventTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTranslationFactory extends Factory
{
    protected $model = EventTranslation::class;

    public function definition()
    {
        return [
            'locale' => 'fr',
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'short_description' => $this->faker->paragraph(1),
        ];
    }
}
