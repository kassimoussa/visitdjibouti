<?php

namespace App\Livewire\Admin\Tour;

use Livewire\Component;
use App\Models\Tour;
use App\Models\TourOperator;
use Livewire\WithPagination;

class TourList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $operatorFilter = '';
    public $typeFilter = '';
    public $difficultyFilter = '';

    protected $listeners = ['tourDeleted' => '$refresh'];

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

    public function deleteTour(Tour $tour)
    {
        try {
            $tour->delete();
            session()->flash('success', 'Tour supprimé avec succès');
            $this->dispatch('tourDeleted');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression du tour: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Tour::with(['tourOperator.translations', 'target', 'translations', 'schedules']);

        // Apply filters
        if (!empty($this->search)) {
            $query->whereHas('translations', function ($q) {
                $q->where('title', 'LIKE', '%' . $this->search . '%');
            });
        }

        if (!empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        if (!empty($this->operatorFilter)) {
            $query->where('tour_operator_id', $this->operatorFilter);
        }

        if (!empty($this->typeFilter)) {
            $query->where('type', $this->typeFilter);
        }

        if (!empty($this->difficultyFilter)) {
            $query->where('difficulty_level', $this->difficultyFilter);
        }

        $tours = $query->orderBy('created_at', 'desc')->paginate(20);

        $tourOperators = TourOperator::active()->with('translations')->get();

        return view('livewire.admin.tour.tour-list', [
            'tours' => $tours,
            'tourOperators' => $tourOperators,
        ]);
    }
}