<?php

namespace Database\Factories;

use App\Models\TourOperator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TourOperatorFactory extends Factory
{
    protected $model = TourOperator::class;

    public function definition()
    {
        return [
            'slug' => Str::slug($this->faker->unique()->company),
            'is_active' => true,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterCreating(function (TourOperator $operator) {
            $operator->translations()->create([
                'locale' => 'fr',
                'name' => $this->faker->company,
                'description' => $this->faker->paragraph,
            ]);
        });
    }
}
