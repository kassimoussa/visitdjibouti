<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('+1 week', '+2 week');
        $endDate = (clone $startDate)->modify('+' . $this->faker->numberBetween(0, 3) . ' days');

        return [
            'slug' => Str::slug($this->faker->unique()->sentence(3)),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'published',
            'allow_reservations' => true,
            'price' => 0,
            'max_participants' => $this->faker->numberBetween(10, 50),
            'current_participants' => $this->faker->numberBetween(0, 5),
        ];
    }
}
