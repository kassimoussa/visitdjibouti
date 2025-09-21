<?php

namespace App\Livewire\Admin\Tour;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\Poi;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class TourForm extends Component
{
    public $tour = null;
    public $isEditing = false;

    // Form fields
    public $tour_operator_id = '';
    public $type = 'poi';
    public $target_id = '';
    public $target_type = '';
    public $start_date = '';
    public $end_date = '';
    public $duration_hours = '';
    public $max_participants = '';
    public $min_participants = 1;
    public $price = 0;
    public $currency = 'DJF';
    public $difficulty_level = 'easy';
    public $includes = [];
    public $requirements = [];
    public $meeting_point_latitude = '';
    public $meeting_point_longitude = '';
    public $meeting_point_address = '';
    public $status = 'active';
    public $is_featured = false;
    public $is_recurring = false;
    public $weather_dependent = false;
    public $age_restriction_min = '';
    public $age_restriction_max = '';
    public $cancellation_policy = '';
    public $featured_image_id = '';
    public $media_ids = [];

    // Translations
    public $translations = [
        'fr' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'itinerary' => '',
            'meeting_point_description' => '',
            'highlights' => '',
            'what_to_bring' => '',
            'cancellation_policy_text' => ''
        ],
        'en' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'itinerary' => '',
            'meeting_point_description' => '',
            'highlights' => '',
            'what_to_bring' => '',
            'cancellation_policy_text' => ''
        ],
        'ar' => [
            'title' => '',
            'description' => '',
            'short_description' => '',
            'itinerary' => '',
            'meeting_point_description' => '',
            'highlights' => '',
            'what_to_bring' => '',
            'cancellation_policy_text' => ''
        ]
    ];

    // Dynamic includes and requirements
    public $newInclude = '';
    public $newRequirement = '';

    // Gallery management
    public $showGallerySelector = false;
    public $mediaSelectorMode = 'multiple';

    protected $rules = [
        'tour_operator_id' => 'required|exists:tour_operators,id',
        'type' => 'required|in:poi,event,mixed,cultural,adventure,nature,gastronomic',
        'target_id' => 'required|integer',
        'start_date' => 'required|date|after_or_equal:today',
        'end_date' => 'nullable|date|after_or_equal:start_date',
        'duration_hours' => 'nullable|integer|min:1',
        'max_participants' => 'nullable|integer|min:1',
        'min_participants' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'currency' => 'required|string|size:3',
        'difficulty_level' => 'required|in:easy,moderate,difficult,expert',
        'meeting_point_latitude' => 'nullable|numeric|between:-90,90',
        'meeting_point_longitude' => 'nullable|numeric|between:-180,180',
        'meeting_point_address' => 'nullable|string|max:255',
        'status' => 'required|in:active,suspended,archived',
        'age_restriction_min' => 'nullable|integer|min:0',
        'age_restriction_max' => 'nullable|integer|min:0',
        'translations.fr.title' => 'required|string|max:255',
        'translations.fr.description' => 'required|string',
        'translations.en.title' => 'nullable|string|max:255',
        'translations.en.description' => 'nullable|string',
        'translations.ar.title' => 'nullable|string|max:255',
        'translations.ar.description' => 'nullable|string',
    ];

    public function mount($tour = null)
    {
        if ($tour) {
            $this->tour = $tour;
            $this->isEditing = true;
            $this->loadTourData($tour);
        }
    }

    public function updatedType()
    {
        $this->target_id = '';
        $this->target_type = $this->type === 'poi' ? Poi::class : ($this->type === 'event' ? Event::class : '');
    }

    public function addInclude()
    {
        if (!empty($this->newInclude)) {
            $this->includes[] = $this->newInclude;
            $this->newInclude = '';
        }
    }

    public function removeInclude($index)
    {
        unset($this->includes[$index]);
        $this->includes = array_values($this->includes);
    }

    public function addRequirement()
    {
        if (!empty($this->newRequirement)) {
            $this->requirements[] = $this->newRequirement;
            $this->newRequirement = '';
        }
    }

    public function removeRequirement($index)
    {
        unset($this->requirements[$index]);
        $this->requirements = array_values($this->requirements);
    }

    /**
     * Ouvrir le sélecteur de médias pour l'image principale
     */
    public function openFeaturedImageSelector()
    {
        $this->mediaSelectorMode = 'single';
        $preselected = $this->featured_image_id ? [$this->featured_image_id] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    /**
     * Ouvrir le sélecteur de médias pour la galerie
     */
    public function openGallerySelector()
    {
        $this->mediaSelectorMode = 'multiple';
        $this->dispatch('open-media-selector', 'multiple', $this->media_ids);
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    #[On('media-selected')]
    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->featured_image_id = !empty($selectedIds) ? $selectedIds[0] : null;
        } else {
            $this->media_ids = $selectedIds;
        }
    }

    public function selectFeaturedImage($mediaId)
    {
        $this->featured_image_id = $mediaId;
    }

    public function removeFromGallery($mediaId)
    {
        $this->media_ids = array_values(array_filter($this->media_ids, function($id) use ($mediaId) {
            return $id != $mediaId;
        }));

        // Si l'image supprimée était l'image principale, on la retire
        if ($this->featured_image_id == $mediaId) {
            $this->featured_image_id = '';
        }
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            // Set target_type based on type selection
            if ($this->type === 'poi') {
                $this->target_type = Poi::class;
            } elseif ($this->type === 'event') {
                $this->target_type = Event::class;
            }

            $data = [
                'tour_operator_id' => $this->tour_operator_id,
                'type' => $this->type,
                'target_id' => $this->target_id,
                'target_type' => $this->target_type,
                'start_date' => $this->start_date ?: null,
                'end_date' => $this->end_date ?: null,
                'duration_hours' => $this->duration_hours ?: null,
                'max_participants' => $this->max_participants ?: null,
                'min_participants' => $this->min_participants,
                'price' => $this->price,
                'currency' => $this->currency,
                'difficulty_level' => $this->difficulty_level,
                'includes' => $this->includes,
                'requirements' => $this->requirements,
                'meeting_point_latitude' => $this->meeting_point_latitude ?: null,
                'meeting_point_longitude' => $this->meeting_point_longitude ?: null,
                'meeting_point_address' => $this->meeting_point_address ?: null,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'is_recurring' => $this->is_recurring,
                'weather_dependent' => $this->weather_dependent,
                'age_restriction_min' => $this->age_restriction_min ?: null,
                'age_restriction_max' => $this->age_restriction_max ?: null,
                'cancellation_policy' => $this->cancellation_policy ?: null,
                'featured_image_id' => $this->featured_image_id ?: null,
            ];

            if ($this->isEditing) {
                $this->tour->update($data);
                $tour = $this->tour;
            } else {
                $tour = Tour::create($data);
            }

            // Create/Update translations
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['title'])) {
                    $translationData = [
                        'title' => $translation['title'],
                        'description' => $translation['description'] ?: '',
                        'short_description' => $translation['short_description'] ?: '',
                        'itinerary' => $translation['itinerary'] ?: '',
                        'meeting_point_description' => $translation['meeting_point_description'] ?: '',
                        'highlights' => !empty($translation['highlights']) ? explode("\n", $translation['highlights']) : null,
                        'what_to_bring' => !empty($translation['what_to_bring']) ? explode("\n", $translation['what_to_bring']) : null,
                        'cancellation_policy_text' => $translation['cancellation_policy_text'] ?: '',
                    ];

                    if ($this->isEditing) {
                        $tour->translations()->updateOrCreate(
                            ['locale' => $locale],
                            $translationData
                        );
                    } else {
                        $tour->translations()->create(array_merge(
                            ['locale' => $locale],
                            $translationData
                        ));
                    }
                }
            }

            // Sync media
            if (!empty($this->media_ids)) {
                $mediaData = [];
                foreach ($this->media_ids as $index => $mediaId) {
                    $mediaData[$mediaId] = ['order' => $index];
                }
                $tour->media()->sync($mediaData);
            } else {
                $tour->media()->detach();
            }

            DB::commit();

            $message = $this->isEditing ? 'Tour mis à jour avec succès' : 'Tour créé avec succès';
            session()->flash('success', $message);

            return redirect()->route('tours.index');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la sauvegarde: ' . $e->getMessage());
        }
    }

    private function loadTourData(Tour $tour)
    {
        $this->tour_operator_id = $tour->tour_operator_id;
        $this->type = $tour->type;
        $this->target_id = $tour->target_id;
        $this->target_type = $tour->target_type;
        $this->start_date = $tour->start_date?->format('Y-m-d');
        $this->end_date = $tour->end_date?->format('Y-m-d');
        $this->duration_hours = $tour->duration_hours;
        $this->max_participants = $tour->max_participants;
        $this->min_participants = $tour->min_participants;
        $this->price = $tour->price;
        $this->currency = $tour->currency;
        $this->difficulty_level = $tour->difficulty_level;
        $this->includes = $tour->includes ?? [];
        $this->requirements = $tour->requirements ?? [];
        $this->meeting_point_latitude = $tour->meeting_point_latitude;
        $this->meeting_point_longitude = $tour->meeting_point_longitude;
        $this->meeting_point_address = $tour->meeting_point_address;
        $this->status = $tour->status;
        $this->is_featured = $tour->is_featured;
        $this->is_recurring = $tour->is_recurring;
        $this->weather_dependent = $tour->weather_dependent;
        $this->age_restriction_min = $tour->age_restriction_min;
        $this->age_restriction_max = $tour->age_restriction_max;
        $this->cancellation_policy = $tour->cancellation_policy;
        $this->featured_image_id = $tour->featured_image_id;
        $this->media_ids = $tour->media->pluck('id')->toArray();

        // Load translations
        foreach ($tour->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'description' => $translation->description,
                'short_description' => $translation->short_description,
                'itinerary' => $translation->itinerary,
                'meeting_point_description' => $translation->meeting_point_description,
                'highlights' => is_array($translation->highlights) ? implode("\n", $translation->highlights) : '',
                'what_to_bring' => is_array($translation->what_to_bring) ? implode("\n", $translation->what_to_bring) : '',
                'cancellation_policy_text' => $translation->cancellation_policy_text,
            ];
        }
    }

    public function render()
    {
        $tourOperators = TourOperator::active()->with('translations')->get();
        $pois = Poi::where('status', 'published')->with('translations')->get();
        $events = Event::where('status', 'published')->with('translations')->get();

        return view('livewire.admin.tour.tour-form', [
            'tourOperators' => $tourOperators,
            'pois' => $pois,
            'events' => $events,
        ]);
    }
}