<?php

namespace App\Livewire\Operator\Tour;

use App\Models\Event;
use App\Models\Media;
use App\Models\Poi;
use App\Models\Tour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class TourForm extends Component
{
    use WithFileUploads;

    // Properties for the Tour model
    public $tourId;

    public $tour_operator_id;

    public $target_id;

    public $target_type;

    public $start_date;

    public $end_date;

    public $price = 0;

    public $currency = 'DJF';

    public $max_participants;

    public $difficulty_level = 'easy';

    // Status is not editable by operator - always created as 'draft'

    public $is_featured = false;

    public $weather_dependent = false;

    public $meeting_point_address = '';

    public $cancellation_policy = '';

    public $slug;

    // View-related properties
    public $isEditMode = false;

    public $pois = [];

    public $events = [];

    public $translations = [];

    public $allMedia = [];

    public $selectedMedia = [];

    public $featuredImageId;

    public $mediaSelectorMode = 'single';

    protected function rules()
    {
        return [
            'target_id' => 'nullable|integer',
            'target_type' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'difficulty_level' => 'required|in:easy,moderate,difficult,expert',
            'is_featured' => 'boolean',
            'weather_dependent' => 'boolean',
            'meeting_point_address' => 'nullable|string|max:255',
            'featuredImageId' => 'nullable|exists:media,id',
            'translations.fr.title' => 'required|string|max:255',
            'translations.fr.description' => 'required|string',
            'translations.en.title' => 'nullable|string|max:255',
            'translations.en.description' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'price.required' => 'Le prix est requis.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être au minimum 0.',
            'difficulty_level.required' => 'Le niveau de difficulté est requis.',
            'translations.fr.title.required' => 'Le titre en français est requis.',
            'translations.fr.description.required' => 'La description en français est requise.',
        ];
    }

    public function mount(?Tour $tour = null)
    {
        // Get the authenticated operator's tour_operator_id
        $user = Auth::guard('operator')->user();
        $this->tour_operator_id = $user->tour_operator_id;

        $this->pois = Poi::all();
        $this->events = Event::all();
        $this->allMedia = Media::all();

        // Initialiser les traductions pour FR et EN
        $locales = ['fr', 'en'];
        foreach ($locales as $locale) {
            $this->translations[$locale] = [
                'title' => '',
                'description' => '',
            ];
        }

        if ($tour && $tour->exists) {
            // Verify the tour belongs to this operator
            if ($tour->tour_operator_id !== $this->tour_operator_id) {
                abort(403, 'Vous n\'avez pas accès à ce tour.');
            }

            $this->isEditMode = true;
            $this->tourId = $tour->id;

            // Fill properties from the model (excluding status - managed by approval workflow)
            $this->fill($tour->only([
                'tour_operator_id', 'target_id', 'target_type', 'start_date', 'end_date',
                'price', 'currency', 'max_participants',
                'difficulty_level', 'is_featured', 'weather_dependent',
                'meeting_point_address', 'cancellation_policy', 'slug',
            ]));

            // Cast boolean values
            $this->is_featured = (bool) $tour->is_featured;
            $this->weather_dependent = (bool) $tour->weather_dependent;

            // Format dates
            $this->start_date = $tour->start_date ? $tour->start_date->format('Y-m-d') : null;
            $this->end_date = $tour->end_date ? $tour->end_date->format('Y-m-d') : null;

            foreach ($tour->translations as $translation) {
                if (isset($this->translations[$translation->locale])) {
                    $this->translations[$translation->locale] = [
                        'title' => $translation->title ?? '',
                        'description' => $translation->description ?? '',
                    ];
                }
            }
            $this->selectedMedia = $tour->media->pluck('id')->toArray();
            $this->featuredImageId = $tour->featured_image_id;
        }
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    protected $listeners = ['media-selected' => 'handleMediaSelection'];

    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            // Mode image principale : prendre seulement le premier ID
            $this->featuredImageId = ! empty($selectedIds) ? $selectedIds[0] : null;
        } else {
            // Mode galerie : remplacer tous les médias sélectionnés
            $this->selectedMedia = $selectedIds;
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
     * Supprimer un média de la galerie
     */
    public function removeMediaFromGallery($index)
    {
        if (isset($this->selectedMedia[$index])) {
            array_splice($this->selectedMedia, $index, 1);
            $this->selectedMedia = array_values($this->selectedMedia); // Réindexer
        }
    }

    public function save()
    {
        $validatedData = $this->validate();

        $user = Auth::guard('operator')->user();

        $tourData = [
            'tour_operator_id' => $this->tour_operator_id,
            'target_id' => $this->target_id,
            'target_type' => $this->target_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'price' => $this->price,
            'currency' => $this->currency,
            'max_participants' => $this->max_participants,
            'difficulty_level' => $this->difficulty_level,
            // Status is handled by approval workflow:
            // - New tours: created as 'draft' with created_by_operator_user_id
            // - Updates: status changes handled in Tour model boot() method
            'is_featured' => $this->is_featured,
            'weather_dependent' => $this->weather_dependent,
            'meeting_point_address' => $this->meeting_point_address,
            'cancellation_policy' => $this->cancellation_policy,
            'featured_image_id' => $this->featuredImageId,
            'slug' => $this->slug,
        ];

        // For new tours, set initial status and creator
        if (!$this->isEditMode) {
            $tourData['status'] = 'draft';
            $tourData['created_by_operator_user_id'] = $user->id;
        }

        if (empty($tourData['slug'])) {
            $tourData['slug'] = Str::slug($this->translations['fr']['title'] ?? 'tour-'.uniqid());
        }

        $tour = Tour::updateOrCreate(['id' => $this->tourId], $tourData);

        // Sauvegarder les traductions
        foreach ($this->translations as $locale => $data) {
            if (! empty($data['title'])) {
                $tour->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $data['title'] ?? '',
                        'description' => $data['description'] ?? '',
                    ]
                );
            }
        }

        // Synchroniser les médias de la galerie
        $tour->media()->sync($this->selectedMedia);

        session()->flash('success', 'Tour sauvegardé avec succès.');

        return redirect()->route('operator.tours.index');
    }

    public function render()
    {
        return view('livewire.operator.tour.tour-form');
    }
}
