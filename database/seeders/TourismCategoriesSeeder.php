<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Database\Seeder;

class TourismCategoriesSeeder extends Seeder
{
    public function run()
    {
        // Supprimer les catégories existantes pour un fresh start
        Category::query()->delete();

        // Structure des catégories principales avec leurs sous-catégories
        $categories = [
            [
                'name' => [
                    'fr' => 'À voir, À Faire',
                    'en' => 'To See, To Do',
                    'ar' => 'للمشاهدة والقيام به',
                ],
                'description' => [
                    'fr' => 'Sites touristiques, activités et attractions à découvrir',
                    'en' => 'Tourist sites, activities and attractions to discover',
                    'ar' => 'المواقع السياحية والأنشطة والمعالم التي يجب اكتشافها',
                ],
                'icon' => 'fas fa-compass',
                'color' => '#3498db',
                'children' => [
                    [
                        'name' => [
                            'fr' => 'Sites Naturels',
                            'en' => 'Natural Sites',
                            'ar' => 'المواقع الطبيعية',
                        ],
                        'description' => [
                            'fr' => 'Parcs nationaux, lacs, paysages naturels',
                            'en' => 'National parks, lakes, natural landscapes',
                            'ar' => 'الحدائق الوطنية والبحيرات والمناظر الطبيعية',
                        ],
                        'icon' => 'fas fa-mountain',
                        'color' => '#27ae60',
                    ],
                    [
                        'name' => [
                            'fr' => 'Sites Historiques',
                            'en' => 'Historical Sites',
                            'ar' => 'المواقع التاريخية',
                        ],
                        'description' => [
                            'fr' => 'Monuments historiques, sites archéologiques',
                            'en' => 'Historical monuments, archaeological sites',
                            'ar' => 'الآثار التاريخية والمواقع الأثرية',
                        ],
                        'icon' => 'fas fa-landmark',
                        'color' => '#8e44ad',
                    ],
                    [
                        'name' => [
                            'fr' => 'Architecture',
                            'en' => 'Architecture',
                            'ar' => 'العمارة',
                        ],
                        'description' => [
                            'fr' => 'Bâtiments remarquables, mosquées, architecture coloniale',
                            'en' => 'Remarkable buildings, mosques, colonial architecture',
                            'ar' => 'المباني البارزة والمساجد والعمارة الاستعمارية',
                        ],
                        'icon' => 'fas fa-building',
                        'color' => '#34495e',
                    ],
                    [
                        'name' => [
                            'fr' => 'Musées & Culture',
                            'en' => 'Museums & Culture',
                            'ar' => 'المتاحف والثقافة',
                        ],
                        'description' => [
                            'fr' => 'Musées, centres culturels, galeries d\'art',
                            'en' => 'Museums, cultural centers, art galleries',
                            'ar' => 'المتاحف والمراكز الثقافية ومعارض الفنون',
                        ],
                        'icon' => 'fas fa-university',
                        'color' => '#e67e22',
                    ],
                    [
                        'name' => [
                            'fr' => 'Sports Nautiques',
                            'en' => 'Water Sports',
                            'ar' => 'الرياضات المائية',
                        ],
                        'description' => [
                            'fr' => 'Plongée, snorkeling, voile, pêche',
                            'en' => 'Diving, snorkeling, sailing, fishing',
                            'ar' => 'الغوص والغطس والإبحار والصيد',
                        ],
                        'icon' => 'fas fa-swimmer',
                        'color' => '#3498db',
                    ],
                    [
                        'name' => [
                            'fr' => 'Randonnée & Trekking',
                            'en' => 'Hiking & Trekking',
                            'ar' => 'المشي والرحلات',
                        ],
                        'description' => [
                            'fr' => 'Sentiers de randonnée, trekking en montagne',
                            'en' => 'Hiking trails, mountain trekking',
                            'ar' => 'مسارات المشي والرحلات الجبلية',
                        ],
                        'icon' => 'fas fa-hiking',
                        'color' => '#27ae60',
                    ],
                    [
                        'name' => [
                            'fr' => 'Safari & Observation',
                            'en' => 'Safari & Wildlife',
                            'ar' => 'السفاري ومراقبة الحياة البرية',
                        ],
                        'description' => [
                            'fr' => 'Observation de la faune, safaris, birdwatching',
                            'en' => 'Wildlife observation, safaris, birdwatching',
                            'ar' => 'مراقبة الحياة البرية والرحلات الاستكشافية ومراقبة الطيور',
                        ],
                        'icon' => 'fas fa-binoculars',
                        'color' => '#f39c12',
                    ],
                    [
                        'name' => [
                            'fr' => 'Aventure & Extrême',
                            'en' => 'Adventure & Extreme',
                            'ar' => 'المغامرة والرياضات القصوى',
                        ],
                        'description' => [
                            'fr' => 'Sports extrêmes, escalade, exploration volcanique',
                            'en' => 'Extreme sports, climbing, volcanic exploration',
                            'ar' => 'الرياضات القصوى والتسلق واستكشاف البراكين',
                        ],
                        'icon' => 'fas fa-skull-crossbones',
                        'color' => '#e74c3c',
                    ],
                    [
                        'name' => [
                            'fr' => 'Détente & Bien-être',
                            'en' => 'Relaxation & Wellness',
                            'ar' => 'الاسترخاء والعافية',
                        ],
                        'description' => [
                            'fr' => 'Spa, massages, sources chaudes naturelles',
                            'en' => 'Spa, massages, natural hot springs',
                            'ar' => 'المنتجعات الصحية والتدليك والينابيع الحارة الطبيعية',
                        ],
                        'icon' => 'fas fa-spa',
                        'color' => '#9b59b6',
                    ],
                ],
            ],
            [
                'name' => [
                    'fr' => 'Où Dormir',
                    'en' => 'Where to Stay',
                    'ar' => 'أين تقيم',
                ],
                'description' => [
                    'fr' => 'Hébergements et logements à Djibouti',
                    'en' => 'Accommodations and lodging in Djibouti',
                    'ar' => 'أماكن الإقامة والسكن في جيبوتي',
                ],
                'icon' => 'fas fa-bed',
                'color' => '#9b59b6',
                'children' => [
                    [
                        'name' => [
                            'fr' => 'Hôtels de Luxe',
                            'en' => 'Luxury Hotels',
                            'ar' => 'الفنادق الفاخرة',
                        ],
                        'description' => [
                            'fr' => 'Hôtels 4-5 étoiles, resorts de luxe',
                            'en' => '4-5 star hotels, luxury resorts',
                            'ar' => 'فنادق 4-5 نجوم ومنتجعات فاخرة',
                        ],
                        'icon' => 'fas fa-crown',
                        'color' => '#f1c40f',
                    ],
                    [
                        'name' => [
                            'fr' => 'Hôtels Standard',
                            'en' => 'Standard Hotels',
                            'ar' => 'الفنادق العادية',
                        ],
                        'description' => [
                            'fr' => 'Hôtels 2-3 étoiles, bon rapport qualité-prix',
                            'en' => '2-3 star hotels, good value for money',
                            'ar' => 'فنادق 2-3 نجوم بنسبة جودة-سعر جيدة',
                        ],
                        'icon' => 'fas fa-hotel',
                        'color' => '#3498db',
                    ],
                    [
                        'name' => [
                            'fr' => 'Auberges & Guesthouses',
                            'en' => 'Hostels & Guesthouses',
                            'ar' => 'النزل وبيوت الضيافة',
                        ],
                        'description' => [
                            'fr' => 'Hébergements économiques, auberges de jeunesse',
                            'en' => 'Budget accommodations, youth hostels',
                            'ar' => 'أماكن الإقامة الاقتصادية ونزل الشباب',
                        ],
                        'icon' => 'fas fa-home',
                        'color' => '#27ae60',
                    ],
                    [
                        'name' => [
                            'fr' => 'Campings & Éco-lodges',
                            'en' => 'Camping & Eco-lodges',
                            'ar' => 'المخيمات والنزل البيئية',
                        ],
                        'description' => [
                            'fr' => 'Campings, éco-lodges, hébergements nature',
                            'en' => 'Camping sites, eco-lodges, nature accommodations',
                            'ar' => 'مواقع التخييم والنزل البيئية وأماكن الإقامة الطبيعية',
                        ],
                        'icon' => 'fas fa-campground',
                        'color' => '#2ecc71',
                    ],
                ],
            ],
            [
                'name' => [
                    'fr' => 'Où Manger',
                    'en' => 'Where to Eat',
                    'ar' => 'أين تأكل',
                ],
                'description' => [
                    'fr' => 'Restaurants et gastronomie djiboutienne',
                    'en' => 'Restaurants and Djiboutian gastronomy',
                    'ar' => 'المطاعم والمأكولات الجيبوتية',
                ],
                'icon' => 'fas fa-utensils',
                'color' => '#f39c12',
                'children' => [
                    [
                        'name' => [
                            'fr' => 'Cuisine Locale',
                            'en' => 'Local Cuisine',
                            'ar' => 'المأكولات المحلية',
                        ],
                        'description' => [
                            'fr' => 'Restaurants de cuisine djiboutienne traditionnelle',
                            'en' => 'Traditional Djiboutian cuisine restaurants',
                            'ar' => 'مطاعم المأكولات الجيبوتية التقليدية',
                        ],
                        'icon' => 'fas fa-drumstick-bite',
                        'color' => '#e67e22',
                    ],
                    [
                        'name' => [
                            'fr' => 'Cuisine Internationale',
                            'en' => 'International Cuisine',
                            'ar' => 'المأكولات العالمية',
                        ],
                        'description' => [
                            'fr' => 'Restaurants français, italiens, indiens, chinois...',
                            'en' => 'French, Italian, Indian, Chinese restaurants...',
                            'ar' => 'المطاعم الفرنسية والإيطالية والهندية والصينية...',
                        ],
                        'icon' => 'fas fa-globe-americas',
                        'color' => '#3498db',
                    ],
                    [
                        'name' => [
                            'fr' => 'Fruits de Mer',
                            'en' => 'Seafood',
                            'ar' => 'المأكولات البحرية',
                        ],
                        'description' => [
                            'fr' => 'Restaurants spécialisés en poissons et fruits de mer',
                            'en' => 'Restaurants specializing in fish and seafood',
                            'ar' => 'مطاعم متخصصة في الأسماك والمأكولات البحرية',
                        ],
                        'icon' => 'fas fa-fish',
                        'color' => '#3498db',
                    ],
                    [
                        'name' => [
                            'fr' => 'Cafés & Pâtisseries',
                            'en' => 'Cafés & Pastries',
                            'ar' => 'المقاهي والحلويات',
                        ],
                        'description' => [
                            'fr' => 'Cafés, salons de thé, pâtisseries',
                            'en' => 'Cafés, tea rooms, pastry shops',
                            'ar' => 'المقاهي وصالات الشاي ومحلات الحلويات',
                        ],
                        'icon' => 'fas fa-coffee',
                        'color' => '#8e44ad',
                    ],
                    [
                        'name' => [
                            'fr' => 'Street Food',
                            'en' => 'Street Food',
                            'ar' => 'طعام الشارع',
                        ],
                        'description' => [
                            'fr' => 'Cuisine de rue, snacks locaux, marchés',
                            'en' => 'Street food, local snacks, markets',
                            'ar' => 'طعام الشارع والوجبات الخفيفة المحلية والأسواق',
                        ],
                        'icon' => 'fas fa-hamburger',
                        'color' => '#e74c3c',
                    ],
                ],
            ],
            [
                'name' => [
                    'fr' => 'Shopping',
                    'en' => 'Shopping',
                    'ar' => 'التسوق',
                ],
                'description' => [
                    'fr' => 'Boutiques, marchés et centres commerciaux',
                    'en' => 'Shops, markets and shopping centers',
                    'ar' => 'المتاجر والأسواق والمراكز التجارية',
                ],
                'icon' => 'fas fa-shopping-bag',
                'color' => '#e74c3c',
                'children' => [
                    [
                        'name' => [
                            'fr' => 'Artisanat Local',
                            'en' => 'Local Crafts',
                            'ar' => 'الحرف المحلية',
                        ],
                        'description' => [
                            'fr' => 'Artisanat traditionnel, souvenirs authentiques',
                            'en' => 'Traditional crafts, authentic souvenirs',
                            'ar' => 'الحرف التقليدية والهدايا التذكارية الأصيلة',
                        ],
                        'icon' => 'fas fa-palette',
                        'color' => '#e67e22',
                    ],
                    [
                        'name' => [
                            'fr' => 'Marchés Traditionnels',
                            'en' => 'Traditional Markets',
                            'ar' => 'الأسواق التقليدية',
                        ],
                        'description' => [
                            'fr' => 'Marchés locaux, souks, marchés aux épices',
                            'en' => 'Local markets, souks, spice markets',
                            'ar' => 'الأسواق المحلية والأسواق الشعبية وأسواق التوابل',
                        ],
                        'icon' => 'fas fa-store-alt',
                        'color' => '#f39c12',
                    ],
                    [
                        'name' => [
                            'fr' => 'Centres Commerciaux',
                            'en' => 'Shopping Malls',
                            'ar' => 'المراكز التجارية',
                        ],
                        'description' => [
                            'fr' => 'Centres commerciaux modernes, boutiques de mode',
                            'en' => 'Modern shopping centers, fashion boutiques',
                            'ar' => 'المراكز التجارية الحديثة ومتاجر الأزياء',
                        ],
                        'icon' => 'fas fa-building',
                        'color' => '#3498db',
                    ],
                    [
                        'name' => [
                            'fr' => 'Bijouterie & Parfums',
                            'en' => 'Jewelry & Perfumes',
                            'ar' => 'المجوهرات والعطور',
                        ],
                        'description' => [
                            'fr' => 'Bijouteries, parfumeries, produits de luxe',
                            'en' => 'Jewelry stores, perfumeries, luxury products',
                            'ar' => 'متاجر المجوهرات والعطور والمنتجات الفاخرة',
                        ],
                        'icon' => 'fas fa-gem',
                        'color' => '#9b59b6',
                    ],
                ],
            ],
        ];

        // Créer les catégories principales et leurs sous-catégories
        foreach ($categories as $index => $categoryData) {
            $category = Category::create([
                'parent_id' => null,
                'slug' => \Str::slug($categoryData['name']['fr']),
                'icon' => $categoryData['icon'],
                'color' => $categoryData['color'],
                'sort_order' => $index + 1,
                'level' => 0,
                'is_active' => true,
            ]);

            // Créer les traductions pour la catégorie principale
            foreach (['fr', 'en', 'ar'] as $locale) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $categoryData['name'][$locale],
                    'description' => $categoryData['description'][$locale],
                ]);
            }

            // Créer les sous-catégories
            if (isset($categoryData['children'])) {
                foreach ($categoryData['children'] as $childIndex => $childData) {
                    $childCategory = Category::create([
                        'parent_id' => $category->id,
                        'slug' => \Str::slug($childData['name']['fr']),
                        'icon' => $childData['icon'],
                        'color' => $childData['color'],
                        'sort_order' => $childIndex + 1,
                        'level' => 1,
                        'is_active' => true,
                    ]);

                    // Créer les traductions pour la sous-catégorie
                    foreach (['fr', 'en', 'ar'] as $locale) {
                        CategoryTranslation::create([
                            'category_id' => $childCategory->id,
                            'locale' => $locale,
                            'name' => $childData['name'][$locale],
                            'description' => $childData['description'][$locale],
                        ]);
                    }
                }
            }
        }

        $this->command->info('✅ Catégories touristiques créées avec succès !');
        $this->command->info('📊 '.Category::whereNull('parent_id')->count().' catégories principales');
        $this->command->info('📁 '.Category::whereNotNull('parent_id')->count().' sous-catégories');
    }
}
