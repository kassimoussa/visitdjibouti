<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use Illuminate\Database\Seeder;

class AppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Splash Screens Configuration
        AppSetting::setSetting('splash_screens', 'mixed', [
            'screens' => [
                [
                    'id' => 1,
                    'duration' => 3000,
                    'translations' => [
                        'fr' => [
                            'title' => 'Bienvenue à Djibouti',
                            'subtitle' => 'Découvrez les merveilles de la Corne de l\'Afrique',
                        ],
                        'en' => [
                            'title' => 'Welcome to Djibouti',
                            'subtitle' => 'Discover the wonders of the Horn of Africa',
                        ],
                        'ar' => [
                            'title' => 'مرحبا بكم في جيبوتي',
                            'subtitle' => 'اكتشف عجائب القرن الأفريقي',
                        ],
                    ],
                    'animation' => 'fade',
                    'background_color' => '#1E88E5',
                    'text_color' => '#FFFFFF',
                    'order' => 1,
                ],
                [
                    'id' => 2,
                    'duration' => 2500,
                    'translations' => [
                        'fr' => [
                            'title' => 'Explorez',
                            'subtitle' => 'Points d\'intérêt, événements et plus encore',
                        ],
                        'en' => [
                            'title' => 'Explore',
                            'subtitle' => 'Points of interest, events and more',
                        ],
                        'ar' => [
                            'title' => 'استكشف',
                            'subtitle' => 'نقاط الاهتمام والأحداث والمزيد',
                        ],
                    ],
                    'animation' => 'slide_left',
                    'background_color' => '#00ACC1',
                    'text_color' => '#FFFFFF',
                    'order' => 2,
                ],
            ],
            'total_duration' => 5500,
            'skip_enabled' => true,
            'auto_advance' => true,
        ]);

        // App Slogans and Welcome Messages
        AppSetting::setSetting('app_messages', 'text', [
            'app_slogan' => [
                'fr' => 'Votre guide touristique digital',
                'en' => 'Your digital tourism guide',
                'ar' => 'دليلك السياحي الرقمي',
            ],
            'welcome_message' => [
                'fr' => 'Explorez les merveilles de Djibouti avec notre application interactive',
                'en' => 'Explore the wonders of Djibouti with our interactive application',
                'ar' => 'استكشف عجائب جيبوتي مع تطبيقنا التفاعلي',
            ],
            'home_greeting' => [
                'fr' => 'Que souhaitez-vous découvrir aujourd\'hui ?',
                'en' => 'What would you like to discover today?',
                'ar' => 'ماذا تريد أن تكتشف اليوم؟',
            ],
        ]);

        // App Configuration
        AppSetting::setSetting('app_config', 'config', [
            'theme' => [
                'primary_color' => '#1E88E5',
                'secondary_color' => '#00ACC1',
                'accent_color' => '#FFC107',
                'background_color' => '#F5F5F5',
                'text_color' => '#212121',
                'light_text_color' => '#757575',
            ],
            'features' => [
                'offline_mode' => true,
                'push_notifications' => true,
                'location_tracking' => true,
                'favorites_sync' => true,
                'dark_mode' => false,
                'auto_language_detection' => true,
            ],
            'api_settings' => [
                'cache_duration' => 3600,
                'image_quality' => 'high',
                'max_retry_attempts' => 3,
                'timeout' => 30,
            ],
            'maps' => [
                'default_zoom' => 10,
                'max_zoom' => 18,
                'min_zoom' => 6,
                'default_center' => [
                    'latitude' => 11.8251,
                    'longitude' => 42.5903,
                ],
            ],
        ]);

        // Onboarding Messages
        AppSetting::setSetting('onboarding', 'text', [
            'steps' => [
                [
                    'id' => 1,
                    'translations' => [
                        'fr' => [
                            'title' => 'Découvrez les POIs',
                            'description' => 'Explorez les points d\'intérêt touristiques de Djibouti avec des informations détaillées',
                        ],
                        'en' => [
                            'title' => 'Discover POIs',
                            'description' => 'Explore Djibouti\'s tourist points of interest with detailed information',
                        ],
                        'ar' => [
                            'title' => 'اكتشف النقاط المثيرة للاهتمام',
                            'description' => 'استكشف نقاط الاهتمام السياحية في جيبوتي مع معلومات مفصلة',
                        ],
                    ],
                    'icon' => 'map-marker',
                    'order' => 1,
                ],
                [
                    'id' => 2,
                    'translations' => [
                        'fr' => [
                            'title' => 'Participez aux événements',
                            'description' => 'Réservez votre place aux événements culturels et touristiques',
                        ],
                        'en' => [
                            'title' => 'Join Events',
                            'description' => 'Book your spot at cultural and tourist events',
                        ],
                        'ar' => [
                            'title' => 'شارك في الأحداث',
                            'description' => 'احجز مكانك في الأحداث الثقافية والسياحية',
                        ],
                    ],
                    'icon' => 'calendar',
                    'order' => 2,
                ],
                [
                    'id' => 3,
                    'translations' => [
                        'fr' => [
                            'title' => 'Sauvegardez vos favoris',
                            'description' => 'Créez votre liste personnalisée de lieux et événements préférés',
                        ],
                        'en' => [
                            'title' => 'Save Favorites',
                            'description' => 'Create your personalized list of favorite places and events',
                        ],
                        'ar' => [
                            'title' => 'احفظ المفضلات',
                            'description' => 'أنشئ قائمتك الشخصية من الأماكن والأحداث المفضلة',
                        ],
                    ],
                    'icon' => 'heart',
                    'order' => 3,
                ],
            ],
        ]);

        // Push Notification Templates
        AppSetting::setSetting('notification_templates', 'text', [
            'welcome' => [
                'fr' => 'Bienvenue dans Visit Djibouti ! Commencez votre exploration dès maintenant.',
                'en' => 'Welcome to Visit Djibouti! Start your exploration now.',
                'ar' => 'مرحبا في زيارة جيبوتي! ابدأ استكشافك الآن.',
            ],
            'new_event' => [
                'fr' => 'Nouvel événement disponible : {{event_name}}',
                'en' => 'New event available: {{event_name}}',
                'ar' => 'حدث جديد متاح: {{event_name}}',
            ],
            'event_reminder' => [
                'fr' => 'Rappel : {{event_name}} commence dans {{time_remaining}}',
                'en' => 'Reminder: {{event_name}} starts in {{time_remaining}}',
                'ar' => 'تذكير: {{event_name}} يبدأ في {{time_remaining}}',
            ],
            'nearby_poi' => [
                'fr' => 'Découvrez {{poi_name}} à proximité de votre position',
                'en' => 'Discover {{poi_name}} near your location',
                'ar' => 'اكتشف {{poi_name}} بالقرب من موقعك',
            ],
        ]);

        // Error Messages
        AppSetting::setSetting('error_messages', 'text', [
            'network_error' => [
                'fr' => 'Problème de connexion. Vérifiez votre connexion internet.',
                'en' => 'Connection problem. Please check your internet connection.',
                'ar' => 'مشكلة في الاتصال. يرجى التحقق من اتصال الإنترنت.',
            ],
            'location_disabled' => [
                'fr' => 'Activez la géolocalisation pour une meilleure expérience.',
                'en' => 'Enable location services for a better experience.',
                'ar' => 'قم بتفعيل خدمات الموقع للحصول على تجربة أفضل.',
            ],
            'server_error' => [
                'fr' => 'Erreur serveur. Veuillez réessayer plus tard.',
                'en' => 'Server error. Please try again later.',
                'ar' => 'خطأ في الخادم. يرجى المحاولة مرة أخرى لاحقا.',
            ],
        ]);

        // About App Information
        AppSetting::setSetting('app_info', 'text', [
            'version' => '1.0.0',
            'build_number' => '1',
            'about_text' => [
                'fr' => 'Visit Djibouti est votre compagnon idéal pour découvrir les trésors cachés et les merveilles de Djibouti.',
                'en' => 'Visit Djibouti is your ideal companion to discover the hidden treasures and wonders of Djibouti.',
                'ar' => 'زيارة جيبوتي هي رفيقك المثالي لاكتشاف الكنوز المخفية وعجائب جيبوتي.',
            ],
            'privacy_policy_url' => [
                'fr' => 'https://visitdjibouti.dj/privacy-fr',
                'en' => 'https://visitdjibouti.dj/privacy-en',
                'ar' => 'https://visitdjibouti.dj/privacy-ar',
            ],
            'terms_url' => [
                'fr' => 'https://visitdjibouti.dj/terms-fr',
                'en' => 'https://visitdjibouti.dj/terms-en',
                'ar' => 'https://visitdjibouti.dj/terms-ar',
            ],
            'support_email' => 'support@visitdjibouti.dj',
            'support_phone' => '+253 21 35 00 00',
        ]);
    }
}
