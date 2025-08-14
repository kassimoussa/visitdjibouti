<?php

namespace Database\Factories;

use App\Models\AppUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppUser>
 */
class AppUserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AppUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['male', 'female', 'other']);
        $firstName = fake()->firstName($gender === 'other' ? null : $gender);
        $lastName = fake()->lastName();
        $name = $firstName . ' ' . $lastName;

        $cities = ['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'];
        $languages = ['fr', 'en', 'ar'];
        $providers = ['email', 'google', 'facebook'];
        $provider = fake()->randomElement($providers);

        return [
            'name' => $name,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => fake()->optional(0.8)->dateTime(),
            'password' => $provider === 'email' ? Hash::make('password') : null,
            'phone' => fake()->optional(0.7)->numerify('+253 ## ## ## ##'),
            'avatar' => fake()->optional(0.3)->imageUrl(200, 200, 'people'),
            'date_of_birth' => fake()->optional(0.6)->dateTimeBetween('-60 years', '-16 years')->format('Y-m-d'),
            'gender' => $gender,
            'preferred_language' => fake()->randomElement($languages),
            'push_notifications_enabled' => fake()->boolean(80), // 80% chance true
            'email_notifications_enabled' => fake()->boolean(70), // 70% chance true
            'provider' => $provider,
            'provider_id' => $provider !== 'email' ? $provider . '_' . fake()->unique()->numerify('##########') : null,
            'city' => fake()->optional(0.8)->randomElement($cities),
            'country' => 'DJ',
            'is_active' => fake()->boolean(95), // 95% chance true
            'last_login_at' => fake()->optional(0.9)->dateTimeBetween('-1 month', 'now'),
            'last_login_ip' => fake()->optional(0.5)->ipv4(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the user is a social login user.
     */
    public function social(string $provider = null): static
    {
        $provider = $provider ?? fake()->randomElement(['google', 'facebook']);
        
        return $this->state(fn (array $attributes) => [
            'provider' => $provider,
            'provider_id' => $provider . '_' . fake()->unique()->numerify('##########'),
            'password' => null,
            'avatar' => fake()->imageUrl(200, 200, 'people'),
        ]);
    }

    /**
     * Indicate that the user prefers a specific language.
     */
    public function language(string $lang): static
    {
        return $this->state(fn (array $attributes) => [
            'preferred_language' => $lang,
        ]);
    }

    /**
     * Indicate that the user has verified their email.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }
}