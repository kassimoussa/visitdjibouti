<?php

namespace Database\Seeders;

use App\Models\TourOperator;
use App\Models\TourOperatorTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourOperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Données d'exemple simplifiées pour les opérateurs de tour
        $operators = [
            [
                'phones' => '+253 21 35 40 50|+253 77 89 12 34',
                'emails' => 'info@djiboutiadventures.com|contact@djiboutiadventures.com',
                'website' => 'www.djiboutiadventures.com',
                'address' => 'Avenue Hassan Gouled Aptidon, Djibouti',
                'latitude' => 11.5721,
                'longitude' => 43.1456,
                'is_active' => true,
                'featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Djibouti Adventures',
                        'description' => 'Spécialisé dans les excursions désertiques et les plongées dans la mer Rouge. Découvrez les merveilles naturelles de Djibouti avec nos guides experts locaux.',
                        'address_translated' => 'Avenue Hassan Gouled Aptidon, Djibouti',
                    ],
                    'en' => [
                        'name' => 'Djibouti Adventures',
                        'description' => 'Specialized in desert expeditions and Red Sea diving. Discover the natural wonders of Djibouti with our expert local guides.',
                        'address_translated' => 'Hassan Gouled Aptidon Avenue, Djibouti',
                    ],
                    'ar' => [
                        'name' => 'مغامرات جيبوتي',
                        'description' => 'متخصص في رحلات الصحراء والغوص في البحر الأحمر. اكتشف عجائب جيبوتي الطبيعية مع مرشدينا الخبراء المحليين.',
                        'address_translated' => 'شارع حسن جوليد أبتيدون، جيبوتي',
                    ],
                ],
            ],
            [
                'phones' => '+253 21 36 42 18',
                'emails' => 'contact@redseatours.dj',
                'website' => 'redseatours.dj',
                'address' => 'Plateau du Serpent, Djibouti Ville',
                'latitude' => 11.5884,
                'longitude' => 43.1452,
                'is_active' => true,
                'featured' => false,
                'translations' => [
                    'fr' => [
                        'name' => 'Red Sea Tours',
                        'description' => 'Explorez la beauté sous-marine de la mer Rouge et les paysages volcaniques uniques de Djibouti.',
                        'address_translated' => 'Plateau du Serpent, Djibouti Ville',
                    ],
                    'en' => [
                        'name' => 'Red Sea Tours',
                        'description' => 'Explore the underwater beauty of the Red Sea and the unique volcanic landscapes of Djibouti.',
                        'address_translated' => 'Serpent Plateau, Djibouti City',
                    ],
                ],
            ],
            [
                'phones' => '+253 21 35 78 90|+253 77 65 43 21',
                'emails' => 'info@nomadtours.dj',
                'website' => 'nomadtours.dj',
                'address' => 'Quartier Balbala, Djibouti',
                'latitude' => 11.5458,
                'longitude' => 43.1289,
                'is_active' => true,
                'featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Nomad Tours',
                        'description' => 'Découvrez la culture nomade authentique et les traditions ancestrales des peuples Afar et Somali.',
                        'address_translated' => 'Quartier Balbala, Djibouti',
                    ],
                    'en' => [
                        'name' => 'Nomad Tours',
                        'description' => 'Discover authentic nomadic culture and ancestral traditions of Afar and Somali peoples.',
                        'address_translated' => 'Balbala District, Djibouti',
                    ],
                ],
            ],
            [
                'phones' => '+253 21 37 25 63',
                'emails' => 'bookings@luxurydjibouti.com|vip@luxurydjibouti.com',
                'website' => 'www.luxurydjibouti.com',
                'address' => 'Les Plateaux, Djibouti Ville',
                'latitude' => 11.5951,
                'longitude' => 43.1480,
                'is_active' => true,
                'featured' => true,
                'translations' => [
                    'fr' => [
                        'name' => 'Luxury Djibouti',
                        'description' => 'Expériences haut de gamme et sur mesure pour découvrir Djibouti dans le confort et l\'exclusivité.',
                        'address_translated' => 'Les Plateaux, Djibouti Ville',
                    ],
                    'en' => [
                        'name' => 'Luxury Djibouti',
                        'description' => 'High-end and customized experiences to discover Djibouti in comfort and exclusivity.',
                        'address_translated' => 'Les Plateaux, Djibouti City',
                    ],
                ],
            ],
            [
                'phones' => '+253 21 34 89 12|+253 77 12 34 56',
                'emails' => 'discover@danakiltours.dj',
                'website' => 'danakiltours.dj',
                'address' => 'Route de Tadjourah, Djibouti',
                'latitude' => 11.5678,
                'longitude' => 43.1234,
                'is_active' => true,
                'featured' => false,
                'translations' => [
                    'fr' => [
                        'name' => 'Danakil Tours',
                        'description' => 'Spécialiste des expéditions dans la dépression du Danakil et les volcans actifs de la région.',
                        'address_translated' => 'Route de Tadjourah, Djibouti',
                    ],
                    'en' => [
                        'name' => 'Danakil Tours',
                        'description' => 'Specialist in expeditions to the Danakil Depression and active volcanoes of the region.',
                        'address_translated' => 'Tadjourah Road, Djibouti',
                    ],
                ],
            ],
        ];

        foreach ($operators as $operatorData) {
            // Créer le slug unique
            $baseSlug = Str::slug($operatorData['translations']['fr']['name']);
            $slug = $baseSlug;
            $counter = 1;
            while (TourOperator::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }

            // Extraire les données de traduction
            $translations = $operatorData['translations'];
            unset($operatorData['translations']);

            // Créer l'opérateur
            $operator = TourOperator::create(array_merge($operatorData, ['slug' => $slug]));

            // Créer les traductions
            foreach ($translations as $locale => $translationData) {
                $operator->translations()->create(array_merge($translationData, [
                    'locale' => $locale,
                ]));
            }
        }

        $this->command->info('Tour operators seeded successfully!');
    }
}