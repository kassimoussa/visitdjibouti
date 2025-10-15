<?php

namespace App\Livewire\Admin;

use App\Models\AppSetting;
use Livewire\Attributes\On;
use Livewire\Component;

class AppSettingManager extends Component
{
    public $showModal = false;

    public $modalMode = 'edit';

    public $modalTitle = 'Paramètres App Mobile';

    public $currentSettingId = null;

    public $currentKey = '';

    public $currentType = 'text';

    public $mediaId = null;

    public $isActive = true;

    // Contenu JSON structuré (en arrière-plan)
    public $jsonContent = '{}';

    public $editingContent = [];

    // ===== CHAMPS FORMULAIRES POUR SPLASH SCREENS =====
    public $splashScreens = [
        [
            'duration' => 3000,
            'animation' => 'fade',
            'background_color' => '#1E88E5',
            'text_color' => '#FFFFFF',
            'order' => 1,
            'media_id' => null,
            'translations' => [
                'fr' => ['title' => '', 'subtitle' => ''],
                'en' => ['title' => '', 'subtitle' => ''],
                'ar' => ['title' => '', 'subtitle' => ''],
            ],
        ],
    ];

    public $totalDuration = 5500;

    public $skipEnabled = true;

    public $autoAdvance = true;

    // ===== CHAMPS FORMULAIRES POUR MESSAGES =====
    public $appMessages = [
        'app_slogan' => ['fr' => '', 'en' => '', 'ar' => ''],
        'welcome_message' => ['fr' => '', 'en' => '', 'ar' => ''],
        'home_greeting' => ['fr' => '', 'en' => '', 'ar' => ''],
    ];

    // ===== CHAMPS FORMULAIRES POUR CONFIGURATION =====
    public $appConfigTheme = [
        'primary_color' => '#1E88E5',
        'secondary_color' => '#00ACC1',
        'accent_color' => '#FFC107',
        'background_color' => '#F5F5F5',
        'text_color' => '#212121',
    ];

    public $appConfigFeatures = [
        'offline_mode' => true,
        'push_notifications' => true,
        'location_tracking' => true,
        'favorites_sync' => true,
        'dark_mode' => false,
    ];

    public $appConfigApi = [
        'cache_duration' => 3600,
        'image_quality' => 'high',
        'timeout' => 30,
    ];

    // ===== CHAMPS FORMULAIRES POUR NOTIFICATIONS =====
    public $notificationTemplates = [
        'welcome' => ['fr' => '', 'en' => '', 'ar' => ''],
        'new_event' => ['fr' => '', 'en' => '', 'ar' => ''],
        'event_reminder' => ['fr' => '', 'en' => '', 'ar' => ''],
        'nearby_poi' => ['fr' => '', 'en' => '', 'ar' => ''],
    ];

    // ===== CHAMPS FORMULAIRES POUR MESSAGES D'ERREUR =====
    public $errorMessages = [
        'network_error' => ['fr' => '', 'en' => '', 'ar' => ''],
        'location_disabled' => ['fr' => '', 'en' => '', 'ar' => ''],
        'server_error' => ['fr' => '', 'en' => '', 'ar' => ''],
    ];

    // ===== CHAMPS FORMULAIRES POUR ONBOARDING =====
    public $onboardingSteps = [
        [
            'icon' => 'map-marker',
            'order' => 1,
            'translations' => [
                'fr' => ['title' => '', 'description' => ''],
                'en' => ['title' => '', 'description' => ''],
                'ar' => ['title' => '', 'description' => ''],
            ],
        ],
    ];

    // ===== CHAMPS FORMULAIRES POUR INFO APP =====
    public $appInfo = [
        'version' => '1.0.0',
        'build_number' => '1',
        'support_email' => 'support@visitdjibouti.dj',
        'support_phone' => '+253 21 35 00 00',
        'about_text' => ['fr' => '', 'en' => '', 'ar' => ''],
        'privacy_policy_url' => ['fr' => '', 'en' => '', 'ar' => ''],
        'terms_url' => ['fr' => '', 'en' => '', 'ar' => ''],
    ];

    // Langues disponibles
    public $availableLocales = ['fr', 'en'];

