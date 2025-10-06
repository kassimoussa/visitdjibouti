<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourOperator;

class TestTourOperatorSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un tour operator de test
        $tourOperator = TourOperator::create([
            'slug' => 'djibouti-tours',
            'phones' => '+253 21 35 14 00|+253 77 84 23 45',
            'emails' => 'contact@djiboutitours.dj|info@djiboutitours.dj',
            'website' => 'https://djiboutitours.dj',
            'address' => 'Plateau du Serpent, Djibouti Ville',
            'latitude' => 11.5721,
            'longitude' => 43.1456,
            'is_active' => true,
            'featured' => true,
        ]);

        // Créer les traductions
        $tourOperator->translations()->create([
            'locale' => 'fr',
            'name' => 'Djibouti Tours',
            'description' => 'Agence de voyage spécialisée dans les circuits découverte de Djibouti. Explorez les merveilles naturelles du pays avec nos guides expérimentés.',
            'address_translated' => 'Plateau du Serpent, Djibouti Ville',
        ]);

        $tourOperator->translations()->create([
            'locale' => 'en',
            'name' => 'Djibouti Tours',
            'description' => 'Travel agency specialized in discovery tours of Djibouti. Explore the natural wonders of the country with our experienced guides.',
            'address_translated' => 'Plateau du Serpent, Djibouti City',
        ]);

        $tourOperator->translations()->create([
            'locale' => 'ar',
            'name' => 'جولات جيبوتي',
            'description' => 'وكالة سفر متخصصة في جولات اكتشاف جيبوتي. اكتشف عجائب الطبيعة في البلاد مع مرشدينا ذوي الخبرة.',
            'address_translated' => 'هضبة الثعبان، مدينة جيبوتي',
        ]);

        // Créer un second tour operator
        $tourOperator2 = TourOperator::create([
            'slug' => 'red-sea-adventures',
            'phones' => '+253 21 35 20 15',
            'emails' => 'contact@redseaadventures.dj',
            'website' => 'https://redseaadventures.dj',
            'address' => 'Héron, Djibouti Ville',
            'latitude' => 11.5750,
            'longitude' => 43.1480,
            'is_active' => true,
            'featured' => false,
        ]);

        $tourOperator2->translations()->create([
            'locale' => 'fr',
            'name' => 'Red Sea Adventures',
            'description' => 'Spécialiste des aventures marines et terrestres en mer Rouge. Plongée, snorkeling et excursions dans la nature.',
            'address_translated' => 'Héron, Djibouti Ville',
        ]);

        $tourOperator2->translations()->create([
            'locale' => 'en',
            'name' => 'Red Sea Adventures',
            'description' => 'Specialist in marine and terrestrial adventures in the Red Sea. Diving, snorkeling and nature excursions.',
            'address_translated' => 'Héron, Djibouti City',
        ]);

        $tourOperator2->translations()->create([
            'locale' => 'ar',
            'name' => 'مغامرات البحر الأحمر',
            'description' => 'متخصص في المغامرات البحرية والبرية في البحر الأحمر. الغوص والسباحة والرحلات في الطبيعة.',
            'address_translated' => 'الهرون، مدينة جيبوتي',
        ]);

        echo "✅ Tour operators de test créés avec succès !\n";
        echo "- Djibouti Tours (ID: {$tourOperator->id})\n";
        echo "- Red Sea Adventures (ID: {$tourOperator2->id})\n";
    }
}