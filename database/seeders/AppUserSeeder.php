<?php

namespace Database\Seeders;

use App\Models\AppUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AppUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateurs de test pour l'application mobile
        $testUsers = [
            [
                'name' => 'Ahmed Hassan',
                'email' => 'ahmed@test.dj',
                'password' => Hash::make('password123'),
                'phone' => '+253 21 35 40 50',
                'gender' => 'male',
                'date_of_birth' => '1990-05-15',
                'preferred_language' => 'fr',
                'city' => 'Djibouti',
                'provider' => 'email',
                'is_active' => true,
                'push_notifications_enabled' => true,
                'email_notifications_enabled' => true,
            ],
            [
                'name' => 'Fatima Omar',
                'email' => 'fatima@test.dj',
                'password' => Hash::make('password123'),
                'phone' => '+253 21 35 41 60',
                'gender' => 'female',
                'date_of_birth' => '1985-08-22',
                'preferred_language' => 'ar',
                'city' => 'Ali Sabieh',
                'provider' => 'email',
                'is_active' => true,
                'push_notifications_enabled' => false,
                'email_notifications_enabled' => true,
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@test.com',
                'password' => Hash::make('password123'),
                'phone' => '+253 21 35 42 70',
                'gender' => 'male',
                'date_of_birth' => '1988-12-10',
                'preferred_language' => 'en',
                'city' => 'Tadjourah',
                'provider' => 'email',
                'is_active' => true,
                'push_notifications_enabled' => true,
                'email_notifications_enabled' => false,
            ],
            [
                'name' => 'Sarah Google',
                'email' => 'sarah.google@test.com',
                'password' => null, // Connexion via Google
                'phone' => null,
                'gender' => 'female',
                'date_of_birth' => '1992-03-18',
                'preferred_language' => 'en',
                'city' => 'Obock',
                'provider' => 'google',
                'provider_id' => 'google_123456789',
                'avatar' => 'https://ui-avatars.com/api/?name=Sarah+Google&color=7F9CF5&background=EBF4FF',
                'is_active' => true,
                'push_notifications_enabled' => true,
                'email_notifications_enabled' => true,
            ],
            [
                'name' => 'Mohamed Facebook',
                'email' => 'mohamed.facebook@test.com',
                'password' => null, // Connexion via Facebook
                'phone' => '+253 21 35 43 80',
                'gender' => 'male',
                'date_of_birth' => '1987-07-05',
                'preferred_language' => 'fr',
                'city' => 'Dikhil',
                'provider' => 'facebook',
                'provider_id' => 'facebook_987654321',
                'avatar' => 'https://ui-avatars.com/api/?name=Mohamed+Facebook&color=3B82F6&background=DBEAFE',
                'is_active' => true,
                'push_notifications_enabled' => true,
                'email_notifications_enabled' => false,
            ],
        ];

        foreach ($testUsers as $userData) {
            AppUser::create($userData);
        }

        // Créer quelques utilisateurs supplémentaires avec des données aléatoires
        AppUser::factory(15)->create();
    }
}