    public $currentLocale = 'fr';

    // Types de settings disponibles
    public $availableTypes = [
        'image' => 'Images/Médias',
        'text' => 'Textes multilingues',
        'config' => 'Configuration JSON',
        'mixed' => 'Mixte (Texte + Images)',
    ];

    // Settings prédéfinis
    public $predefinedSettings = [
        'splash_screens' => [
            'name' => 'Écrans de démarrage',
            'type' => 'mixed',
            'description' => 'Configuration des splash screens avec images et textes',
        ],
        'app_messages' => [
            'name' => 'Messages de l\'application',
            'type' => 'text',
            'description' => 'Slogans, messages d\'accueil et textes d\'interface',
        ],
        'app_config' => [
            'name' => 'Configuration générale',
            'type' => 'config',
            'description' => 'Thème, couleurs, fonctionnalités activées',
        ],
        'onboarding' => [
            'name' => 'Introduction guidée',
            'type' => 'text',
            'description' => 'Étapes d\'introduction pour nouveaux utilisateurs',
        ],
        'notification_templates' => [
            'name' => 'Templates de notifications',
            'type' => 'text',
            'description' => 'Modèles de messages de notifications push',
        ],
        'error_messages' => [
            'name' => 'Messages d\'erreur',
            'type' => 'text',
            'description' => 'Messages d\'erreur personnalisés multilingues',
        ],
        'app_info' => [
            'name' => 'Informations de l\'app',
            'type' => 'text',
            'description' => 'Version, à propos, liens de support',
        ],
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function render()
    {
        $settings = AppSetting::orderBy('key')->get();

        return view('livewire.admin.settings.app-setting-manager', compact('settings'));
    }

    public function loadSettings()
    {
        // Logique de chargement si nécessaire
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->modalTitle = 'Créer un nouveau paramètre';
        $this->showModal = true;
    }

    public function openEditModal($settingId)
    {
        $setting = AppSetting::findOrFail($settingId);

        $this->currentSettingId = $setting->id;
        $this->currentKey = $setting->key;
        $this->currentType = $setting->type;
        $this->mediaId = $setting->media_id;
        $this->isActive = $setting->is_active;
        $this->editingContent = $setting->content ?? [];

        // Charger les données dans les champs appropriés selon la clé
        $this->loadDataIntoFormFields($setting->key, $setting->content ?? []);

        $this->modalMode = 'edit';
        $this->modalTitle = 'Modifier '.($this->predefinedSettings[$setting->key]['name'] ?? $setting->key);
        $this->showModal = true;
    }

    public function openPredefinedModal($key)
    {
        $setting = AppSetting::where('key', $key)->first();

        if ($setting) {
            $this->openEditModal($setting->id);
        } else {
            $this->resetForm();
            $this->currentKey = $key;
            $this->currentType = $this->predefinedSettings[$key]['type'];
            $this->modalMode = 'create';
            $this->modalTitle = 'Configurer '.$this->predefinedSettings[$key]['name'];

            // Initialiser avec les valeurs par défaut des champs
            $this->initializeDefaultFormFields($key);

            $this->showModal = true;
        }
    }

    private function initializeDefaultContent($key)
    {
        $defaultContent = [];

        switch ($key) {
            case 'splash_screens':
                $defaultContent = [
                    'screens' => [
                        [
                            'id' => 1,
                            'duration' => 3000,
                            'translations' => [
                                'fr' => ['title' => '', 'subtitle' => ''],
                                'en' => ['title' => '', 'subtitle' => ''],
                                'ar' => ['title' => '', 'subtitle' => ''],
                            ],
                            'animation' => 'fade',
                            'order' => 1,
                        ],
                    ],
                ];
                break;

            case 'app_messages':
                $defaultContent = [
                    'app_slogan' => [
                        'fr' => '',
                        'en' => '',
                        'ar' => '',
                    ],
                    'welcome_message' => [
                        'fr' => '',
                        'en' => '',
                        'ar' => '',
                    ],
                ];
                break;

            case 'app_config':
                $defaultContent = [
                    'theme' => [
                        'primary_color' => '#1E88E5',
                        'secondary_color' => '#00ACC1',
                        'accent_color' => '#FFC107',
                    ],
                    'features' => [
                        'offline_mode' => true,
                        'push_notifications' => true,
                        'location_tracking' => true,
                    ],
                ];
                break;

            default:
                $defaultContent = [
                    'exemple' => [
                        'fr' => '',
                        'en' => '',
                        'ar' => '',
                    ],
                ];
        }

        $this->editingContent = $defaultContent;
        $this->jsonContent = json_encode($defaultContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function save()
    {
        $this->validate([
            'currentKey' => 'required|string|max:255',
            'currentType' => 'required|in:image,text,config,mixed',
        ], [
            'currentKey.required' => 'La clé est obligatoire',
            'currentType.required' => 'Le type est obligatoire',
        ]);

        try {
            // Convertir les champs de formulaire en JSON selon la clé
            $content = $this->convertFormFieldsToJson($this->currentKey);

            if ($this->modalMode === 'create') {
                AppSetting::create([
                    'key' => $this->currentKey,
                    'type' => $this->currentType,
                    'media_id' => $this->mediaId,
                    'content' => $content,
                    'is_active' => $this->isActive,
                ]);

                session()->flash('message', 'Paramètre créé avec succès.');
            } else {
                $setting = AppSetting::findOrFail($this->currentSettingId);
                $setting->update([
                    'key' => $this->currentKey,
                    'type' => $this->currentType,
                    'media_id' => $this->mediaId,
                    'content' => $content,
                    'is_active' => $this->isActive,
                ]);

                session()->flash('message', 'Paramètre mis à jour avec succès.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la sauvegarde: '.$e->getMessage());
        }
    }

    public function toggleStatus($settingId)
    {
        try {
            $setting = AppSetting::findOrFail($settingId);
            $setting->update(['is_active' => ! $setting->is_active]);

            $status = $setting->is_active ? 'activé' : 'désactivé';
            session()->flash('message', "Paramètre {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: '.$e->getMessage());
        }
    }

    public function delete($settingId)
    {
        try {
            $setting = AppSetting::findOrFail($settingId);
            $setting->delete();

            session()->flash('message', 'Paramètre supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }

    public function switchLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    #[On('media-selected')]
    public function onMediaSelected($mediaIds)
    {
        // Si c'est un tableau, prendre le premier élément, sinon utiliser directement la valeur
        $this->mediaId = is_array($mediaIds) && count($mediaIds) > 0 ? $mediaIds[0] : $mediaIds;
    }

    public function openMediaSelector()
    {
        $preselected = $this->mediaId ? [$this->mediaId] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->currentSettingId = null;
        $this->currentKey = '';
        $this->currentType = 'text';
        $this->mediaId = null;
        $this->isActive = true;
        $this->editingContent = [];
        $this->jsonContent = '{}';
        $this->currentLocale = 'fr';

        // Reset des champs de configuration
        $this->appConfigTheme = [
            'primary_color' => '#1E88E5',
            'secondary_color' => '#00ACC1',
            'accent_color' => '#FFC107',
            'background_color' => '#F5F5F5',
            'text_color' => '#212121',
        ];

        $this->appConfigFeatures = [
            'offline_mode' => true,
            'push_notifications' => true,
            'location_tracking' => true,
            'favorites_sync' => true,
            'dark_mode' => false,
        ];

        $this->appConfigApi = [
            'cache_duration' => 3600,
            'image_quality' => 'high',
            'timeout' => 30,
        ];

        $this->resetValidation();
    }

    /**
     * Charger les données JSON dans les champs de formulaire appropriés
     */
    private function loadDataIntoFormFields($key, $content)
    {
        switch ($key) {
            case 'splash_screens':
                if (isset($content['screens'])) {
                    $this->splashScreens = $content['screens'];
                }
                $this->totalDuration = $content['total_duration'] ?? 5500;
                $this->skipEnabled = $content['skip_enabled'] ?? true;
                $this->autoAdvance = $content['auto_advance'] ?? true;
                break;

            case 'app_messages':
                $this->appMessages = array_merge($this->appMessages, $content);
                break;

            case 'app_config':
                if (isset($content['theme'])) {
                    $this->appConfigTheme = array_merge($this->appConfigTheme, $content['theme']);
                }
                if (isset($content['features'])) {
                    $this->appConfigFeatures = array_merge($this->appConfigFeatures, $content['features']);
                }
                if (isset($content['api_settings'])) {
                    $this->appConfigApi = array_merge($this->appConfigApi, $content['api_settings']);
                }
                break;

            case 'notification_templates':
                $this->notificationTemplates = array_merge($this->notificationTemplates, $content);
                break;

            case 'error_messages':
                $this->errorMessages = array_merge($this->errorMessages, $content);
                break;

            case 'onboarding':
                if (isset($content['steps'])) {
                    $this->onboardingSteps = $content['steps'];
                }
                break;

            case 'app_info':
                $this->appInfo = array_merge($this->appInfo, $content);
                break;
        }
    }

    /**
     * Initialiser les champs avec les valeurs par défaut
     */
    private function initializeDefaultFormFields($key)
    {
        // Les valeurs par défaut sont déjà définies dans les propriétés
        // Cette méthode peut être utilisée pour des initialisations spéciales si nécessaire
    }

    /**
     * Convertir les champs de formulaire en structure JSON pour la sauvegarde
     */
    private function convertFormFieldsToJson($key)
    {
        switch ($key) {
            case 'splash_screens':
                return [
                    'screens' => $this->splashScreens,
                    'total_duration' => $this->totalDuration,
                    'skip_enabled' => $this->skipEnabled,
                    'auto_advance' => $this->autoAdvance,
                ];

            case 'app_messages':
                return $this->appMessages;

            case 'app_config':
                return [
                    'theme' => $this->appConfigTheme,
                    'features' => $this->appConfigFeatures,
                    'api_settings' => $this->appConfigApi,
                ];

            case 'notification_templates':
                return $this->notificationTemplates;

            case 'error_messages':
                return $this->errorMessages;

            case 'onboarding':
                return [
                    'steps' => $this->onboardingSteps,
                ];

            case 'app_info':
                return $this->appInfo;

            default:
                // Pour les paramètres personnalisés, retourner un objet vide
                return [];
        }
    }

    /**
     * Ajouter un écran splash
     */
    public function addSplashScreen()
    {
        $this->splashScreens[] = [
            'duration' => 3000,
            'animation' => 'fade',
            'background_color' => '#1E88E5',
            'text_color' => '#FFFFFF',
            'order' => count($this->splashScreens) + 1,
            'media_id' => null,
            'translations' => [
                'fr' => ['title' => '', 'subtitle' => ''],
                'en' => ['title' => '', 'subtitle' => ''],
                'ar' => ['title' => '', 'subtitle' => ''],
            ],
        ];
    }

    /**
     * Supprimer un écran splash
     */
    public function removeSplashScreen($index)
    {
        if (isset($this->splashScreens[$index])) {
            unset($this->splashScreens[$index]);
            $this->splashScreens = array_values($this->splashScreens);

            // Réorganiser les ordres
            foreach ($this->splashScreens as $i => $screen) {
                $this->splashScreens[$i]['order'] = $i + 1;
            }
        }
    }

    /**
     * Ajouter une étape d'onboarding
     */
    public function addOnboardingStep()
    {
        $this->onboardingSteps[] = [
            'icon' => 'info',
            'order' => count($this->onboardingSteps) + 1,
            'translations' => [
                'fr' => ['title' => '', 'description' => ''],
                'en' => ['title' => '', 'description' => ''],
                'ar' => ['title' => '', 'description' => ''],
            ],
        ];
    }

    /**
     * Supprimer une étape d'onboarding
     */
    public function removeOnboardingStep($index)
    {
        if (isset($this->onboardingSteps[$index])) {
            unset($this->onboardingSteps[$index]);
            $this->onboardingSteps = array_values($this->onboardingSteps);

            // Réorganiser les ordres
            foreach ($this->onboardingSteps as $i => $step) {
                $this->onboardingSteps[$i]['order'] = $i + 1;
            }
        }
    }
}
