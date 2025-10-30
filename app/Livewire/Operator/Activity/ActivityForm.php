<?php

namespace App\Livewire\Operator\Activity;

use App\Models\Activity;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ActivityForm extends Component
{
    // Properties for the Activity model
    public $activityId;

    public $tour_operator_id;

    public $start_date;

    public $end_date;

    public $price = 0;

    public $currency = 'DJF';

    public $duration_hours;

    public $duration_minutes;

    public $difficulty_level = 'easy';

    public $min_participants = 1;

    public $max_participants;

    public $location_address = '';

    public $latitude;

    public $longitude;

    public $region;

    public $has_age_restrictions = false;

    public $min_age;

    public $max_age;

    public $physical_requirements = [];

    public $certifications_required = [];

    public $equipment_provided = [];

    public $equipment_required = [];

    public $includes = [];

    public $weather_dependent = false;

    public $cancellation_policy = '';

    public $is_featured = false;

    public $slug;

    // View-related properties
    public $isEditMode = false;

    public $translations = [];

    public $allMedia = [];

    public $selectedMedia = [];

    public $featuredImageId;

    public $mediaSelectorMode = 'single';

    // Temporary inputs for arrays
    public $newPhysicalRequirement = '';

    public $newCertification = '';

    public $newEquipmentProvided = '';

    public $newEquipmentRequired = '';

    public $newInclude = '';

    protected function rules()
    {
        return [
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'nullable|integer|min:0',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'difficulty_level' => 'required|in:easy,moderate,difficult,expert',
            'min_participants' => 'required|integer|min:1',
            'max_participants' => 'nullable|integer|min:1',
            'location_address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'region' => 'nullable|string|max:100',
            'has_age_restrictions' => 'boolean',
            'min_age' => 'nullable|required_if:has_age_restrictions,true|integer|min:1|max:100',
            'max_age' => 'nullable|required_if:has_age_restrictions,true|integer|min:1|max:120|gte:min_age',
            'weather_dependent' => 'boolean',
            'is_featured' => 'boolean',
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
            'min_participants.required' => 'Le nombre minimum de participants est requis.',
            'max_age.gte' => 'L\'âge maximum doit être supérieur ou égal à l\'âge minimum.',
            'translations.fr.title.required' => 'Le titre en français est requis.',
            'translations.fr.description.required' => 'La description en français est requise.',
        ];
    }

    public function mount(?Activity $activity = null)
    {
        // Get the authenticated operator's tour_operator_id
        $user = Auth::guard('operator')->user();
        $this->tour_operator_id = $user->tour_operator_id;

        $this->allMedia = Media::all();

        // Initialiser les traductions pour FR et EN
        $locales = ['fr', 'en'];
        foreach ($locales as $locale) {
            $this->translations[$locale] = [
                'title' => '',
                'description' => '',
                'short_description' => '',
                'what_to_bring' => '',
                'meeting_point_description' => '',
                'additional_info' => '',
            ];
        }

        if ($activity && $activity->exists) {
            // Verify the activity belongs to this operator
            if ($activity->tour_operator_id !== $this->tour_operator_id) {
                abort(403, 'Vous n\'avez pas accès à cette activité.');
            }

            $this->isEditMode = true;
            $this->activityId = $activity->id;

            // Fill properties from the model
            $this->fill($activity->only([
                'tour_operator_id', 'price', 'currency', 'duration_hours', 'duration_minutes',
                'difficulty_level', 'min_participants', 'max_participants',
                'location_address', 'latitude', 'longitude', 'region',
                'has_age_restrictions', 'min_age', 'max_age',
                'weather_dependent', 'cancellation_policy', 'is_featured', 'slug',
            ]));

            // Cast boolean values
            $this->is_featured = (bool) $activity->is_featured;
            $this->weather_dependent = (bool) $activity->weather_dependent;
            $this->has_age_restrictions = (bool) $activity->has_age_restrictions;

            // Load JSON arrays
            $this->physical_requirements = $activity->physical_requirements ?? [];
            $this->certifications_required = $activity->certifications_required ?? [];
            $this->equipment_provided = $activity->equipment_provided ?? [];
            $this->equipment_required = $activity->equipment_required ?? [];
            $this->includes = $activity->includes ?? [];

            // Load translations
            foreach ($activity->translations as $translation) {
                if (isset($this->translations[$translation->locale])) {
                    $this->translations[$translation->locale] = [
                        'title' => $translation->title ?? '',
                        'description' => $translation->description ?? '',
                        'short_description' => $translation->short_description ?? '',
                        'what_to_bring' => $translation->what_to_bring ?? '',
                        'meeting_point_description' => $translation->meeting_point_description ?? '',
                        'additional_info' => $translation->additional_info ?? '',
                    ];
                }
            }

            $this->selectedMedia = $activity->media->pluck('id')->toArray();
            $this->featuredImageId = $activity->featured_image_id;
        }
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    protected $listeners = ['media-selected' => 'handleMediaSelection'];

    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->featuredImageId = ! empty($selectedIds) ? $selectedIds[0] : null;
        } else {
            $this->selectedMedia = $selectedIds;
        }
    }

    public function openFeaturedImageSelector()
    {
        $this->mediaSelectorMode = 'single';
        $preselected = $this->featuredImageId ? [$this->featuredImageId] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    public function openGallerySelector()
    {
        $this->mediaSelectorMode = 'multiple';
        $this->dispatch('open-media-selector', 'multiple', $this->selectedMedia);
    }

    public function removeMediaFromGallery($index)
    {
        if (isset($this->selectedMedia[$index])) {
            array_splice($this->selectedMedia, $index, 1);
            $this->selectedMedia = array_values($this->selectedMedia);
        }
    }

    // Array management methods
    public function addPhysicalRequirement()
    {
        if (trim($this->newPhysicalRequirement)) {
            $this->physical_requirements[] = trim($this->newPhysicalRequirement);
            $this->newPhysicalRequirement = '';
        }
    }

    public function removePhysicalRequirement($index)
    {
        unset($this->physical_requirements[$index]);
        $this->physical_requirements = array_values($this->physical_requirements);
    }

    public function addCertification()
    {
        if (trim($this->newCertification)) {
            $this->certifications_required[] = trim($this->newCertification);
            $this->newCertification = '';
        }
    }

    public function removeCertification($index)
    {
        unset($this->certifications_required[$index]);
        $this->certifications_required = array_values($this->certifications_required);
    }

    public function addEquipmentProvided()
    {
        if (trim($this->newEquipmentProvided)) {
            $this->equipment_provided[] = trim($this->newEquipmentProvided);
            $this->newEquipmentProvided = '';
        }
    }

    public function removeEquipmentProvided($index)
    {
        unset($this->equipment_provided[$index]);
        $this->equipment_provided = array_values($this->equipment_provided);
    }

    public function addEquipmentRequired()
    {
        if (trim($this->newEquipmentRequired)) {
            $this->equipment_required[] = trim($this->newEquipmentRequired);
            $this->newEquipmentRequired = '';
        }
    }

    public function removeEquipmentRequired($index)
    {
        unset($this->equipment_required[$index]);
        $this->equipment_required = array_values($this->equipment_required);
    }

    public function addInclude()
    {
        if (trim($this->newInclude)) {
            $this->includes[] = trim($this->newInclude);
            $this->newInclude = '';
        }
    }

    public function removeInclude($index)
    {
        unset($this->includes[$index]);
        $this->includes = array_values($this->includes);
    }

    public function save()
    {
        $validatedData = $this->validate();

        $user = Auth::guard('operator')->user();

        $activityData = [
            'tour_operator_id' => $this->tour_operator_id,
            'price' => $this->price,
            'currency' => $this->currency,
            'duration_hours' => $this->duration_hours,
            'duration_minutes' => $this->duration_minutes,
            'difficulty_level' => $this->difficulty_level,
            'min_participants' => $this->min_participants,
            'max_participants' => $this->max_participants,
            'location_address' => $this->location_address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'region' => $this->region,
            'has_age_restrictions' => $this->has_age_restrictions,
            'min_age' => $this->has_age_restrictions ? $this->min_age : null,
            'max_age' => $this->has_age_restrictions ? $this->max_age : null,
            'physical_requirements' => $this->physical_requirements,
            'certifications_required' => $this->certifications_required,
            'equipment_provided' => $this->equipment_provided,
            'equipment_required' => $this->equipment_required,
            'includes' => $this->includes,
            'weather_dependent' => $this->weather_dependent,
            'cancellation_policy' => $this->cancellation_policy,
            'is_featured' => $this->is_featured,
            'featured_image_id' => $this->featuredImageId,
            'slug' => $this->slug,
        ];

        // For new activities, set creator and initial status
        if (! $this->isEditMode) {
            $activityData['status'] = 'active';
            $activityData['created_by_operator_user_id'] = $user->id;
        }

        if (empty($activityData['slug'])) {
            $activityData['slug'] = Str::slug($this->translations['fr']['title'] ?? 'activity-'.uniqid());
        }

        $activity = Activity::updateOrCreate(['id' => $this->activityId], $activityData);

        // Sauvegarder les traductions
        foreach ($this->translations as $locale => $data) {
            if (! empty($data['title'])) {
                $activity->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $data['title'] ?? '',
                        'description' => $data['description'] ?? '',
                        'short_description' => $data['short_description'] ?? '',
                        'what_to_bring' => $data['what_to_bring'] ?? '',
                        'meeting_point_description' => $data['meeting_point_description'] ?? '',
                        'additional_info' => $data['additional_info'] ?? '',
                    ]
                );
            }
        }

        // Synchroniser les médias de la galerie
        $activity->media()->sync($this->selectedMedia);

        session()->flash('success', 'Activité sauvegardée avec succès.');

        return redirect()->route('operator.activities.index');
    }

    public function render()
    {
        return view('livewire.operator.activity.activity-form');
    }
}
