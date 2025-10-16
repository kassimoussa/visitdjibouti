<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourOperator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TourFactory extends Factory
{
    protected $model = Tour::class;

    public function definition()
    {
        return [
            'tour_operator_id' => TourOperator::factory(),
            'slug' => Str::slug($this->faker->unique()->sentence(4)),
            'type' => 'mixed',
            'status' => 'active',
            'price' => $this->faker->numberBetween(5000, 50000),
            'max_participants' => $this->faker->numberBetween(10, 30),
        ];
    }
}
