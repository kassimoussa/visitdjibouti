<?php

namespace Database\Factories;

use App\Models\TourOperatorUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class TourOperatorUserFactory extends Factory
{
    protected $model = TourOperatorUser::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'is_active' => true,
        ];
    }
}