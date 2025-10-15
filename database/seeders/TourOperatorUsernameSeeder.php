<?php

namespace Database\Seeders;

use App\Models\TourOperator;
use App\Models\TourOperatorUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TourOperatorUsernameSeeder extends Seeder
{
    public function run(): void
    {
        // S'assurer que nous avons des tour operators
        $tourOperators = TourOperator::all();

        if ($tourOperators->isEmpty()) {
            $this->command->info('Aucun tour operator trouvé. Exécutez d\'abord TourOperatorSeeder.');

            return;
        }

        $this->command->info('Création des utilisateurs tour operators avec username/password...');

        // Supprimer les anciens utilisateurs s'ils existent
        TourOperatorUser::truncate();

        // Créer des utilisateurs pour le premier tour operator
        $firstOperator = $tourOperators->first();

        // Directeur avec accès complet
        TourOperatorUser::create([
            'tour_operator_id' => $firstOperator->id,
            'name' => 'Ahmed Mohamed Hassan',
            'username' => 'ahmed.hassan',
            'email' => 'ahmed.hassan@djiboutitours.dj',
            'password' => Hash::make('admin123'),
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

        // Manager opérationnel
        TourOperatorUser::create([
            'tour_operator_id' => $firstOperator->id,
            'name' => 'Fatima Ali Abdou',
            'username' => 'fatima.manager',
            'email' => 'fatima.ali@djiboutitours.dj',
            'password' => Hash::make('manager123'),
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

        // Guide avec accès limité
        TourOperatorUser::create([
            'tour_operator_id' => $firstOperator->id,
            'name' => 'Omar Said Djama',
            'username' => 'omar.guide',
            'email' => 'omar.djama@djiboutitours.dj',
            'password' => Hash::make('guide123'),
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

        // Si nous avons un second tour operator
        if ($tourOperators->count() > 1) {
            $secondOperator = $tourOperators->skip(1)->first();

            // Directeur Red Sea Adventures
            TourOperatorUser::create([
                'tour_operator_id' => $secondOperator->id,
                'name' => 'Mohamed Youssouf Adan',
                'username' => 'mohamed.director',
                'email' => 'mohamed.adan@redseaadventures.dj',
                'password' => Hash::make('director123'),
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

            // Coordinatrice
            TourOperatorUser::create([
                'tour_operator_id' => $secondOperator->id,
                'name' => 'Sarah Johnson',
                'username' => 'sarah.coordinator',
                'email' => 'sarah.johnson@redseaadventures.dj',
                'password' => Hash::make('coord123'),
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

        $this->command->info('✅ Utilisateurs tour operators créés avec username/password !');
        $this->command->newLine();
        $this->command->table(
            ['Tour Operator', 'Nom', 'Username', 'Mot de passe', 'Rôle'],
            [
                [$firstOperator->getTranslatedName('fr'), 'Ahmed Mohamed Hassan', 'ahmed.hassan', 'admin123', 'Directeur (accès complet)'],
                [$firstOperator->getTranslatedName('fr'), 'Fatima Ali Abdou', 'fatima.manager', 'manager123', 'Manager (pas de rapports)'],
                [$firstOperator->getTranslatedName('fr'), 'Omar Said Djama', 'omar.guide', 'guide123', 'Guide (lecture seule)'],
                [$tourOperators->count() > 1 ? $tourOperators->skip(1)->first()->getTranslatedName('en') : 'N/A', 'Mohamed Youssouf Adan', 'mohamed.director', 'director123', 'Directeur (accès complet)'],
                [$tourOperators->count() > 1 ? $tourOperators->skip(1)->first()->getTranslatedName('en') : 'N/A', 'Sarah Johnson', 'sarah.coordinator', 'coord123', 'Coordinatrice (pas de rapports)'],
            ]
        );

        $this->command->info('🔐 URL de connexion: /operator/login');
        $this->command->info('🌐 Les opérateurs peuvent utiliser soit leur username soit leur email pour se connecter');
    }
}
