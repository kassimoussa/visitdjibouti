<?php

namespace App\Livewire\Admin;

use App\Models\Embassy;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class EmbassyManager extends Component
{
    use WithPagination;

    public $showModal = false;
    public $modalMode = 'create'; // create or edit
    public $modalTitle = 'Nouvelle ambassade';
    public $embassyId = null;

    // Propriétés du formulaire principal
    public $type = 'foreign_in_djibouti';
    public $country_code = '';
    public $phones = '';
    public $emails = '';
    public $fax = '';
    public $website = '';
    public $ld = '';
    public $latitude = null;
    public $longitude = null;
    public $is_active = true;

    // Traductions
    public $translations = [
        'fr' => [
            'name' => '',
            'ambassador_name' => '',
            'address' => '',
            'postal_box' => '',
        ],
        'en' => [
            'name' => '',
            'ambassador_name' => '',
            'address' => '',
            'postal_box' => '',
        ],
        'ar' => [
            'name' => '',
            'ambassador_name' => '',
            'address' => '',
            'postal_box' => '',
        ],
    ];

    public $availableLocales = ['fr', 'en', 'ar'];
    public $currentLocale = 'fr';

    // Filtres et recherche
    public $search = '';
    public $filterType = '';
    public $filterLocale = 'fr'; // Langue pour l'affichage de la liste

    protected function rules()
    {
        $rules = [
            'type' => 'required|in:foreign_in_djibouti,djiboutian_abroad',
            'country_code' => 'nullable|string|max:10',
            'phones' => 'nullable|string|max:255',
            'emails' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
            'ld' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
        ];

        // Règles pour les traductions
        foreach ($this->availableLocales as $locale) {
            $isRequired = ($locale === 'fr') ? 'required' : 'nullable';
            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.ambassador_name"] = 'nullable|string|max:255';
            $rules["translations.{$locale}.address"] = 'nullable|string|max:1000';
            $rules["translations.{$locale}.postal_box"] = 'nullable|string|max:100';
        }

        return $rules;
    }

    protected $messages = [
        'type.required' => 'Le type d\'ambassade est obligatoire.',
        'translations.fr.name.required' => 'Le nom en français est obligatoire.',
        'website.url' => 'Le site web doit être une URL valide.',
        'emails.email' => 'Les emails doivent être valides.',
        'latitude.between' => 'La latitude doit être entre -90 et 90.',
        'longitude.between' => 'La longitude doit être entre -180 et 180.',
    ];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $embassies = Embassy::with(['translations'])
            ->when($this->search, function ($query) {
                $query->whereHas('translations', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('ambassador_name', 'like', '%' . $this->search . '%')
                      ->orWhere('address', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, function ($query) {
                $query->where('type', $this->filterType);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.settings.embassy-manager', [
            'embassies' => $embassies,
            'types' => Embassy::TYPES,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
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
        $this->modalTitle = 'Nouvelle ambassade';
        $this->showModal = true;
    }

    public function openEditModal($embassyId)
    {
        $embassy = Embassy::with('translations')->findOrFail($embassyId);
        
        $this->embassyId = $embassy->id;
        $this->type = $embassy->type;
        $this->country_code = $embassy->country_code;
        $this->phones = $embassy->phones;
        $this->emails = $embassy->emails;
        $this->fax = $embassy->fax;
        $this->website = $embassy->website;
        $this->ld = $embassy->ld;
        $this->latitude = $embassy->latitude;
        $this->longitude = $embassy->longitude;
        $this->is_active = $embassy->is_active;

        // Initialiser les traductions vides d'abord
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'ambassador_name' => '',
                'address' => '',
                'postal_box' => '',
            ];
        }

        // Charger les traductions existantes
        foreach ($embassy->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name ?? '',
                'ambassador_name' => $translation->ambassador_name ?? '',
                'address' => $translation->address ?? '',
                'postal_box' => $translation->postal_box ?? '',
            ];
        }
        
        $this->modalMode = 'edit';
        $this->modalTitle = 'Modifier l\'ambassade';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'type' => $this->type,
                'country_code' => $this->country_code,
                'phones' => $this->phones,
                'emails' => $this->emails,
                'fax' => $this->fax,
                'website' => $this->website,
                'ld' => $this->ld,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'is_active' => $this->is_active,
            ];

            if ($this->modalMode === 'create') {
                $embassy = Embassy::create($data);
                session()->flash('message', 'Ambassade créée avec succès.');
            } else {
                $embassy = Embassy::findOrFail($this->embassyId);
                $embassy->update($data);
                session()->flash('message', 'Ambassade modifiée avec succès.');
            }

            // Sauvegarder les traductions
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['name'])) {
                    $embassy->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }


    public function switchLocale($locale)
    {
        $this->currentLocale = $locale;
    }

    public function toggleStatus($embassyId)
    {
        try {
            $embassy = Embassy::findOrFail($embassyId);
            $embassy->update(['is_active' => !$embassy->is_active]);
            
            $status = $embassy->is_active ? 'activée' : 'désactivée';
            session()->flash('message', "Ambassade {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function delete($embassyId)
    {
        try {
            $embassy = Embassy::findOrFail($embassyId);
            $embassy->delete();
            
            session()->flash('message', 'Ambassade supprimée avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->type = 'foreign_in_djibouti';
        $this->country_code = '';
        $this->phones = '';
        $this->emails = '';
        $this->fax = '';
        $this->website = '';
        $this->ld = '';
        $this->latitude = null;
        $this->longitude = null;
        $this->is_active = true;
        $this->embassyId = null;
        $this->currentLocale = 'fr';

        // Reset traductions
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'ambassador_name' => '',
                'address' => '',
                'postal_box' => '',
            ];
        }

        $this->resetValidation();
    }
}