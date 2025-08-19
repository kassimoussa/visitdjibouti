<?php

namespace App\Livewire\Admin\_Development;

use App\Models\TourOperator;
use App\Models\TourOperatorService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Str;

class TourOperatorManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $modalMode = 'create';
    public $modalTitle = 'Nouvel opérateur de tour';
    public $tourOperatorId = null;

    // Propriétés du formulaire principal
    public $license_number = '';
    public $certification_type = 'local';
    public $phone = '';
    public $email = '';
    public $website = '';
    public $fax = '';
    public $languages_spoken = '';
    public $address = '';
    public $latitude = null;
    public $longitude = null;
    public $logo_id = null;
    public $is_active = true;
    public $featured = false;
    public $min_price = null;
    public $max_price = null;
    public $currency = 'USD';
    public $price_range = 'mid-range';
    public $opening_hours = '';
    public $years_experience = null;
    public $max_group_size = null;
    public $emergency_contact_available = false;

    // Traductions
    public $translations = [
        'fr' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
            'services' => '',
            'specialties' => '',
            'about_text' => '',
            'booking_conditions' => '',
        ],
        'en' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
            'services' => '',
            'specialties' => '',
            'about_text' => '',
            'booking_conditions' => '',
        ],
        'ar' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
            'services' => '',
            'specialties' => '',
            'about_text' => '',
            'booking_conditions' => '',
        ],
    ];

    // Services sélectionnés
    public $selectedServices = [];

    public $availableLocales = ['fr', 'en', 'ar'];
    public $currentLocale = 'fr';

    // Filtres et recherche
    public $search = '';
    public $filterCertification = '';
    public $filterPriceRange = '';
    public $filterFeatured = '';
    public $filterLocale = 'fr';

    protected function rules()
    {
        $rules = [
            'license_number' => 'nullable|string|max:100',
            'certification_type' => 'required|in:local,national,international',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:100',
            'languages_spoken' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo_id' => 'nullable|exists:media,id',
            'is_active' => 'boolean',
            'featured' => 'boolean',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'currency' => 'nullable|string|max:3',
            'price_range' => 'nullable|in:budget,mid-range,luxury,premium',
            'opening_hours' => 'nullable|string|max:1000',
            'years_experience' => 'nullable|integer|min:0|max:100',
            'max_group_size' => 'nullable|integer|min:1|max:1000',
            'emergency_contact_available' => 'boolean',
        ];

        // Règles pour les traductions
        foreach ($this->availableLocales as $locale) {
            $isRequired = ($locale === 'fr') ? 'required' : 'nullable';
            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = 'nullable|string|max:2000';
            $rules["translations.{$locale}.address_translated"] = 'nullable|string|max:1000';
            $rules["translations.{$locale}.services"] = 'nullable|string|max:1000';
            $rules["translations.{$locale}.specialties"] = 'nullable|string|max:1000';
            $rules["translations.{$locale}.about_text"] = 'nullable|string|max:2000';
            $rules["translations.{$locale}.booking_conditions"] = 'nullable|string|max:2000';
        }

        return $rules;
    }

    protected $messages = [
        'certification_type.required' => 'Le type de certification est obligatoire.',
        'translations.fr.name.required' => 'Le nom en français est obligatoire.',
        'email.email' => 'L\'email doit être valide.',
        'website.url' => 'Le site web doit être une URL valide.',
        'latitude.between' => 'La latitude doit être entre -90 et 90.',
        'longitude.between' => 'La longitude doit être entre -180 et 180.',
        'max_price.gte' => 'Le prix maximum doit être supérieur ou égal au prix minimum.',
    ];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $tourOperators = TourOperator::with(['translations', 'logo', 'services'])
            ->when($this->search, function ($query) {
                $query->whereHas('translations', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('services', 'like', '%' . $this->search . '%')
                      ->orWhere('specialties', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterCertification, function ($query) {
                $query->where('certification_type', $this->filterCertification);
            })
            ->when($this->filterPriceRange, function ($query) {
                $query->where('price_range', $this->filterPriceRange);
            })
            ->when($this->filterFeatured !== '', function ($query) {
                $query->where('featured', $this->filterFeatured);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin._development.tour-operator-manager', [
            'tourOperators' => $tourOperators,
            'certificationTypes' => TourOperator::CERTIFICATION_TYPES,
            'priceRanges' => TourOperator::PRICE_RANGES,
            'serviceTypes' => TourOperator::SERVICE_TYPES,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCertification()
    {
        $this->resetPage();
    }

    public function updatingFilterPriceRange()
    {
        $this->resetPage();
    }

    public function updatingFilterFeatured()
    {
        $this->resetPage();
    }

    public function updatingFilterLocale()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->modalTitle = 'Nouvel opérateur de tour';
        $this->showModal = true;
    }

    public function openEditModal($tourOperatorId)
    {
        $tourOperator = TourOperator::with(['translations', 'services'])->findOrFail($tourOperatorId);
        
        $this->tourOperatorId = $tourOperator->id;
        $this->license_number = $tourOperator->license_number ?? '';
        $this->certification_type = $tourOperator->certification_type;
        $this->phone = $tourOperator->phone ?? '';
        $this->email = $tourOperator->email ?? '';
        $this->website = $tourOperator->website ?? '';
        $this->fax = $tourOperator->fax ?? '';
        $this->languages_spoken = $tourOperator->languages_spoken ?? '';
        $this->address = $tourOperator->address ?? '';
        $this->latitude = $tourOperator->latitude;
        $this->longitude = $tourOperator->longitude;
        $this->logo_id = $tourOperator->logo_id;
        $this->is_active = $tourOperator->is_active;
        $this->featured = $tourOperator->featured;
        $this->min_price = $tourOperator->min_price;
        $this->max_price = $tourOperator->max_price;
        $this->currency = $tourOperator->currency ?? 'USD';
        $this->price_range = $tourOperator->price_range ?? 'mid-range';
        $this->opening_hours = $tourOperator->opening_hours ?? '';
        $this->years_experience = $tourOperator->years_experience;
        $this->max_group_size = $tourOperator->max_group_size;
        $this->emergency_contact_available = $tourOperator->emergency_contact_available;

        // Initialiser les traductions vides d'abord
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => '',
                'address_translated' => '',
                'services' => '',
                'specialties' => '',
                'about_text' => '',
                'booking_conditions' => '',
            ];
        }

        // Charger les traductions existantes
        foreach ($tourOperator->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name ?? '',
                'description' => $translation->description ?? '',
                'address_translated' => $translation->address_translated ?? '',
                'services' => $translation->services ?? '',
                'specialties' => $translation->specialties ?? '',
                'about_text' => $translation->about_text ?? '',
                'booking_conditions' => $translation->booking_conditions ?? '',
            ];
        }

        // Charger les services sélectionnés
        $this->selectedServices = $tourOperator->services->pluck('service_type')->toArray();
        
        $this->modalMode = 'edit';
        $this->modalTitle = 'Modifier l\'opérateur de tour';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $slug = $this->generateSlug($this->translations['fr']['name']);

            $data = [
                'slug' => $slug,
                'license_number' => $this->license_number,
                'certification_type' => $this->certification_type,
                'phone' => $this->phone,
                'email' => $this->email,
                'website' => $this->website,
                'fax' => $this->fax,
                'languages_spoken' => $this->languages_spoken,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'logo_id' => $this->logo_id,
                'is_active' => $this->is_active,
                'featured' => $this->featured,
                'min_price' => $this->min_price,
                'max_price' => $this->max_price,
                'currency' => $this->currency,
                'price_range' => $this->price_range,
                'opening_hours' => $this->opening_hours,
                'years_experience' => $this->years_experience,
                'max_group_size' => $this->max_group_size,
                'emergency_contact_available' => $this->emergency_contact_available,
            ];

            if ($this->modalMode === 'create') {
                $tourOperator = TourOperator::create($data);
                session()->flash('message', 'Opérateur de tour créé avec succès.');
            } else {
                $tourOperator = TourOperator::findOrFail($this->tourOperatorId);
                $tourOperator->update($data);
                session()->flash('message', 'Opérateur de tour modifié avec succès.');
            }

            // Sauvegarder les traductions
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['name'])) {
                    $tourOperator->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }

            // Sauvegarder les services
            $tourOperator->services()->delete(); // Supprimer les anciens services
            foreach ($this->selectedServices as $serviceType) {
                $tourOperator->services()->create([
                    'service_type' => $serviceType,
                    'is_primary' => true, // Par défaut, tous sont considérés comme principaux
                ]);
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function toggleStatus($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['is_active' => !$tourOperator->is_active]);
            
            $status = $tourOperator->is_active ? 'activé' : 'désactivé';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function toggleFeatured($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['featured' => !$tourOperator->featured]);
            
            $status = $tourOperator->featured ? 'mis en avant' : 'retiré de la mise en avant';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut featured: ' . $e->getMessage());
        }
    }

    public function delete($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->delete();
            
            session()->flash('message', 'Opérateur de tour supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function switchLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    #[On('media-selected')]
    public function onMediaSelected($mediaIds)
    {
        $this->logo_id = is_array($mediaIds) && count($mediaIds) > 0 ? $mediaIds[0] : $mediaIds;
    }

    public function openMediaSelector()
    {
        $preselected = $this->logo_id ? [$this->logo_id] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->license_number = '';
        $this->certification_type = 'local';
        $this->phone = '';
        $this->email = '';
        $this->website = '';
        $this->fax = '';
        $this->languages_spoken = '';
        $this->address = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->logo_id = null;
        $this->is_active = true;
        $this->featured = false;
        $this->min_price = null;
        $this->max_price = null;
        $this->currency = 'USD';
        $this->price_range = 'mid-range';
        $this->opening_hours = '';
        $this->years_experience = null;
        $this->max_group_size = null;
        $this->emergency_contact_available = false;
        $this->tourOperatorId = null;
        $this->currentLocale = 'fr';
        $this->selectedServices = [];

        // Reset traductions
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => '',
                'address_translated' => '',
                'services' => '',
                'specialties' => '',
                'about_text' => '',
                'booking_conditions' => '',
            ];
        }

        $this->resetValidation();
    }

    private function generateSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (TourOperator::where('slug', $slug)->where('id', '!=', $this->tourOperatorId)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}