<?php

namespace App\Livewire\Admin\Tour;

use App\Models\Event;
use App\Models\Poi;
use App\Models\Tour;
use App\Models\TourOperator;
use App\Models\TourSchedule;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class TourManager extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $operatorFilter = '';

    public $typeFilter = '';

    public $difficultyFilter = '';

    public $showCreateModal = false;

    public $showEditModal = false;

    public $showScheduleModal = false;

    public $editingTour = null;

    public $selectedTour = null;

    // Form fields for tour creation/editing
    public $tour_operator_id = '';

    public $type = 'poi';

    public $target_id = '';

    public $target_type = '';

    public $duration_hours = '';

    public $duration_days = '';

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

    // Translations
    public $translations = [
        'fr' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
        'en' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
        'ar' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
    ];

    // Schedule form fields
    public $schedule_start_date = '';

    public $schedule_end_date = '';

    public $schedule_start_time = '';

    public $schedule_end_time = '';

    public $schedule_available_spots = '';

    public $schedule_guide_name = '';

    public $schedule_guide_contact = '';

    public $schedule_guide_languages = [];

    public $schedule_special_notes = '';

    protected $listeners = ['tourDeleted' => '$refresh'];

    protected $rules = [
        'tour_operator_id' => 'required|exists:tour_operators,id',
        'type' => 'required|in:poi,event,mixed,cultural,adventure,nature,gastronomic',
        'target_id' => 'required|integer',
        'duration_hours' => 'nullable|integer|min:1',
        'duration_days' => 'nullable|integer|min:1',
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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedOperatorFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedDifficultyFilter()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->target_id = '';
        $this->target_type = $this->type === 'poi' ? Poi::class : ($this->type === 'event' ? Event::class : '');
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal(Tour $tour)
    {
        $this->editingTour = $tour;
        $this->loadTourData($tour);
        $this->showEditModal = true;
    }

    public function openScheduleModal(Tour $tour)
    {
        $this->selectedTour = $tour;
        $this->resetScheduleForm();
        $this->showScheduleModal = true;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showScheduleModal = false;
        $this->editingTour = null;
        $this->selectedTour = null;
        $this->resetForm();
        $this->resetScheduleForm();
    }

    public function createTour()
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

            $tour = Tour::create([
                'tour_operator_id' => $this->tour_operator_id,
                'type' => $this->type,
                'target_id' => $this->target_id,
                'target_type' => $this->target_type,
                'duration_hours' => $this->duration_hours ?: null,
                'duration_days' => $this->duration_days ?: null,
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
            ]);

            // Create translations
            foreach ($this->translations as $locale => $translation) {
                if (! empty($translation['title'])) {
                    $tour->translations()->create([
                        'locale' => $locale,
                        'title' => $translation['title'],
                        'description' => $translation['description'] ?: '',
                        'short_description' => $translation['short_description'] ?: '',
                        'itinerary' => $translation['itinerary'] ?: '',
                        'meeting_point_description' => $translation['meeting_point_description'] ?: '',
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Tour créé avec succès');
            $this->closeModals();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la création du tour: '.$e->getMessage());
        }
    }

    public function updateTour()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            if ($this->type === 'poi') {
                $this->target_type = Poi::class;
            } elseif ($this->type === 'event') {
                $this->target_type = Event::class;
            }

            $this->editingTour->update([
                'tour_operator_id' => $this->tour_operator_id,
                'type' => $this->type,
                'target_id' => $this->target_id,
                'target_type' => $this->target_type,
                'duration_hours' => $this->duration_hours ?: null,
                'duration_days' => $this->duration_days ?: null,
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
            ]);

            // Update translations
            foreach ($this->translations as $locale => $translation) {
                if (! empty($translation['title'])) {
                    $this->editingTour->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'title' => $translation['title'],
                            'description' => $translation['description'] ?: '',
                            'short_description' => $translation['short_description'] ?: '',
                            'itinerary' => $translation['itinerary'] ?: '',
                            'meeting_point_description' => $translation['meeting_point_description'] ?: '',
                        ]
                    );
                }
            }

            DB::commit();

            session()->flash('success', 'Tour mis à jour avec succès');
            $this->closeModals();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de la mise à jour du tour: '.$e->getMessage());
        }
    }

    public function createSchedule()
    {
        $this->validate([
            'schedule_start_date' => 'required|date|after_or_equal:today',
            'schedule_end_date' => 'nullable|date|after_or_equal:schedule_start_date',
            'schedule_start_time' => 'nullable|date_format:H:i',
            'schedule_end_time' => 'nullable|date_format:H:i|after:schedule_start_time',
            'schedule_available_spots' => 'required|integer|min:1',
            'schedule_guide_name' => 'nullable|string|max:255',
            'schedule_guide_contact' => 'nullable|string|max:255',
        ]);

        try {
            TourSchedule::create([
                'tour_id' => $this->selectedTour->id,
                'start_date' => $this->schedule_start_date,
                'end_date' => $this->schedule_end_date ?: $this->schedule_start_date,
                'start_time' => $this->schedule_start_time ?: null,
                'end_time' => $this->schedule_end_time ?: null,
                'available_spots' => $this->schedule_available_spots,
                'guide_name' => $this->schedule_guide_name ?: null,
                'guide_contact' => $this->schedule_guide_contact ?: null,
                'guide_languages' => $this->schedule_guide_languages,
                'special_notes' => $this->schedule_special_notes ?: null,
                'created_by_admin_id' => auth('admin')->id(),
            ]);

            session()->flash('success', 'Créneau créé avec succès');
            $this->closeModals();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du créneau: '.$e->getMessage());
        }
    }

    public function deleteTour(Tour $tour)
    {
        try {
            $tour->delete();
            session()->flash('success', 'Tour supprimé avec succès');
            $this->dispatch('tourDeleted');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression du tour: '.$e->getMessage());
        }
    }

    private function resetForm()
    {
        $this->tour_operator_id = '';
        $this->type = 'poi';
        $this->target_id = '';
        $this->target_type = '';
        $this->duration_hours = '';
        $this->duration_days = '';
        $this->max_participants = '';
        $this->min_participants = 1;
        $this->price = 0;
        $this->currency = 'DJF';
        $this->difficulty_level = 'easy';
        $this->includes = [];
        $this->requirements = [];
        $this->meeting_point_latitude = '';
        $this->meeting_point_longitude = '';
        $this->meeting_point_address = '';
        $this->status = 'active';
        $this->is_featured = false;
        $this->is_recurring = false;
        $this->weather_dependent = false;
        $this->age_restriction_min = '';
        $this->age_restriction_max = '';
        $this->cancellation_policy = '';

        $this->translations = [
            'fr' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
            'en' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
            'ar' => ['title' => '', 'description' => '', 'short_description' => '', 'itinerary' => '', 'meeting_point_description' => ''],
        ];
    }

    private function resetScheduleForm()
    {
        $this->schedule_start_date = '';
        $this->schedule_end_date = '';
        $this->schedule_start_time = '';
        $this->schedule_end_time = '';
        $this->schedule_available_spots = '';
        $this->schedule_guide_name = '';
        $this->schedule_guide_contact = '';
        $this->schedule_guide_languages = [];
        $this->schedule_special_notes = '';
    }

    private function loadTourData(Tour $tour)
    {
        $this->tour_operator_id = $tour->tour_operator_id;
        $this->type = $tour->type;
        $this->target_id = $tour->target_id;
        $this->target_type = $tour->target_type;
        $this->duration_hours = $tour->duration_hours;
        $this->duration_days = $tour->duration_days;
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

        // Load translations
        foreach ($tour->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'description' => $translation->description,
                'short_description' => $translation->short_description,
                'itinerary' => $translation->itinerary,
                'meeting_point_description' => $translation->meeting_point_description,
            ];
        }
    }

    public function render()
    {
        $query = Tour::with(['tourOperator.translations', 'target', 'translations', 'schedules']);

        // Apply filters
        if (! empty($this->search)) {
            $query->whereHas('translations', function ($q) {
                $q->where('title', 'LIKE', '%'.$this->search.'%');
            });
        }

        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (! empty($this->operatorFilter)) {
            $query->where('tour_operator_id', $this->operatorFilter);
        }

        if (! empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        if (! empty($this->difficultyFilter)) {
            $query->where('difficulty_level', $this->difficultyFilter);
        }

        $tours = $query->orderBy('created_at', 'desc')->paginate(20);

        $tourOperators = TourOperator::active()->with('translations')->get();
        $pois = Poi::where('status', 'published')->with('translations')->get();
        $events = Event::where('status', 'published')->with('translations')->get();

        return view('livewire.admin.tour.tour-manager', [
            'tours' => $tours,
            'tourOperators' => $tourOperators,
            'pois' => $pois,
            'events' => $events,
        ]);
    }
}
