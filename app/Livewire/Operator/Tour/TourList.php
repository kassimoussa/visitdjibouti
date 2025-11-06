<?php

namespace App\Livewire\Operator\Tour;

use App\Models\Tour;
use Livewire\Component;
use Livewire\WithPagination;

class TourList extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $status = '';
    public $duration = '';
    public $region = '';

    // Statistics
    public $statistics = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'duration' => ['except' => ''],
        'region' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadStatistics();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingDuration()
    {
        $this->resetPage();
    }

    public function updatingRegion()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'duration', 'region']);
        $this->resetPage();
    }

    public function loadStatistics()
    {
        $operatorId = auth()->guard('operator')->user()->tourOperator->id;

        $this->statistics = [
            'draft' => Tour::where('tour_operator_id', $operatorId)->where('status', 'draft')->count(),
            'pending_approval' => Tour::where('tour_operator_id', $operatorId)->where('status', 'pending_approval')->count(),
            'approved' => Tour::where('tour_operator_id', $operatorId)->where('status', 'active')->count(),
            'rejected' => Tour::where('tour_operator_id', $operatorId)->where('status', 'rejected')->count(),
        ];
    }

    public function deleteTour($tourId)
    {
        $operatorId = auth()->guard('operator')->user()->tourOperator->id;
        $tour = Tour::where('id', $tourId)
            ->where('tour_operator_id', $operatorId)
            ->first();

        if ($tour && in_array($tour->status, ['draft', 'rejected'])) {
            $tour->delete();
            $this->loadStatistics();
            session()->flash('success', 'Tour supprimé avec succès');
        } else {
            session()->flash('error', 'Vous ne pouvez supprimer que les tours en brouillon ou rejetés');
        }
    }

    public function render()
    {
        $operatorId = auth()->guard('operator')->user()->tourOperator->id;

        $query = Tour::with(['translations', 'featuredImage', 'reservations'])
            ->where('tour_operator_id', $operatorId);

        // Apply filters
        if ($this->search) {
            $query->whereHas('translations', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->duration) {
            $query->where('duration_type', $this->duration);
        }

        if ($this->region) {
            $query->where('region', $this->region);
        }

        $tours = $query->latest()->paginate(10);

        return view('livewire.operator.tour.tour-list', [
            'tours' => $tours,
        ]);
    }
}
