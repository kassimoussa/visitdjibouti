<?php

namespace Database\Seeders;

use App\Models\Embassy;
use App\Models\EmbassyTranslation;
use Illuminate\Database\Seeder;

class EmbassySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Ambassades étrangères à Djibouti
        $embassies = [
            [
                'type' => 'foreign_in_djibouti',
                'country_code' => 'PAL',
                'phones' => '21 35 49 23|21 35 82 05',
                'emails' => 'palestine@embassy.dj',
                'fax' => '21 35 82 05',
                'website' => 'www.palestine.embassy.dj',
                'ld' => '21 35.82.05|21 35 27 52',
                'latitude' => 11.5889,
                'longitude' => 43.1467,
                'translations' => [
                    'fr' => [
                        'name' => 'Ambassade de l\'État de Palestine',
                        'ambassador_name' => 'Kamil Abdallah Gazaz, Ambassadeur',
                        'address' => 'Rue des Martyrs, Quartier 7, Djibouti',
                        'postal_box' => 'BP 1849',
                    ],
                    'en' => [
                        'name' => 'Embassy of the State of Palestine',
                        'ambassador_name' => 'Kamil Abdallah Gazaz, Ambassador',
                        'address' => 'Martyrs Street, District 7, Djibouti',
                        'postal_box' => 'PO Box 1849',
                    ],
                    'ar' => [
                        'name' => 'سفارة دولة فلسطين',
                        'ambassador_name' => 'كامل عبد الله غزاز، سفير',
                        'address' => 'شارع الشهداء، الحي السابع، جيبوتي',
                        'postal_box' => 'ص.ب 1849',
                    ],
                ],
            ],
            [
                'type' => 'foreign_in_djibouti',
                'country_code' => 'USA',
                'phones' => '21 45 30 00',
                'emails' => 'info@usa.embassy.dj',
                'website' => 'https://dj.usembassy.gov',
                'latitude' => 11.5950,
                'longitude' => 43.1481,
                'translations' => [
                    'fr' => [
                        'name' => 'Ambassade des États-Unis',
                        'ambassador_name' => 'Jonathan Pratt, Ambassadeur',
                        'address' => 'Plateau du Serpent, Boulevard Maréchal Joffre, Djibouti',
                        'postal_box' => 'BP 185',
                    ],
                    'en' => [
                        'name' => 'Embassy of the United States',
                        'ambassador_name' => 'Jonathan Pratt, Ambassador',
                        'address' => 'Plateau du Serpent, Boulevard Maréchal Joffre, Djibouti',
                        'postal_box' => 'PO Box 185',
                    ],
                    'ar' => [
                        'name' => 'سفارة الولايات المتحدة الأمريكية',
                        'ambassador_name' => 'جوناثان برات، سفير',
                        'address' => 'هضبة الثعبان، شارع المارشال جوفر، جيبوتي',
                        'postal_box' => 'ص.ب 185',
                    ],
                ],
            ],
            [
                'type' => 'foreign_in_djibouti',
                'country_code' => 'DEU',
                'phones' => '21 32 27 00',
                'emails' => 'info@djibouti.diplo.de',
                'website' => 'www.djibouti.diplo.de',
                'latitude' => 11.5923,
                'longitude' => 43.1456,
                'translations' => [
                    'fr' => [
                        'name' => 'Ambassade d\'Allemagne',
                        'ambassador_name' => 'Dr. Michael Bauer, Ambassadeur',
                        'address' => 'Route de l\'Aéroport, Djibouti',
                        'postal_box' => 'BP 2036',
                    ],
                    'en' => [
                        'name' => 'Embassy of Germany',
                        'ambassador_name' => 'Dr. Michael Bauer, Ambassador',
                        'address' => 'Airport Road, Djibouti',
                        'postal_box' => 'PO Box 2036',
                    ],
                    'ar' => [
                        'name' => 'سفارة ألمانيا',
                        'ambassador_name' => 'د. مايكل باور، سفير',
                        'address' => 'طريق المطار، جيبوتي',
                        'postal_box' => 'ص.ب 2036',
                    ],
                ],
            ],
        ];

        // Ambassades djiboutiennes à l'étranger
        $djiboutianEmbassies = [
            [
                'type' => 'djiboutian_abroad',
                'country_code' => 'FRA',
                'phones' => '+33 1 47 27 49 22',
                'emails' => 'contact@ambassade-djibouti.fr',
                'website' => 'www.ambassade-djibouti.fr',
                'latitude' => 48.8566,
                'longitude' => 2.3522,
                'translations' => [
                    'fr' => [
                        'name' => 'Ambassade de Djibouti en France',
                        'ambassador_name' => 'Ahmed Araita Ali, Ambassadeur',
                        'address' => '26 rue Emile Ménier, 75116 Paris, France',
                        'postal_box' => '',
                    ],
                    'en' => [
                        'name' => 'Embassy of Djibouti in France',
                        'ambassador_name' => 'Ahmed Araita Ali, Ambassador',
                        'address' => '26 rue Emile Ménier, 75116 Paris, France',
                        'postal_box' => '',
                    ],
                    'ar' => [
                        'name' => 'سفارة جيبوتي في فرنسا',
                        'ambassador_name' => 'أحمد عرايطة علي، سفير',
                        'address' => '26 شارع إميل مونييه، 75116 باريس، فرنسا',
                        'postal_box' => '',
                    ],
                ],
            ],
        ];

        $allEmbassies = array_merge($embassies, $djiboutianEmbassies);

        foreach ($allEmbassies as $embassyData) {
            $translations = $embassyData['translations'];
            unset($embassyData['translations']);

            $embassy = Embassy::create($embassyData);

            foreach ($translations as $locale => $translation) {
                EmbassyTranslation::create([
                    'embassy_id' => $embassy->id,
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'ambassador_name' => $translation['ambassador_name'],
                    'address' => $translation['address'],
                    'postal_box' => $translation['postal_box'],
                ]);
            }
        }
    }
}
