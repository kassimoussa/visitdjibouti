<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\TourOperator;
use App\Models\TourOperatorUser;
use App\Models\TourOperatorTranslation;
use App\Models\Event;
use App\Models\EventTranslation;

class TourOperatorSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test tour operator
        $tourOperator = TourOperator::create([
            'slug' => 'djibouti-adventures',
            'phones' => '+253 77 12 34 56|+253 21 12 34 56',
            'emails' => 'contact@djibouti-adventures.dj|info@djibouti-adventures.dj',
            'website' => 'https://djibouti-adventures.dj',
            'address' => 'Rue de la République, Djibouti',
            'latitude' => 11.5720,
            'longitude' => 43.1456,
            'is_active' => true,
            'featured' => true,
        ]);

        // Add translations for the tour operator
        TourOperatorTranslation::create([
            'tour_operator_id' => $tourOperator->id,
            'locale' => 'fr',
            'name' => 'Djibouti Adventures',
            'description' => 'Votre partenaire de confiance pour découvrir les merveilles de Djibouti. Nous proposons des tours personnalisés, des excursions en groupe et des événements spéciaux.',
            'address_translated' => 'Rue de la République, Djibouti',
        ]);

        TourOperatorTranslation::create([
            'tour_operator_id' => $tourOperator->id,
            'locale' => 'en',
            'name' => 'Djibouti Adventures',
            'description' => 'Your trusted partner to discover the wonders of Djibouti. We offer personalized tours, group excursions and special events.',
            'address_translated' => 'République Street, Djibouti',
        ]);

        TourOperatorTranslation::create([
            'tour_operator_id' => $tourOperator->id,
            'locale' => 'ar',
            'name' => 'مغامرات جيبوتي',
            'description' => 'شريكك الموثوق لاكتشاف عجائب جيبوتي. نحن نقدم جولات مخصصة ورحلات جماعية وفعاليات خاصة.',
            'address_translated' => 'شارع الجمهورية، جيبوتي',
        ]);

        // Create test tour operator users
        $operatorUser = TourOperatorUser::create([
            'tour_operator_id' => $tourOperator->id,
            'name' => 'Ahmed Hassan',
            'email' => 'ahmed@djibouti-adventures.dj',
            'password' => Hash::make('password123'),
            'phone_number' => '+253 77 12 34 56',
            'position' => 'Responsable des opérations',
            'language_preference' => 'fr',
            'permissions' => ['all'],
            'is_active' => true,
        ]);

        $operatorUser2 = TourOperatorUser::create([
            'tour_operator_id' => $tourOperator->id,
            'name' => 'Sarah Mohamed',
            'email' => 'sarah@djibouti-adventures.dj',
            'password' => Hash::make('password123'),
            'phone_number' => '+253 77 65 43 21',
            'position' => 'Responsable événements',
            'language_preference' => 'fr',
            'permissions' => ['manage_events', 'view_reservations'],
            'is_active' => true,
        ]);

        // Create a test event managed by this tour operator
        $event = Event::create([
            'slug' => 'festival-lac-assal-2024',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(32),
            'start_time' => '09:00',
            'end_time' => '18:00',
            'location' => 'Lac Assal',
            'latitude' => 11.6556,
            'longitude' => 42.4167,
            'contact_email' => 'events@djibouti-adventures.dj',
            'contact_phone' => '+253 77 12 34 56',
            'website_url' => 'https://djibouti-adventures.dj/events/lac-assal-festival',
            'price' => 15000,
            'max_participants' => 100,
            'current_participants' => 0,
            'organizer' => 'Djibouti Adventures',
            'is_featured' => true,
            'status' => 'published',
            'creator_id' => null, // Created by admin but assigned to operator
            'tour_operator_id' => $tourOperator->id, // Managed by tour operator
            'views_count' => 0,
        ]);

        // Add translations for the event
        EventTranslation::create([
            'event_id' => $event->id,
            'locale' => 'fr',
            'title' => 'Festival du Lac Assal 2024',
            'description' => 'Rejoignez-nous pour une aventure inoubliable au lac Assal, le point le plus bas d\'Afrique et l\'un des lacs les plus salés au monde. Une expérience unique vous attend avec des activités culturelles, des dégustations locales et des visites guidées.',
            'short_description' => 'Aventure exceptionnelle au lac Assal avec activités culturelles et visites guidées.',
            'location_details' => 'Lac Assal, Région de Tadjourah',
            'requirements' => 'Chaussures de marche, chapeau, crème solaire, bouteille d\'eau',
            'program' => "09:00 - Départ de Djibouti\n11:00 - Arrivée au lac Assal\n11:30 - Visite guidée et explications géologiques\n13:00 - Déjeuner traditionnel\n14:30 - Activités libres et baignade\n16:00 - Dégustation de sel local\n17:00 - Retour vers Djibouti\n18:30 - Arrivée à Djibouti",
            'additional_info' => 'Transport en 4x4 climatisé inclus. Déjeuner traditionnel djiboutien inclus.',
        ]);

        EventTranslation::create([
            'event_id' => $event->id,
            'locale' => 'en',
            'title' => 'Lake Assal Festival 2024',
            'description' => 'Join us for an unforgettable adventure at Lake Assal, the lowest point in Africa and one of the saltiest lakes in the world. A unique experience awaits you with cultural activities, local tastings and guided tours.',
            'short_description' => 'Exceptional adventure at Lake Assal with cultural activities and guided tours.',
            'location_details' => 'Lake Assal, Tadjourah Region',
            'requirements' => 'Walking shoes, hat, sunscreen, water bottle',
            'program' => "09:00 - Departure from Djibouti\n11:00 - Arrival at Lake Assal\n11:30 - Guided tour and geological explanations\n13:00 - Traditional lunch\n14:30 - Free activities and swimming\n16:00 - Local salt tasting\n17:00 - Return to Djibouti\n18:30 - Arrival in Djibouti",
            'additional_info' => 'Transport in air-conditioned 4x4 included. Traditional Djiboutian lunch included.',
        ]);

        $this->command->info('Tour Operator System test data created successfully!');
        $this->command->info('Test accounts:');
        $this->command->info('- ahmed@djibouti-adventures.dj (password: password123) - Full permissions');
        $this->command->info('- sarah@djibouti-adventures.dj (password: password123) - Events only');
        $this->command->info('Tour Operator: Djibouti Adventures');
        $this->command->info('Test Event: Festival du Lac Assal 2024');
    }
}