<?php

namespace App\Livewire\Admin;

use App\Models\OrganizationInfo;
use App\Models\Link;
use Livewire\Component;
use Livewire\Attributes\On;

class OrganizationInfoManager extends Component
{
    public $showModal = false;
    public $modalMode = 'edit';
    public $modalTitle = 'Informations de l\'organisation';
    public $modalType = 'basic'; // basic, contact, hours, logo, links
    public $organizationId = null;

    // Propriétés du formulaire principal
    public $logo_id = null;
    public $email = '';
    public $phone = '';
    public $address = '';
    public $opening_hours = '';

    // Traductions
    public $translations = [
        'fr' => [
            'name' => '',
            'description' => '',
            'opening_hours_translated' => '',
        ],
        'en' => [
            'name' => '',
            'description' => '',
            'opening_hours_translated' => '',
        ],
        'ar' => [
            'name' => '',
            'description' => '',
            'opening_hours_translated' => '',
        ],
    ];

    // Liens
    public $links = [];
    public $currentLink = [
        'id' => null,
        'url' => '',
        'platform' => 'website',
        'order' => 0,
        'translations' => [
            'fr' => ['name' => ''],
            'en' => ['name' => ''],
            'ar' => ['name' => ''],
        ]
    ];
    public $linkEditMode = 'create'; // create ou edit

    public $availableLocales = ['fr', 'en', 'ar'];
    public $currentLocale = 'fr';

    public $availablePlatforms = [
        'website' => 'Site web',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'whatsapp' => 'WhatsApp',
    ];

