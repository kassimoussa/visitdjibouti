<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperator;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class TourOperatorForm extends Component
{
    public $tourOperatorId = null;

    public $isEditing = false;

    public $isOperatorMode = false; // True si utilisé par un opérateur (désactive is_active et featured)

    // Propriétés du formulaire principal
    public $phones = '';

    public $emails = '';

    public $website = '';

    public $address = '';

    public $latitude = null;

    public $longitude = null;

    public $logo_id = null;

    public $is_active = true;

    public $featured = false;

    // Traductions
    public $translations = [
        'fr' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
        ],
        'en' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
        ],
        'ar' => [
            'name' => '',
            'description' => '',
            'address_translated' => '',
        ],
    ];

    // Galerie d'images
    public $selectedMedia = [];

    public $availableLocales = ['fr', 'en'];

    public $activeLocale = 'fr';

    protected function rules()
    {
        $rules = [
            'phones' => 'nullable|string|max:500',
            'emails' => 'nullable|string|max:500',
            'website' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo_id' => 'nullable|exists:media,id',
            'is_active' => 'boolean',
            'featured' => 'boolean',
        ];

        // Règles pour les traductions
        foreach ($this->availableLocales as $locale) {
            $isRequired = ($locale === 'fr') ? 'required' : 'nullable';
            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = 'nullable|string|max:2000';
            $rules["translations.{$locale}.address_translated"] = 'nullable|string|max:1000';
        }

        return $rules;
    }

    protected $messages = [
        'translations.fr.name.required' => 'Le nom en français est obligatoire.',
        'latitude.between' => 'La latitude doit être entre -90 et 90.',
        'longitude.between' => 'La longitude doit être entre -180 et 180.',
    ];

    public function mount($tourOperatorId = null, $isOperatorMode = false)
    {
        $this->isOperatorMode = $isOperatorMode;

        if ($tourOperatorId) {
            $this->tourOperatorId = $tourOperatorId;
            $this->isEditing = true;
            $this->loadTourOperator();
        } elseif ($this->isOperatorMode) {
            // Si mode opérateur sans ID, charger l'opérateur de l'utilisateur connecté
            $user = \Illuminate\Support\Facades\Auth::guard('operator')->user();
            $this->tourOperatorId = $user->tour_operator_id;
            $this->isEditing = true;
            $this->loadTourOperator();
        }
    }

    public function render()
    {
        return view('livewire.admin.tour-operator.tour-operator-form');
    }

    public function loadTourOperator()
    {
        $tourOperator = TourOperator::with(['translations', 'media'])->findOrFail($this->tourOperatorId);

        $this->phones = $tourOperator->phones ?? '';
        $this->emails = $tourOperator->emails ?? '';
        $this->website = $tourOperator->website ?? '';
        $this->address = $tourOperator->address ?? '';
        $this->latitude = $tourOperator->latitude;
        $this->longitude = $tourOperator->longitude;
        $this->logo_id = $tourOperator->logo_id;
        $this->is_active = $tourOperator->is_active;
        $this->featured = $tourOperator->featured;

        // Initialiser les traductions vides d'abord
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => '',
                'address_translated' => '',
            ];
        }

        // Charger les traductions existantes
        foreach ($tourOperator->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name ?? '',
                'description' => $translation->description ?? '',
                'address_translated' => $translation->address_translated ?? '',
            ];
        }

        // Charger les médias sélectionnés pour la galerie
        $this->selectedMedia = $tourOperator->media->pluck('id')->toArray();
    }

    public function save()
    {
        $this->validate();

        try {
            $slug = $this->generateSlug($this->translations['fr']['name']);

            $data = [
                'slug' => $slug,
                'phones' => $this->phones,
                'emails' => $this->emails,
                'website' => $this->website,
                'address' => $this->address,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'logo_id' => $this->logo_id,
                'is_active' => $this->is_active,
                'featured' => $this->featured,
            ];

            if ($this->isEditing) {
                $tourOperator = TourOperator::findOrFail($this->tourOperatorId);
                $tourOperator->update($data);
                session()->flash('message', 'Opérateur de tour modifié avec succès.');
            } else {
                $tourOperator = TourOperator::create($data);
                session()->flash('message', 'Opérateur de tour créé avec succès.');
            }

            // Sauvegarder les traductions
            foreach ($this->translations as $locale => $translation) {
                if (! empty($translation['name'])) {
                    $tourOperator->translations()->updateOrCreate(
                        ['locale' => $locale],
                        $translation
                    );
                }
            }

            // Sauvegarder les médias de la galerie
            if (! empty($this->selectedMedia)) {
                $mediaData = [];
                foreach ($this->selectedMedia as $index => $mediaId) {
                    $mediaData[$mediaId] = ['order' => $index + 1];
                }
                $tourOperator->media()->sync($mediaData);
            } else {
                $tourOperator->media()->detach();
            }

            // Rediriger selon le mode
            if ($this->isOperatorMode) {
                return redirect()->route('operator.tour-operator.show');
            } else {
                return redirect()->route('tour-operators.index');
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: '.$e->getMessage());
        }
    }

    public function changeLocale($locale)
    {
        $this->activeLocale = $locale;
    }

    #[On('media-selected')]
    public function onMediaSelected($mediaIds)
    {
        $this->logo_id = is_array($mediaIds) && count($mediaIds) > 0 ? $mediaIds[0] : $mediaIds;
    }

    #[On('gallery-media-selected')]
    public function onGalleryMediaSelected($mediaIds)
    {
        $this->selectedMedia = is_array($mediaIds) ? $mediaIds : [$mediaIds];
    }

    public function openMediaSelector()
    {
        $preselected = $this->logo_id ? [$this->logo_id] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    public function openGallerySelector()
    {
        $this->dispatch('open-gallery-media-selector', 'multiple', $this->selectedMedia);
    }

    private function generateSlug($name)
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (TourOperator::where('slug', $slug)->where('id', '!=', $this->tourOperatorId)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
