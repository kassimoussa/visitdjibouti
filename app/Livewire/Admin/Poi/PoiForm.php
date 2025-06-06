<?php

namespace App\Livewire\Admin\Poi;

use App\Models\Category;
use App\Models\Media;
use App\Models\Poi;
use App\Models\PoiTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class PoiForm extends Component
{
    // Propriétés générales du POI (non traduites)
    public $poiId;
    public $slug = '';
    public $latitude = null;
    public $longitude = null;
    public $region = '';
    public $contact = '';
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

    // Mode édition
    public $isEditMode = false;

    // Règles de validation
    protected function rules()
    {
        $rules = [
            'slug' => $this->isEditMode 
                ? 'nullable|string|max:255|unique:pois,slug,' . $this->poiId 
                : 'nullable|string|max:255|unique:pois,slug',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'region' => 'nullable|string|max:255',
            'contact' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'is_featured' => 'boolean',
            'allow_reservations' => 'boolean',
            'status' => 'required|in:draft,published,archived',
            'selectedCategories' => 'required|array|min:1',
            'featuredImageId' => 'nullable|exists:media,id',
        ];

        // Ajouter les règles pour chaque langue
        $requiredLocale = config('app.fallback_locale', 'fr'); // La langue par défaut est obligatoire
        
        foreach (['fr', 'en', 'ar'] as $locale) {
            $isRequired = ($locale === $requiredLocale) ? 'required' : 'nullable';
            
            $rules["translations.{$locale}.name"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = "{$isRequired}|string";
            $rules["translations.{$locale}.short_description"] = "nullable|string|max:500";
            $rules["translations.{$locale}.address"] = "nullable|string|max:255";
            $rules["translations.{$locale}.opening_hours"] = "nullable|string";
            $rules["translations.{$locale}.entry_fee"] = "nullable|string|max:255";
            $rules["translations.{$locale}.tips"] = "nullable|string";
        }
        
        return $rules;
    }

    /**
     * Changer la langue active
     */
    public function changeLocale($locale)
    {
        if (in_array($locale, ['fr', 'en', 'ar'])) {
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
        if (!in_array($mediaId, $this->selectedMedia)) {
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
            $this->contact = $poi->contact;
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
        }
    }

    /**
     * Mise à jour du slug basé sur le nom en français
     */
    public function updatedTranslations()
    {
        if (empty($this->slug) && !empty($this->translations['fr']['name'])) {
            $this->slug = Str::slug($this->translations['fr']['name']);
        }
    }

    /**
     * Enregistrer le POI avec ses traductions
     */
    public function save()
    {
        // Validation
        $this->validate();

        // Générer le slug s'il n'est pas fourni
        if (empty($this->slug) && !empty($this->translations['fr']['name'])) {
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
                'contact' => $this->contact,
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

            session()->flash('success', 'Point d\'intérêt mis à jour avec succès.');
        } else {
            $poi = Poi::create([
                'slug' => $this->slug,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'region' => $this->region,
                'contact' => $this->contact,
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

            session()->flash('success', 'Point d\'intérêt créé avec succès.');
        }

        // Rediriger vers la liste des POI
        return redirect()->route('pois.index');
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
        $categories = Category::where('is_active', true)
            ->with(['translations' => function($query) {
                $query->where('locale', $this->activeLocale)
                      ->orWhere('locale', config('app.fallback_locale', 'fr'));
            }])
            ->get()
            ->sortBy(function($category) {
                $translation = $category->translation($this->activeLocale);
                return $translation ? $translation->name : '';
            });
            
        $regions = $this->getRegionsList();

        // Récupérer les médias disponibles
        $media = Media::orderBy('created_at', 'desc')->get();

        return view('livewire.admin.poi.poi-form', [
            'categories' => $categories,
            'regions' => $regions,
            'media' => $media,
            'availableLocales' => ['fr', 'en', 'ar'],
        ]);
    }
}