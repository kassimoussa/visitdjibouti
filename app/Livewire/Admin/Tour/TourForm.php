<?php

namespace App\Livewire\Admin\Tour;

use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\Poi;
use App\Models\Event;
use App\Models\Media;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;

class TourForm extends Component
{
    use WithFileUploads;

    public Tour $tour;
    public $tourOperators = [];
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
            'tour.tour_operator_id' => 'required|exists:tour_operators,id',
            'tour.target_id' => 'nullable|integer',
            'tour.target_type' => 'nullable|string',
            'tour.start_date' => 'nullable|date',
            'tour.end_date' => 'nullable|date|after_or_equal:tour.start_date',
            'tour.start_time' => 'nullable',
            'tour.end_time' => 'nullable',
            'tour.price' => 'required|numeric|min:0',
            'tour.max_participants' => 'nullable|integer|min:1',
            'tour.difficulty_level' => 'required|in:easy,moderate,difficult,expert',
            'tour.status' => 'required|in:active,suspended,archived',
            'tour.is_featured' => 'boolean',
            'tour.weather_dependent' => 'boolean',
            'tour.meeting_point_address' => 'nullable|string|max:255',
            'tour.cancellation_policy' => 'nullable|string',
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
            'tour.tour_operator_id.required' => 'Le tour opérateur est requis.',
            'tour.tour_operator_id.exists' => 'Le tour opérateur sélectionné n\'existe pas.',
            'tour.price.required' => 'Le prix est requis.',
            'tour.price.numeric' => 'Le prix doit être un nombre.',
            'tour.price.min' => 'Le prix doit être au minimum 0.',
            'tour.difficulty_level.required' => 'Le niveau de difficulté est requis.',
            'tour.status.required' => 'Le statut est requis.',
            'translations.fr.title.required' => 'Le titre en français est requis.',
            'translations.fr.description.required' => 'La description en français est requise.',
        ];
    }

    public function mount(Tour $tour = null)
    {
        $this->tour = $tour ?? new Tour();

        // Initialiser les valeurs par défaut pour un nouveau tour
        if (!$this->tour->exists) {
            $this->tour->status = 'active';
            $this->tour->difficulty_level = 'easy';
            $this->tour->is_featured = false;
            $this->tour->weather_dependent = false;
            $this->tour->currency = 'DJF';
            $this->tour->price = 0;
            $this->tour->type = 'cultural'; // Type par défaut
        }

        $this->tourOperators = TourOperator::all();
        $this->pois = Poi::all();
        $this->events = Event::all();
        $this->allMedia = Media::all();

        // Initialiser les traductions pour FR et EN
        $locales = ['fr', 'en'];
        foreach ($locales as $locale) {
            $this->translations[$locale] = [
                'title' => '',
                'description' => ''
            ];
        }

        if ($this->tour->exists) {
            foreach ($this->tour->translations as $translation) {
                $this->translations[$translation->locale] = [
                    'title' => $translation->title ?? '',
                    'description' => $translation->description ?? ''
                ];
            }
            $this->selectedMedia = $this->tour->media->pluck('id')->toArray();
            $this->featuredImageId = $this->tour->featured_image_id;
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
            $this->featuredImageId = !empty($selectedIds) ? $selectedIds[0] : null;
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
        $this->validate();

        // Générer le slug s'il est vide
        if (empty($this->tour->slug)) {
            $this->tour->slug = Str::slug($this->translations['fr']['title'] ?? 'tour-' . uniqid());
        }

        // S'assurer que currency est défini
        if (empty($this->tour->currency)) {
            $this->tour->currency = 'DJF';
        }

        // Définir l'image principale
        $this->tour->featured_image_id = $this->featuredImageId;

        // Sauvegarder le tour
        $this->tour->save();

        // Sauvegarder les traductions
        foreach ($this->translations as $locale => $data) {
            // Ne sauvegarder que si au moins le titre est rempli
            if (!empty($data['title'])) {
                $this->tour->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $data['title'] ?? '',
                        'description' => $data['description'] ?? ''
                    ]
                );
            }
        }

        // Synchroniser les médias de la galerie
        $this->tour->media()->sync($this->selectedMedia);

        session()->flash('success', 'Tour sauvegardé avec succès.');
        return redirect()->route('tours.index');
    }

    public function render()
    {
        return view('livewire.admin.tour.tour-form');
    }
}