    protected function rules()
    {
        $rules = [
            'logo_id' => 'nullable|exists:media,id',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'opening_hours' => 'nullable|string|max:1000',
        ];

        // Règles pour les traductions
        foreach ($this->availableLocales as $locale) {
            $isRequired = ($locale === 'fr') ? 'required' : 'nullable';
            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = 'nullable|string|max:2000';
            $rules["translations.{$locale}.opening_hours_translated"] = 'nullable|string|max:1000';
        }

        // Règles pour le lien en cours d'édition (seulement si on est dans le modal des liens)
        if ($this->modalType === 'links') {
            $rules['currentLink.url'] = 'required|url|max:255';
            $rules['currentLink.platform'] = 'required|string|in:' . implode(',', array_keys($this->availablePlatforms));
            
            foreach ($this->availableLocales as $locale) {
                $isRequired = ($locale === 'fr') ? 'required' : 'nullable';
                $rules["currentLink.translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            }
        }

        return $rules;
    }

    protected $messages = [
        'translations.fr.name.required' => 'Le nom en français est obligatoire.',
        'email.email' => 'L\'email doit être valide.',
        'logo_id.exists' => 'Le logo sélectionné n\'existe pas.',
        'currentLink.url.required' => 'L\'URL est obligatoire.',
        'currentLink.url.url' => 'L\'URL doit être valide.',
        'currentLink.platform.required' => 'La plateforme est obligatoire.',
        'currentLink.translations.fr.name.required' => 'Le nom du lien en français est obligatoire.',
    ];

    public function mount()
    {
        $this->loadOrganizationInfo();
    }

    public function render()
    {
        return view('livewire.admin.settings.organization-info-manager');
    }

    public function loadOrganizationInfo()
    {
        $organization = OrganizationInfo::with(['translations', 'links.translations'])->first();
        
        if ($organization) {
            $this->organizationId = $organization->id;
            $this->logo_id = $organization->logo_id;
            $this->email = $organization->email ?? '';
            $this->phone = $organization->phone ?? '';
            $this->address = $organization->address ?? '';
            $this->opening_hours = $organization->opening_hours ?? '';

            // Initialiser les traductions vides d'abord
            foreach ($this->availableLocales as $locale) {
                $this->translations[$locale] = [
                    'name' => '',
                    'description' => '',
                    'opening_hours_translated' => '',
                ];
            }

            // Charger les traductions existantes
            foreach ($organization->translations as $translation) {
                $this->translations[$translation->locale] = [
                    'name' => $translation->name ?? '',
                    'description' => $translation->description ?? '',
                    'opening_hours_translated' => $translation->opening_hours_translated ?? '',
                ];
            }

            // Charger les liens
            $this->links = [];
            foreach ($organization->links as $link) {
                $linkTranslations = [];
                foreach ($this->availableLocales as $locale) {
                    $linkTranslations[$locale] = ['name' => ''];
                }

                foreach ($link->translations as $translation) {
                    $linkTranslations[$translation->locale] = [
                        'name' => $translation->name ?? ''
                    ];
                }

                $this->links[] = [
                    'id' => $link->id,
                    'url' => $link->url,
                    'platform' => $link->platform,
                    'order' => $link->order,
                    'translations' => $linkTranslations
                ];
            }
        } else {
            // Initialiser avec des valeurs par défaut si aucune organisation n'existe
            $this->resetForm();
        }
    }

    public function openModal($type = 'basic')
    {
        $this->loadOrganizationInfo();
        $this->modalMode = 'edit';
        $this->modalType = $type;
        
        switch ($type) {
            case 'basic':
                $this->modalTitle = 'Informations de base';
                break;
            case 'contact':
                $this->modalTitle = 'Informations de contact';
                break;
            case 'hours':
                $this->modalTitle = 'Horaires d\'ouverture';
                break;
            case 'logo':
                $this->modalTitle = 'Logo de l\'organisation';
                break;
            case 'links':
                $this->modalTitle = 'Liens et réseaux sociaux';
                break;
            default:
                $this->modalTitle = 'Informations de l\'organisation';
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'logo_id' => $this->logo_id,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'opening_hours' => $this->opening_hours,
            ];

            if ($this->organizationId) {
                $organization = OrganizationInfo::findOrFail($this->organizationId);
                $organization->update($data);
            } else {
                $organization = OrganizationInfo::create($data);
                $this->organizationId = $organization->id;
            }

            // Sauvegarder les traductions
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['name'])) {
                    $organization->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }

            // Sauvegarder un lien spécifique si on est dans le modal des liens
            if ($this->modalType === 'links' && !empty($this->currentLink['url'])) {
                if ($this->linkEditMode === 'create') {
                    // Créer un nouveau lien
                    $link = $organization->links()->create([
                        'url' => $this->currentLink['url'],
                        'platform' => $this->currentLink['platform'],
                        'order' => $organization->links()->count(),
                    ]);
                } else {
                    // Modifier un lien existant
                    $link = Link::findOrFail($this->currentLink['id']);
                    $link->update([
                        'url' => $this->currentLink['url'],
                        'platform' => $this->currentLink['platform'],
                    ]);
                }

                // Sauvegarder les traductions du lien
                foreach ($this->currentLink['translations'] as $locale => $translation) {
                    if (!empty($translation['name'])) {
                        $link->translations()->updateOrCreate(
                            ['locale' => $locale],
                            ['name' => $translation['name']]
                        );
                    }
                }
            }

            $this->closeModal();
            session()->flash('message', 'Informations de l\'organisation mises à jour avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function openCreateLinkModal()
    {
        $this->resetCurrentLink();
        $this->linkEditMode = 'create';
        $this->modalType = 'links';
        $this->modalTitle = 'Ajouter un nouveau lien';
        $this->showModal = true;
    }

    public function openEditLinkModal($linkId)
    {
        // Charger les données du lien
        $organization = OrganizationInfo::with(['links.translations'])->first();
        $link = $organization->links()->with('translations')->find($linkId);
        
        if ($link) {
            $this->currentLink = [
                'id' => $link->id,
                'url' => $link->url,
                'platform' => $link->platform,
                'order' => $link->order,
                'translations' => [
                    'fr' => ['name' => ''],
                    'en' => ['name' => ''],
                    'ar' => ['name' => ''],
                ]
            ];

            // Charger les traductions existantes
            foreach ($link->translations as $translation) {
                $this->currentLink['translations'][$translation->locale] = [
                    'name' => $translation->name ?? ''
                ];
            }
            
            $this->linkEditMode = 'edit';
            $this->modalType = 'links';
            $this->modalTitle = 'Modifier le lien';
            $this->showModal = true;
        }
    }

    public function deleteLink($linkId)
    {
        try {
            $link = Link::findOrFail($linkId);
            $link->delete();
            
            session()->flash('message', 'Lien supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    private function resetCurrentLink()
    {
        $this->currentLink = [
            'id' => null,
            'url' => '',
            'platform' => 'website',
            'order' => 0,
            'translations' => [
                'fr' => ['name' => ''],
                'en' => ['name' => ''],
                'ar' => ['name' => ''],
            ]
        ];
    }

    public function switchLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    /**
     * Ouvrir le sélecteur de médias pour le logo
     */
    public function openLogoSelector()
    {
        $preselected = $this->logo_id ? [$this->logo_id] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    #[On('media-selected')]
    public function onMediaSelected($mediaIds)
    {
        // Si c'est un tableau, prendre le premier élément, sinon utiliser directement la valeur
        $this->logo_id = is_array($mediaIds) && count($mediaIds) > 0 ? $mediaIds[0] : $mediaIds;
        
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->logo_id = null;
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->opening_hours = '';
        $this->organizationId = null;
        $this->currentLocale = 'fr';
        $this->links = [];
        $this->resetCurrentLink();

        // Reset traductions
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => '',
                'opening_hours_translated' => '',
            ];
        }

        $this->resetValidation();
    }
}