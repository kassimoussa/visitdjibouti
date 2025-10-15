<?php

namespace Database\Seeders;

use App\Models\TourOperator;
use App\Models\TourOperatorUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TourOperatorUsersSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que nous avons des tour operators
        $tourOperators = TourOperator::all();

        if ($tourOperators->isEmpty()) {
            $this->command->info('Aucun tour operator trouvé. Exécutez d\'abord TestTourOperatorSeeder.');

            return;
        }

        $this->command->info('Création des utilisateurs tour operators...');

        // Créer des utilisateurs pour Djibouti Tours
        $djiboutiTours = $tourOperators->first();

        // Directeur
        TourOperatorUser::create([
            'tour_operator_id' => $djiboutiTours->id,
            'name' => 'Ahmed Mohamed Hassan',
            'email' => 'ahmed.hassan@djiboutitours.dj',
            'password' => Hash::make('password123'),
            'phone_number' => '+253 77 84 23 45',
            'position' => 'Directeur Général',
            'language_preference' => 'fr',
            'permissions' => [
                'manage_events' => true,
                'manage_tours' => true,
                'view_reservations' => true,
                'manage_reservations' => true,
                'view_reports' => true,
                'manage_profile' => true,
            ],
            'is_active' => true,
        ]);

        // Manager
        TourOperatorUser::create([
            'tour_operator_id' => $djiboutiTours->id,
            'name' => 'Fatima Ali Abdou',
            'email' => 'fatima.ali@djiboutitours.dj',
            'password' => Hash::make('password123'),
            'phone_number' => '+253 77 12 34 56',
            'position' => 'Manager Opérations',
            'language_preference' => 'fr',
            'permissions' => [
                'manage_events' => true,
                'manage_tours' => true,
                'view_reservations' => true,
                'manage_reservations' => true,
                'view_reports' => false,
                'manage_profile' => true,
            ],
            'is_active' => true,
        ]);

        // Guide
        TourOperatorUser::create([
            'tour_operator_id' => $djiboutiTours->id,
            'name' => 'Omar Said Djama',
            'email' => 'omar.djama@djiboutitours.dj',
            'password' => Hash::make('password123'),
            'phone_number' => '+253 77 98 76 54',
            'position' => 'Guide Principal',
            'language_preference' => 'fr',
            'permissions' => [
                'manage_events' => false,
                'manage_tours' => true,
                'view_reservations' => true,
                'manage_reservations' => false,
                'view_reports' => false,
                'manage_profile' => true,
            ],
            'is_active' => true,
        ]);

        // Si nous avons un second tour operator, créer ses utilisateurs
        if ($tourOperators->count() > 1) {
            $redSeaAdventures = $tourOperators->skip(1)->first();

            // Directeur Red Sea Adventures
            TourOperatorUser::create([
                'tour_operator_id' => $redSeaAdventures->id,
                'name' => 'Mohamed Youssouf Adan',
                'email' => 'mohamed.adan@redseaadventures.dj',
                'password' => Hash::make('password123'),
                'phone_number' => '+253 77 55 44 33',
                'position' => 'Directeur',
                'language_preference' => 'en',
                'permissions' => [
                    'manage_events' => true,
                    'manage_tours' => true,
                    'view_reservations' => true,
                    'manage_reservations' => true,
                    'view_reports' => true,
                    'manage_profile' => true,
                ],
                'is_active' => true,
            ]);

            // Coordinateur
            TourOperatorUser::create([
                'tour_operator_id' => $redSeaAdventures->id,
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@redseaadventures.dj',
                'password' => Hash::make('password123'),
                'phone_number' => '+253 77 11 22 33',
                'position' => 'Coordinatrice Plongée',
                'language_preference' => 'en',
                'permissions' => [
                    'manage_events' => true,
                    'manage_tours' => true,
                    'view_reservations' => true,
                    'manage_reservations' => true,
                    'view_reports' => false,
                    'manage_profile' => true,
                ],
                'is_active' => true,
            ]);
        }

        $this->command->info('✅ Utilisateurs tour operators créés avec succès !');
        $this->command->table(
            ['Tour Operator', 'Nom', 'Email', 'Poste'],
            [
                ['Djibouti Tours', 'Ahmed Mohamed Hassan', 'ahmed.hassan@djiboutitours.dj', 'Directeur Général'],
                ['Djibouti Tours', 'Fatima Ali Abdou', 'fatima.ali@djiboutitours.dj', 'Manager Opérations'],
                ['Djibouti Tours', 'Omar Said Djama', 'omar.djama@djiboutitours.dj', 'Guide Principal'],
                ['Red Sea Adventures', 'Mohamed Youssouf Adan', 'mohamed.adan@redseaadventures.dj', 'Directeur'],
                ['Red Sea Adventures', 'Sarah Johnson', 'sarah.johnson@redseaadventures.dj', 'Coordinatrice Plongée'],
            ]
        );
        $this->command->info('Mot de passe pour tous les comptes: password123');
    }
}
