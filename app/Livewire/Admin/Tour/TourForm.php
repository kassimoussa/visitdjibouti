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

    protected function rules()
    {
        return [
            'tour.tour_operator_id' => 'required|exists:tour_operators,id',
            'tour.type' => 'required|string',
            'tour.target_id' => 'nullable|integer',
            'tour.target_type' => 'nullable|string',
            'tour.start_date' => 'required|date',
            'tour.end_date' => 'nullable|date|after_or_equal:tour.start_date',
            'tour.start_time' => 'nullable|date_format:H:i',
            'tour.end_time' => 'nullable|date_format:H:i',
            'tour.price' => 'required|numeric|min:0',
            'tour.currency' => 'required|string|size:3',
            'tour.max_participants' => 'nullable|integer|min:1',
            'tour.difficulty_level' => 'required|in:easy,moderate,difficult,expert',
            'tour.status' => 'required|in:active,suspended,archived',
            'tour.is_featured' => 'boolean',
            'tour.weather_dependent' => 'boolean',
            'tour.meeting_point_address' => 'nullable|string|max:255',
            'tour.cancellation_policy' => 'nullable|string',
            'featuredImageId' => 'nullable|exists:media,id',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ];
    }

    public function mount(Tour $tour = null)
    {
        $this->tour = $tour ?? new Tour();
        $this->tourOperators = TourOperator::all();
        $this->pois = Poi::all();
        $this->events = Event::all();
        $this->allMedia = Media::all();

        if ($this->tour->exists) {
            foreach ($this->tour->translations as $translation) {
                $this->translations[$translation->locale] = $translation->toArray();
            }
            $this->selectedMedia = $this->tour->media->pluck('id')->toArray();
            $this->featuredImageId = $this->tour->featured_image_id;
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->tour->slug)) {
            $this->tour->slug = Str::slug($this->translations['fr']['title'] ?? 'tour-' . uniqid());
        }

        $this->tour->featured_image_id = $this->featuredImageId;
        $this->tour->save();

        foreach ($this->translations as $locale => $data) {
            $this->tour->translations()->updateOrCreate(
                ['locale' => $locale],
                $data
            );
        }

        $this->tour->media()->sync($this->selectedMedia);

        session()->flash('success', 'Tour sauvegardé avec succès.');
        return redirect()->route('tours.index');
    }

    public function render()
    {
        return view('livewire.admin.tour.tour-form');
    }
}
