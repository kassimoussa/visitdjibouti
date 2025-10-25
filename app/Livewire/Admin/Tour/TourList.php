<?php

namespace App\Livewire\Admin\Tour;

use App\Models\Tour;
use App\Models\TourOperator;
use Livewire\Component;
use Livewire\WithPagination;

class TourList extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $operatorFilter = '';

    public $typeFilter = '';

    public $difficultyFilter = '';

    // Tri
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

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

    /**
     * Gérer le tri des colonnes
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            // Inverser la direction si on clique sur la même colonne
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            // Nouvelle colonne, direction ascendante par défaut
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
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

    public function render()
    {
        $query = Tour::with(['tourOperator.translations', 'target', 'translations']);

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

        // Tri dynamique
        if ($this->sortField === 'title') {
            // Tri par titre (via traduction)
            $locale = session('locale', 'fr');
            $query->leftJoin('tour_translations', function ($join) use ($locale) {
                $join->on('tours.id', '=', 'tour_translations.tour_id')
                    ->where('tour_translations.locale', '=', $locale);
            })
                ->orderBy('tour_translations.title', $this->sortDirection)
                ->select('tours.*');
        } else {
            // Tri par les autres colonnes directes
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $tours = $query->paginate(20);

        $tourOperators = TourOperator::active()->with('translations')->get();

        return view('livewire.admin.tour.tour-list', [
            'tours' => $tours,
            'tourOperators' => $tourOperators,
        ]);
    }
}
