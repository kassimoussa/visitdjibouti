<?php

namespace App\Livewire\Admin\Poi;

use App\Models\Category;
use App\Models\Media;
use App\Models\Poi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class PoiForm extends Component
{
    // Propriétés générales du POI (non traduites)
    public $poiId;

    public $slug = '';

    public $latitude = null;

    public $longitude = null;

    public $region = '';

    public $contacts = [];

    public $modalContact = [];

    public $editingContactIndex = null;

    public $website = '';

    public $is_featured = false;

    public $allow_reservations = false;

    public $status = 'draft';

    public $featuredImageId = null;

    // Traductions
    public $translations = [
        'fr' => [
            'name' => '',
            'description' => '',
            'short_description' => '',
            'address' => '',
            'opening_hours' => '',
            'entry_fee' => '',
            'tips' => '',
        ],
        'en' => [
            'name' => '',
            'description' => '',
            'short_description' => '',
            'address' => '',
            'opening_hours' => '',
            'entry_fee' => '',
            'tips' => '',
        ],
        'ar' => [
            'name' => '',
            'description' => '',
            'short_description' => '',
            'address' => '',
            'opening_hours' => '',
            'entry_fee' => '',
            'tips' => '',
        ],
    ];

    // Langue active pour l'édition
    public $activeLocale = 'fr';

    // Catégories
    public $selectedCategories = [];

    // Médias
    public $selectedMedia = [];

    public $showMediaSelector = false;

    public $mediaSelectorMode = 'single';

    // Tour Operators
    public $selectedTourOperators = [];

    public $showTourOperatorModal = false;

    public $modalTourOperator = [];

    public $editingTourOperatorIndex = null;

    // Mode édition
    public $isEditMode = false;

    // Règles de validation
    protected function rules()
    {
        $rules = [
            'slug' => $this->isEditMode
                ? 'nullable|string|max:255|unique:pois,slug,'.$this->poiId
                : 'nullable|string|max:255|unique:pois,slug',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'region' => 'nullable|string|max:255',
            'contacts' => 'nullable|array',
            'contacts.*.name' => 'required|string|max:255',
            'contacts.*.type' => 'required|string|max:100',
            'contacts.*.phone' => 'nullable|string|max:20',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.website' => 'nullable|string|max:255',
            'contacts.*.address' => 'nullable|string|max:500',
            'contacts.*.description' => 'nullable|string|max:1000',
            'contacts.*.is_primary' => 'boolean',
            'website' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'allow_reservations' => 'boolean',
            'status' => 'required|in:draft,published,archived',
            'selectedCategories' => 'required|array|min:1',
            'featuredImageId' => 'nullable|exists:media,id',
        ];

        // Ajouter les règles pour chaque langue
        $requiredLocale = config('app.fallback_locale', 'fr'); // La langue par défaut est obligatoire

        foreach (['fr', 'en'] as $locale) {
            $isRequired = ($locale === $requiredLocale) ? 'required' : 'nullable';

            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = "{$isRequired}|string";
            $rules["translations.{$locale}.short_description"] = 'nullable|string|max:500';
            $rules["translations.{$locale}.address"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.opening_hours"] = 'nullable|string';
            $rules["translations.{$locale}.entry_fee"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.tips"] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Changer la langue active
     */
    public function changeLocale($locale)
    {
        if (in_array($locale, ['fr', 'en'])) {
            $this->activeLocale = $locale;
        }
    }

    /**
     * Sélectionner une image en tant qu'image principale
     */
    public function selectFeaturedImage($mediaId)
    {
        $this->featuredImageId = $mediaId;
    }

    /**
     * Ajouter une image à la galerie
     */
    public function addToGallery($mediaId)
    {
        if (! in_array($mediaId, $this->selectedMedia)) {
            $this->selectedMedia[] = $mediaId;
        }
    }

    /**
     * Retirer une image de la galerie
     */
    public function removeFromGallery($mediaId)
    {
        $this->selectedMedia = array_values(array_filter($this->selectedMedia, function ($id) use ($mediaId) {
            return $id != $mediaId;
        }));
    }

    /**
     * Réorganiser la galerie
     */
    public function reorderGallery($oldIndex, $newIndex)
    {
        if (isset($this->selectedMedia[$oldIndex])) {
            $item = $this->selectedMedia[$oldIndex];
            unset($this->selectedMedia[$oldIndex]);
            array_splice($this->selectedMedia, $newIndex, 0, $item);
            $this->selectedMedia = array_values($this->selectedMedia);
        }
    }

    /**
     * Ouvrir le sélecteur de médias pour l'image principale
     */
    public function openFeaturedImageSelector()
    {
        $this->mediaSelectorMode = 'single';
        $preselected = $this->featuredImageId ? [$this->featuredImageId] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    /**
     * Ouvrir le sélecteur de médias pour la galerie
     */
    public function openGallerySelector()
    {
        $this->mediaSelectorMode = 'multiple';
        $this->dispatch('open-media-selector', 'multiple', $this->selectedMedia);
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    #[On('media-selected')]
    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->featuredImageId = ! empty($selectedIds) ? $selectedIds[0] : null;
        } else {
            $this->selectedMedia = $selectedIds;
        }
    }

    /**
     * Montage du composant
     */
    public function mount($poiId = null)
    {
        if ($poiId) {
            $this->poiId = $poiId;
            $this->isEditMode = true;

            // Récupérer le POI depuis la base de données
            $poi = Poi::findOrFail($poiId);

            // Remplir les propriétés non traduites
            $this->slug = $poi->slug;
            $this->latitude = $poi->latitude;
            $this->longitude = $poi->longitude;
            $this->region = $poi->region;
            $this->contacts = $poi->contacts ?: [];
            $this->website = $poi->website;
            $this->is_featured = (bool) $poi->is_featured;
            $this->allow_reservations = (bool) $poi->allow_reservations;
            $this->status = $poi->status;
            $this->featuredImageId = $poi->featured_image_id;

            // Remplir les traductions
            foreach ($poi->translations as $translation) {
                $locale = $translation->locale;

                if (isset($this->translations[$locale])) {
                    $this->translations[$locale]['name'] = $translation->name;
                    $this->translations[$locale]['description'] = $translation->description;
                    $this->translations[$locale]['short_description'] = $translation->short_description;
                    $this->translations[$locale]['address'] = $translation->address;
                    $this->translations[$locale]['opening_hours'] = $translation->opening_hours;
                    $this->translations[$locale]['entry_fee'] = $translation->entry_fee;
                    $this->translations[$locale]['tips'] = $translation->tips;
                }
            }

            // Remplir les catégories sélectionnées
            $this->selectedCategories = $poi->categories->pluck('id')->toArray();

            // Remplir les médias sélectionnés
            $this->selectedMedia = $poi->media->pluck('id')->toArray();

            // Remplir les tour operators sélectionnés avec leurs informations pivot
            $this->selectedTourOperators = $poi->tourOperators()
                ->withPivot(['service_type', 'is_primary', 'notes'])
                ->get()
                ->map(function ($tourOperator) {
                    return [
                        'id' => $tourOperator->id,
                        'service_type' => $tourOperator->pivot->service_type,
                        'is_primary' => (bool) $tourOperator->pivot->is_primary,
                        'notes' => $tourOperator->pivot->notes,
                    ];
                })->toArray();
        }

        // Initialiser le modal contact dans tous les cas
        $this->resetModalContact();
        $this->resetModalTourOperator();
    }

    /**
     * Mise à jour du slug basé sur le nom en français
     */
    public function updatedTranslations()
    {
        if (empty($this->slug) && ! empty($this->translations['fr']['name'])) {
            $this->slug = Str::slug($this->translations['fr']['name']);
        }
    }

    /**
     * Gestion de la sélection hiérarchique des catégories
     */
    public function updatedSelectedCategories()
    {
        $this->applyHierarchicalSelection();
    }

    /**
     * Appliquer la logique de sélection hiérarchique
     */
    private function applyHierarchicalSelection()
    {
        $allCategories = Category::with(['children', 'parent'])->get();
        $updated = false;

        // 1. Auto-sélectionner les parents quand des enfants sont sélectionnés
        foreach ($this->selectedCategories as $categoryId) {
            $category = $allCategories->firstWhere('id', $categoryId);

            if ($category && $category->parent_id) {
                // Si c'est une sous-catégorie, s'assurer que le parent est sélectionné
                if (! in_array($category->parent_id, $this->selectedCategories)) {
                    $this->selectedCategories[] = $category->parent_id;
                    $updated = true;
                }
            }
        }

        // 2. Auto-désélectionner les enfants quand le parent est désélectionné
        $categoriesToRemove = [];
        foreach ($allCategories->whereNull('parent_id') as $parentCategory) {
            if (! in_array($parentCategory->id, $this->selectedCategories)) {
                // Parent pas sélectionné, retirer tous ses enfants
                foreach ($parentCategory->children as $child) {
                    if (in_array($child->id, $this->selectedCategories)) {
                        $categoriesToRemove[] = $child->id;
                        $updated = true;
                    }
                }
            }
        }

        // Retirer les catégories à supprimer
        if (! empty($categoriesToRemove)) {
            $this->selectedCategories = array_values(array_diff($this->selectedCategories, $categoriesToRemove));
        }

        // Supprimer les doublons et réindexer
        $this->selectedCategories = array_values(array_unique($this->selectedCategories));
    }

    /**
     * Ouvrir le modal pour ajouter un nouveau contact
     */
    public function openContactModal()
    {
        $this->resetModalContact();
        $this->editingContactIndex = null;
        $this->dispatch('open-contact-modal');
    }

    /**
     * Ouvrir le modal pour éditer un contact existant
     */
    public function editContact($index)
    {
        if (isset($this->contacts[$index])) {
            // Définir l'index d'édition
            $this->editingContactIndex = $index;

            // S'assurer que tous les champs sont présents avec des valeurs par défaut
            $contact = $this->contacts[$index];
            $this->modalContact = [
                'name' => $contact['name'] ?? '',
                'type' => $contact['type'] ?? 'general',
                'phone' => $contact['phone'] ?? '',
                'email' => $contact['email'] ?? '',
                'website' => $contact['website'] ?? '',
                'address' => $contact['address'] ?? '',
                'description' => $contact['description'] ?? '',
                'is_primary' => (bool) ($contact['is_primary'] ?? false),
            ];

            // Nettoyer les erreurs de validation précédentes
            $this->resetValidation();

            // Ouvrir le modal - les données seront affichées grâce à la liaison wire:model
            $this->dispatch('open-contact-modal');
        } else {
            session()->flash('error', 'Contact introuvable.');
        }
    }

    /**
     * Sauvegarder le contact (ajout ou modification)
     */
    public function saveContact()
    {
        try {
            // Debug: vérifier l'état avant validation
            $isEditing = $this->editingContactIndex !== null;

            // Validation du contact modal
            $this->validateModalContact();

            // Validation personnalisée de l'URL
            if (! empty($this->modalContact['website'])) {
                $website = trim($this->modalContact['website']);
                if (! preg_match('/^https?:\/\//', $website)) {
                    $testWebsite = 'https://'.$website;
                } else {
                    $testWebsite = $website;
                }

                if (! filter_var($testWebsite, FILTER_VALIDATE_URL)) {
                    $this->addError('modalContact.website', "L'adresse du site web n'est pas valide. Exemple : www.oudoum.fr");

                    return;
                }

                // Normaliser l'URL
                $this->modalContact['website'] = $testWebsite;
            }

            // S'assurer que is_primary est un booléen
            $this->modalContact['is_primary'] = (bool) ($this->modalContact['is_primary'] ?? false);

            // Si c'est le contact principal, désactiver les autres contacts principaux
            if ($this->modalContact['is_primary']) {
                foreach ($this->contacts as $i => $contact) {
                    if ($i !== $this->editingContactIndex) {
                        $this->contacts[$i]['is_primary'] = false;
                    }
                }
            }

            // Logique de sauvegarde
            if ($isEditing) {
                // Modification d'un contact existant
                if (isset($this->contacts[$this->editingContactIndex])) {
                    $this->contacts[$this->editingContactIndex] = $this->modalContact;
                } else {
                    // Index invalide, ajouter comme nouveau contact
                    $this->contacts[] = $this->modalContact;
                    $isEditing = false;
                }
            } else {
                // Ajout d'un nouveau contact
                if (empty($this->contacts)) {
                    $this->modalContact['is_primary'] = true;
                }
                $this->contacts[] = $this->modalContact;
            }

            // S'assurer qu'au moins un contact est principal
            $hasPrimary = collect($this->contacts)->contains('is_primary', true);
            if (! $hasPrimary && ! empty($this->contacts)) {
                $this->contacts[0]['is_primary'] = true;
            }

            // Fermer le modal et réinitialiser
            $this->dispatch('close-contact-modal');
            $this->resetModalContact();
            $this->editingContactIndex = null;

            // Message de succès
            session()->flash('success', $isEditing ? 'Contact mis à jour avec succès' : 'Contact ajouté avec succès');

            // Notification optionnelle
            $this->dispatch('contact-saved', ['message' => 'Contact sauvegardé avec succès !']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Les erreurs de validation sont gérées automatiquement
            throw $e;
        } catch (\Exception $e) {
            // Autres erreurs
            session()->flash('error', 'Erreur lors de la sauvegarde du contact : '.$e->getMessage());
            \Log::error('Erreur saveContact: '.$e->getMessage());
        }
    }

    /**
     * Supprimer un contact
     */
    public function removeContact($index)
    {
        if (isset($this->contacts[$index])) {
            $wasDeleted = $this->contacts[$index];
            unset($this->contacts[$index]);
            $this->contacts = array_values($this->contacts); // Réindexer

            // Si on supprime le contact principal et qu'il reste des contacts,
            // faire du premier contact restant le contact principal
            if (($wasDeleted['is_primary'] ?? false) && count($this->contacts) > 0) {
                $this->contacts[0]['is_primary'] = true;
            }

            session()->flash('success', 'Contact supprimé avec succès');
        }
    }

    /**
     * Annuler l'édition du contact (fermer modal sans sauvegarder)
     */
    public function cancelContactEdit()
    {
        $this->resetModalContact();
        $this->editingContactIndex = null;
        $this->resetValidation();
        $this->dispatch('close-contact-modal');
    }

    /**
     * Réinitialiser les données du modal contact
     */
    private function resetModalContact()
    {
        $this->modalContact = [
            'name' => '',
            'type' => 'general',
            'phone' => '',
            'email' => '',
            'website' => '',
            'address' => '',
            'description' => '',
            'is_primary' => false,
        ];

        // Nettoyer les erreurs de validation du modal
        $this->resetValidation([
            'modalContact.name',
            'modalContact.type',
            'modalContact.phone',
            'modalContact.email',
            'modalContact.website',
            'modalContact.address',
            'modalContact.description',
            'modalContact.is_primary',
        ]);
    }

    /**
     * Réinitialiser les données du modal tour operator
     */
    private function resetModalTourOperator()
    {
        $this->modalTourOperator = [
            'tour_operator_id' => null,
            'service_type' => 'guide',
            'is_primary' => false,
            'notes' => '',
        ];

        // Nettoyer les erreurs de validation du modal
        $this->resetValidation([
            'modalTourOperator.tour_operator_id',
            'modalTourOperator.service_type',
            'modalTourOperator.is_primary',
            'modalTourOperator.notes',
        ]);
    }

    /**
     * Ouvrir le modal pour ajouter un tour operator
     */
    public function openTourOperatorModal()
    {
        $this->resetModalTourOperator();
        $this->editingTourOperatorIndex = null;
        $this->dispatch('open-tour-operator-modal');
    }

    /**
     * Éditer un tour operator existant
     */
    public function editTourOperator($index)
    {
        if (isset($this->selectedTourOperators[$index])) {
            $this->editingTourOperatorIndex = $index;
            $tourOperator = $this->selectedTourOperators[$index];

            $this->modalTourOperator = [
                'tour_operator_id' => $tourOperator['id'],
                'service_type' => $tourOperator['service_type'] ?? 'guide',
                'is_primary' => (bool) ($tourOperator['is_primary'] ?? false),
                'notes' => $tourOperator['notes'] ?? '',
            ];

            $this->resetValidation();
            $this->dispatch('open-tour-operator-modal');
        }
    }

    /**
     * Sauvegarder le tour operator
     */
    public function saveTourOperator()
    {
        // Validation
        $this->validate([
            'modalTourOperator.tour_operator_id' => 'required|exists:tour_operators,id',
            'modalTourOperator.service_type' => 'required|in:guide,transport,full_package,accommodation,activity,other',
            'modalTourOperator.is_primary' => 'boolean',
            'modalTourOperator.notes' => 'nullable|string|max:500',
        ]);

        // Vérifier que ce tour operator n'est pas déjà ajouté
        $existingIndex = collect($this->selectedTourOperators)->search(function ($item) {
            return $item['id'] == $this->modalTourOperator['tour_operator_id'];
        });

        if ($existingIndex !== false && $existingIndex !== $this->editingTourOperatorIndex) {
            $this->addError('modalTourOperator.tour_operator_id', 'Ce tour operator est déjà associé à ce POI.');

            return;
        }

        // Si c'est le tour operator principal, désactiver les autres principaux
        if ($this->modalTourOperator['is_primary']) {
            foreach ($this->selectedTourOperators as $i => $operator) {
                if ($i !== $this->editingTourOperatorIndex) {
                    $this->selectedTourOperators[$i]['is_primary'] = false;
                }
            }
        }

        $tourOperatorData = [
            'id' => $this->modalTourOperator['tour_operator_id'],
            'service_type' => $this->modalTourOperator['service_type'],
            'is_primary' => (bool) $this->modalTourOperator['is_primary'],
            'notes' => $this->modalTourOperator['notes'],
        ];

        if ($this->editingTourOperatorIndex !== null) {
            // Modification
            $this->selectedTourOperators[$this->editingTourOperatorIndex] = $tourOperatorData;
            session()->flash('success', 'Tour operator mis à jour avec succès.');
        } else {
            // Ajout
            if (empty($this->selectedTourOperators)) {
                $tourOperatorData['is_primary'] = true;
            }
            $this->selectedTourOperators[] = $tourOperatorData;
            session()->flash('success', 'Tour operator ajouté avec succès.');
        }

        // S'assurer qu'au moins un tour operator est principal
        $hasPrimary = collect($this->selectedTourOperators)->contains('is_primary', true);
        if (! $hasPrimary && ! empty($this->selectedTourOperators)) {
            $this->selectedTourOperators[0]['is_primary'] = true;
        }

        $this->dispatch('close-tour-operator-modal');
        $this->resetModalTourOperator();
        $this->editingTourOperatorIndex = null;
    }

    /**
     * Supprimer un tour operator
     */
    public function removeTourOperator($index)
    {
        if (isset($this->selectedTourOperators[$index])) {
            $wasDeleted = $this->selectedTourOperators[$index];
            unset($this->selectedTourOperators[$index]);
            $this->selectedTourOperators = array_values($this->selectedTourOperators); // Réindexer

            // Si on supprime le tour operator principal et qu'il reste des operators
            if (($wasDeleted['is_primary'] ?? false) && count($this->selectedTourOperators) > 0) {
                $this->selectedTourOperators[0]['is_primary'] = true;
            }

            session()->flash('success', 'Tour operator supprimé avec succès.');
        }
    }

    /**
     * Annuler l'édition du tour operator
     */
    public function cancelTourOperatorEdit()
    {
        $this->resetModalTourOperator();
        $this->editingTourOperatorIndex = null;
        $this->resetValidation();
        $this->dispatch('close-tour-operator-modal');
    }

    /**
     * Obtenir les types de contact disponibles
     */
    public function getContactTypes()
    {
        return [
            'general' => 'Contact général',
            'restaurant' => 'Restaurant',
            'tour_operator' => 'Opérateur de tourisme',
            'guide' => 'Guide local',
            'accommodation' => 'Hébergement',
            'park_office' => 'Bureau du parc',
            'emergency' => 'Urgence',
            'transport' => 'Transport',
            'shop' => 'Boutique/Commerce',
            'other' => 'Autre',
        ];
    }

    /**
     * Obtenir l'icône pour un type de contact
     */
    public function getContactTypeIcon($type)
    {
        $icons = [
            'general' => 'fas fa-phone',
            'restaurant' => 'fas fa-utensils',
            'tour_operator' => 'fas fa-map-marked-alt',
            'guide' => 'fas fa-user-tie',
            'accommodation' => 'fas fa-bed',
            'park_office' => 'fas fa-tree',
            'emergency' => 'fas fa-ambulance',
            'transport' => 'fas fa-bus',
            'shop' => 'fas fa-shopping-bag',
            'other' => 'fas fa-info-circle',
        ];

        return $icons[$type] ?? 'fas fa-phone';
    }

    /**
     * Obtenir la couleur pour un type de contact
     */
    public function getContactTypeColor($type)
    {
        $colors = [
            'general' => '#6c757d',
            'restaurant' => '#fd7e14',
            'tour_operator' => '#20c997',
            'guide' => '#0d6efd',
            'accommodation' => '#6f42c1',
            'park_office' => '#198754',
            'emergency' => '#dc3545',
            'transport' => '#ffc107',
            'shop' => '#e91e63',
            'other' => '#6c757d',
        ];

        return $colors[$type] ?? '#6c757d';
    }

    /**
     * Obtenir les types de service pour tour operators
     */
    public function getServiceTypes()
    {
        $locale = $this->activeLocale ?? 'fr';

        $types = [
            'fr' => [
                'guide' => 'Guide touristique',
                'transport' => 'Transport',
                'full_package' => 'Package complet',
                'accommodation' => 'Hébergement',
                'activity' => 'Activité spécialisée',
                'other' => 'Autre service',
            ],
            'en' => [
                'guide' => 'Tour Guide',
                'transport' => 'Transportation',
                'full_package' => 'Complete Package',
                'accommodation' => 'Accommodation',
                'activity' => 'Specialized Activity',
                'other' => 'Other Service',
            ],
            'ar' => [
                'guide' => 'مرشد سياحي',
                'transport' => 'نقل',
                'full_package' => 'حزمة كاملة',
                'accommodation' => 'إقامة',
                'activity' => 'نشاط متخصص',
                'other' => 'خدمة أخرى',
            ],
        ];

        return $types[$locale] ?? $types['fr'];
    }

    /**
     * Obtenir l'icône pour un type de service
     */
    public function getServiceTypeIcon($type)
    {
        $icons = [
            'guide' => 'fas fa-user-tie',
            'transport' => 'fas fa-bus',
            'full_package' => 'fas fa-suitcase',
            'accommodation' => 'fas fa-bed',
            'activity' => 'fas fa-hiking',
            'other' => 'fas fa-concierge-bell',
        ];

        return $icons[$type] ?? 'fas fa-concierge-bell';
    }

    /**
     * Obtenir la couleur pour un type de service
     */
    public function getServiceTypeColor($type)
    {
        $colors = [
            'guide' => '#0d6efd',
            'transport' => '#ffc107',
            'full_package' => '#20c997',
            'accommodation' => '#6f42c1',
            'activity' => '#fd7e14',
            'other' => '#6c757d',
        ];

        return $colors[$type] ?? '#6c757d';
    }

    /**
     * Obtenir les textes de l'interface selon la langue active
     */
    public function getInterfaceTexts()
    {
        $locale = $this->activeLocale ?? 'fr';

        $texts = [
            'fr' => [
                'tour_operators_associated' => 'Tour Operators Associés',
                'associate_tour_operator' => 'Associer un tour operator',
                'no_tour_operator_associated' => 'Aucun tour operator associé',
                'associate_first_tour_operator' => 'Associer le premier tour operator',
                'enrich_visitor_experience' => 'Associez des tour operators pour enrichir l\'expérience des visiteurs',
                'modify_association' => 'Modifier l\'association',
                'delete_association' => 'Supprimer cette association ?',
                'principal' => 'Principal',
            ],
            'en' => [
                'tour_operators_associated' => 'Associated Tour Operators',
                'associate_tour_operator' => 'Associate Tour Operator',
                'no_tour_operator_associated' => 'No tour operator associated',
                'associate_first_tour_operator' => 'Associate first tour operator',
                'enrich_visitor_experience' => 'Associate tour operators to enrich visitor experience',
                'modify_association' => 'Modify association',
                'delete_association' => 'Delete this association?',
                'principal' => 'Primary',
            ],
            'ar' => [
                'tour_operators_associated' => 'مشغلي الجولات المرتبطين',
                'associate_tour_operator' => 'ربط مشغل جولة',
                'no_tour_operator_associated' => 'لا يوجد مشغل جولة مرتبط',
                'associate_first_tour_operator' => 'ربط أول مشغل جولة',
                'enrich_visitor_experience' => 'اربط مشغلي الجولات لإثراء تجربة الزوار',
                'modify_association' => 'تعديل الارتباط',
                'delete_association' => 'حذف هذا الارتباط؟',
                'principal' => 'أساسي',
            ],
        ];

        return $texts[$locale] ?? $texts['fr'];
    }

    /**
     * Normaliser les URLs dans les contacts avant sauvegarde
     */
    private function normalizeContactUrls()
    {
        foreach ($this->contacts as &$contact) {
            if (! empty($contact['website'])) {
                $website = trim($contact['website']);

                // Si l'URL ne commence pas par http:// ou https://, ajouter https://
                if (! preg_match('/^https?:\/\//', $website)) {
                    $contact['website'] = 'https://'.$website;
                }
            }
        }
    }

    /**
     * Validation spécifique au modal de contact
     */
    private function validateModalContact()
    {
        return $this->validate([
            'modalContact.name' => 'required|string|max:255',
            'modalContact.type' => 'required|string|max:100',
            'modalContact.phone' => 'nullable|string|max:20',
            'modalContact.email' => 'nullable|email|max:255',
            'modalContact.website' => 'nullable|string|max:255',
            'modalContact.address' => 'nullable|string|max:500',
            'modalContact.description' => 'nullable|string|max:1000',
            'modalContact.is_primary' => 'boolean',
        ]);
    }

    /**
     * Validation personnalisée pour les URLs de contacts
     */
    public function validateContactWebsites()
    {
        foreach ($this->contacts as $index => $contact) {
            if (! empty($contact['website'])) {
                $website = trim($contact['website']);

                // Ajouter https:// si pas de protocole pour la validation
                if (! preg_match('/^https?:\/\//', $website)) {
                    $website = 'https://'.$website;
                }

                // Valider l'URL complète
                if (! filter_var($website, FILTER_VALIDATE_URL)) {
                    $this->addError("contacts.{$index}.website", "L'adresse du site web n'est pas valide.");

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Validation en temps réel des URLs de contacts
     */
    public function updatedContacts($value, $key)
    {
        // Si c'est un champ website qui a été modifié
        if (strpos($key, '.website') !== false) {
            // Extraire l'index du contact
            preg_match('/^(\d+)\.website$/', $key, $matches);
            if (isset($matches[1])) {
                $index = $matches[1];
                $this->clearValidationErrors("contacts.{$index}.website");

                if (! empty($value)) {
                    $website = trim($value);

                    // Ajouter https:// si pas de protocole pour la validation
                    if (! preg_match('/^https?:\/\//', $website)) {
                        $testWebsite = 'https://'.$website;
                    } else {
                        $testWebsite = $website;
                    }

                    // Valider l'URL
                    if (! filter_var($testWebsite, FILTER_VALIDATE_URL)) {
                        $this->addError("contacts.{$index}.website", "L'adresse du site web n'est pas valide. Exemple : www.oudoum.fr");
                    }
                }
            }
        }
    }

    /**
     * Supprimer les erreurs de validation spécifiques
     */
    private function clearValidationErrors($field)
    {
        $errors = $this->getErrorBag();
        if ($errors->has($field)) {
            $errors->forget($field);
        }
    }

    /**
     * Enregistrer le POI avec ses traductions
     */
    public function save()
    {
        try {
            // Validation standard
            $this->validate();

            // Validation personnalisée des URLs de contacts
            if (! $this->validateContactWebsites()) {
                return;
            }

            // Normaliser les URLs des contacts
            $this->normalizeContactUrls();

            // Générer le slug s'il n'est pas fourni
            if (empty($this->slug) && ! empty($this->translations['fr']['name'])) {
                $this->slug = Str::slug($this->translations['fr']['name']);
            }

            // Créer ou mettre à jour le POI
            if ($this->isEditMode) {
                $poi = Poi::findOrFail($this->poiId);

                $poi->update([
                    'slug' => $this->slug,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'region' => $this->region,
                    'contacts' => $this->contacts,
                    'website' => $this->website,
                    'is_featured' => $this->is_featured,
                    'allow_reservations' => $this->allow_reservations,
                    'status' => $this->status,
                    'featured_image_id' => $this->featuredImageId,
                ]);

                // Mettre à jour les traductions
                foreach ($this->translations as $locale => $translation) {
                    $poi->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name' => $translation['name'],
                            'description' => $translation['description'],
                            'short_description' => $translation['short_description'],
                            'address' => $translation['address'],
                            'opening_hours' => $translation['opening_hours'],
                            'entry_fee' => $translation['entry_fee'],
                            'tips' => $translation['tips'],
                        ]
                    );
                }

                // Mettre à jour les catégories
                $poi->categories()->sync($this->selectedCategories);

                // Mettre à jour les médias
                $mediaData = [];
                foreach ($this->selectedMedia as $index => $mediaId) {
                    $mediaData[$mediaId] = ['order' => $index];
                }
                $poi->media()->sync($mediaData);

                // Mettre à jour les tour operators
                $tourOperatorData = [];
                foreach ($this->selectedTourOperators as $tourOperator) {
                    $tourOperatorData[$tourOperator['id']] = [
                        'service_type' => $tourOperator['service_type'],
                        'is_primary' => $tourOperator['is_primary'],
                        'is_active' => true,
                        'notes' => $tourOperator['notes'] ?? null,
                    ];
                }
                $poi->tourOperators()->sync($tourOperatorData);

                session()->flash('success', 'Point d\'intérêt mis à jour avec succès.');
            } else {
                $poi = Poi::create([
                    'slug' => $this->slug,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'region' => $this->region,
                    'contacts' => $this->contacts,
                    'website' => $this->website,
                    'is_featured' => $this->is_featured,
                    'allow_reservations' => $this->allow_reservations,
                    'status' => $this->status,
                    'featured_image_id' => $this->featuredImageId,
                    'creator_id' => Auth::guard('admin')->id(),
                ]);

                // Créer les traductions
                foreach ($this->translations as $locale => $translation) {
                    $poi->translations()->create([
                        'locale' => $locale,
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                        'short_description' => $translation['short_description'],
                        'address' => $translation['address'],
                        'opening_hours' => $translation['opening_hours'],
                        'entry_fee' => $translation['entry_fee'],
                        'tips' => $translation['tips'],
                    ]);
                }

                // Attacher les catégories
                $poi->categories()->attach($this->selectedCategories);

                // Attacher les médias
                $mediaData = [];
                foreach ($this->selectedMedia as $index => $mediaId) {
                    $mediaData[$mediaId] = ['order' => $index];
                }
                $poi->media()->attach($mediaData);

                // Attacher les tour operators
                $tourOperatorData = [];
                foreach ($this->selectedTourOperators as $tourOperator) {
                    $tourOperatorData[$tourOperator['id']] = [
                        'service_type' => $tourOperator['service_type'],
                        'is_primary' => $tourOperator['is_primary'],
                        'is_active' => true,
                        'notes' => $tourOperator['notes'] ?? null,
                    ];
                }
                $poi->tourOperators()->attach($tourOperatorData);

                session()->flash('success', 'Point d\'intérêt créé avec succès.');
            }

            // Rediriger vers la liste des POI
            return redirect()->route('pois.index');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la sauvegarde du POI: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'poiId' => $this->poiId ?? null,
                'isEditMode' => $this->isEditMode,
            ]);

            session()->flash('error', 'Erreur lors de la sauvegarde : '.$e->getMessage());

            return;
        }
    }

    /**
     * Obtenir la liste des régions
     */
    public function getRegionsList()
    {
        return [
            'Djibouti' => 'Djibouti',
            'Ali Sabieh' => 'Ali Sabieh',
            'Dikhil' => 'Dikhil',
            'Tadjourah' => 'Tadjourah',
            'Obock' => 'Obock',
            'Arta' => 'Arta',
        ];
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        // Récupérer les catégories principales avec leurs sous-catégories
        $parentCategories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true);
            }, 'translations' => function ($query) {
                $query->where('locale', $this->activeLocale)
                    ->orWhere('locale', config('app.fallback_locale', 'fr'));
            }, 'children.translations' => function ($query) {
                $query->where('locale', $this->activeLocale)
                    ->orWhere('locale', config('app.fallback_locale', 'fr'));
            }])
            ->get()
            ->sortBy(function ($category) {
                $translation = $category->translation($this->activeLocale);

                return $translation ? $translation->name : '';
            });

        $regions = $this->getRegionsList();

        // Récupérer les médias disponibles
        $media = Media::orderBy('created_at', 'desc')->get();

        // Récupérer les tour operators disponibles
        $tourOperators = \App\Models\TourOperator::where('is_active', true)
            ->with('translations')
            ->orderBy('featured', 'desc')
            ->get()
            ->sortBy(function ($operator) {
                return $operator->name;
            });

        return view('livewire.admin.poi.poi-form', [
            'parentCategories' => $parentCategories,
            'regions' => $regions,
            'media' => $media,
            'tourOperators' => $tourOperators,
            'availableLocales' => ['fr', 'en'],
        ]);
    }
}
