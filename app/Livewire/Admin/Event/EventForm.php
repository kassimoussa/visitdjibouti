<?php

namespace App\Livewire\Admin\Event;

use App\Models\Category;
use App\Models\Media;
use App\Models\Event;
use App\Models\EventTranslation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class EventForm extends Component
{
    // Propriétés générales de l'événement (non traduites)
    public $eventId;
    public $slug = '';
    public $start_date = null;
    public $end_date = null;
    public $start_time = null;
    public $end_time = null;
    public $location = '';
    public $latitude = null;
    public $longitude = null;
    public $contact_email = '';
    public $contact_phone = '';
    public $website_url = '';
    public $ticket_url = '';
    public $price = null;
    public $max_participants = null;
    public $current_participants = 0;
    public $organizer = '';
    public $is_featured = false;
    public $status = 'draft';
    public $featuredImageId = null;

    // Traductions
    public $translations = [
        'fr' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'location_details' => '',
            'requirements' => '',
            'program' => '',
            'additional_info' => '',
        ],
        'en' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'location_details' => '',
            'requirements' => '',
            'program' => '',
            'additional_info' => '',
        ],
        'ar' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'location_details' => '',
            'requirements' => '',
            'program' => '',
            'additional_info' => '',
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
                ? 'nullable|string|max:255|unique:events,slug,' . $this->eventId 
                : 'nullable|string|max:255|unique:events,slug',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'ticket_url' => 'nullable|url|max:255',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'current_participants' => 'nullable|integer|min:0',
            'organizer' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'status' => 'required|in:draft,published,archived',
            'selectedCategories' => 'required|array|min:1',
            'featuredImageId' => 'nullable|exists:media,id',
        ];

        // Ajouter les règles pour chaque langue
        $requiredLocale = config('app.fallback_locale', 'fr'); // La langue par défaut est obligatoire
        
        foreach (['fr', 'en', 'ar'] as $locale) {
            $isRequired = ($locale === $requiredLocale) ? 'required' : 'nullable';
            
            $rules["translations.{$locale}.title"] = "{$isRequired}|string|max:255";
            $rules["translations.{$locale}.description"] = "{$isRequired}|string";
            $rules["translations.{$locale}.short_description"] = "nullable|string|max:500";
            $rules["translations.{$locale}.location_details"] = "nullable|string|max:255";
            $rules["translations.{$locale}.requirements"] = "nullable|string";
            $rules["translations.{$locale}.program"] = "nullable|string";
            $rules["translations.{$locale}.additional_info"] = "nullable|string";
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
    public function mount($eventId = null)
    {
        if ($eventId) {
            $this->eventId = $eventId;
            $this->isEditMode = true;
            
            // Récupérer l'événement depuis la base de données
            $event = Event::findOrFail($eventId);

            // Remplir les propriétés non traduites
            $this->slug = $event->slug;
            $this->start_date = $event->start_date?->format('Y-m-d');
            $this->end_date = $event->end_date?->format('Y-m-d');
            $this->start_time = $event->start_time?->format('H:i');
            $this->end_time = $event->end_time?->format('H:i');
            $this->location = $event->location;
            $this->latitude = $event->latitude;
            $this->longitude = $event->longitude;
            $this->contact_email = $event->contact_email;
            $this->contact_phone = $event->contact_phone;
            $this->website_url = $event->website_url;
            $this->ticket_url = $event->ticket_url;
            $this->price = $event->price;
            $this->max_participants = $event->max_participants;
            $this->current_participants = $event->current_participants;
            $this->organizer = $event->organizer;
            $this->is_featured = (bool) $event->is_featured;
            $this->status = $event->status;
            $this->featuredImageId = $event->featured_image_id;

            // Remplir les traductions
            foreach ($event->translations as $translation) {
                $locale = $translation->locale;
                
                if (isset($this->translations[$locale])) {
                    $this->translations[$locale]['title'] = $translation->title;
                    $this->translations[$locale]['description'] = $translation->description;
                    $this->translations[$locale]['short_description'] = $translation->short_description;
                    $this->translations[$locale]['location_details'] = $translation->location_details;
                    $this->translations[$locale]['requirements'] = $translation->requirements;
                    $this->translations[$locale]['program'] = $translation->program;
                    $this->translations[$locale]['additional_info'] = $translation->additional_info;
                }
            }

            // Remplir les catégories sélectionnées
            $this->selectedCategories = $event->categories->pluck('id')->toArray();

            // Remplir les médias sélectionnés
            $this->selectedMedia = $event->media->pluck('id')->toArray();
        }
    }

    /**
     * Mise à jour du slug basé sur le titre en français
     */
    public function updatedTranslations()
    {
        if (empty($this->slug) && !empty($this->translations['fr']['title'])) {
            $this->slug = Str::slug($this->translations['fr']['title']);
        }
    }

    /**
     * Enregistrer l'événement avec ses traductions
     */
    public function save()
    {
        // Validation
        $this->validate();

        // Générer le slug s'il n'est pas fourni
        if (empty($this->slug) && !empty($this->translations['fr']['title'])) {
            $this->slug = Str::slug($this->translations['fr']['title']);
        }

        // Créer ou mettre à jour l'événement
        if ($this->isEditMode) {
            $event = Event::findOrFail($this->eventId);

            $event->update([
                'slug' => $this->slug,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'website_url' => $this->website_url,
                'ticket_url' => $this->ticket_url,
                'price' => $this->price,
                'max_participants' => $this->max_participants,
                'current_participants' => $this->current_participants,
                'organizer' => $this->organizer,
                'is_featured' => $this->is_featured,
                'status' => $this->status,
                'featured_image_id' => $this->featuredImageId,
            ]);

            // Mettre à jour les traductions
            foreach ($this->translations as $locale => $translation) {
                $event->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $translation['title'],
                        'description' => $translation['description'],
                        'short_description' => $translation['short_description'],
                        'location_details' => $translation['location_details'],
                        'requirements' => $translation['requirements'],
                        'program' => $translation['program'],
                        'additional_info' => $translation['additional_info'],
                    ]
                );
            }

            // Mettre à jour les catégories
            $event->categories()->sync($this->selectedCategories);

            // Mettre à jour les médias
            $mediaData = [];
            foreach ($this->selectedMedia as $index => $mediaId) {
                $mediaData[$mediaId] = ['order' => $index];
            }
            $event->media()->sync($mediaData);

            session()->flash('success', 'Événement mis à jour avec succès.');
        } else {
            $event = Event::create([
                'slug' => $this->slug,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'location' => $this->location,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'website_url' => $this->website_url,
                'ticket_url' => $this->ticket_url,
                'price' => $this->price,
                'max_participants' => $this->max_participants,
                'current_participants' => $this->current_participants,
                'organizer' => $this->organizer,
                'is_featured' => $this->is_featured,
                'status' => $this->status,
                'featured_image_id' => $this->featuredImageId,
                'creator_id' => Auth::guard('admin')->id(),
            ]);

            // Créer les traductions
            foreach ($this->translations as $locale => $translation) {
                $event->translations()->create([
                    'locale' => $locale,
                    'title' => $translation['title'],
                    'description' => $translation['description'],
                    'short_description' => $translation['short_description'],
                    'location_details' => $translation['location_details'],
                    'requirements' => $translation['requirements'],
                    'program' => $translation['program'],
                    'additional_info' => $translation['additional_info'],
                ]);
            }

            // Attacher les catégories
            $event->categories()->attach($this->selectedCategories);

            // Attacher les médias
            $mediaData = [];
            foreach ($this->selectedMedia as $index => $mediaId) {
                $mediaData[$mediaId] = ['order' => $index];
            }
            $event->media()->attach($mediaData);

            session()->flash('success', 'Événement créé avec succès.');
        }

        // Rediriger vers la liste des événements
        return redirect()->route('events.index');
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

        // Récupérer les médias disponibles
        $media = Media::orderBy('created_at', 'desc')->get();

        return view('livewire.admin.event.event-form', [
            'categories' => $categories,
            'media' => $media,
            'availableLocales' => ['fr', 'en', 'ar'],
        ]);
    }
}