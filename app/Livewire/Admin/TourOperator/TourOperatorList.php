<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperator;
use Livewire\Component;
use Livewire\WithPagination;

class TourOperatorList extends Component
{
    use WithPagination;

    // Filtres et recherche
    public $search = '';

    public $filterFeatured = '';

    public $filterLocale = 'fr';

    public $availableLocales = ['fr', 'en'];

    // Tri
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $tourOperators = TourOperator::with(['translations', 'logo'])
            ->when($this->search, function ($query) {
                $query->whereHas('translations', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterFeatured !== '', function ($query) {
                $query->where('featured', $this->filterFeatured);
            });

        // Tri dynamique
        if ($this->sortField === 'name') {
            // Tri par nom (via traduction)
            $tourOperators->leftJoin('tour_operator_translations', function ($join) {
                $join->on('tour_operators.id', '=', 'tour_operator_translations.tour_operator_id')
                    ->where('tour_operator_translations.locale', '=', $this->filterLocale);
            })
                ->orderBy('tour_operator_translations.name', $this->sortDirection)
                ->select('tour_operators.*');
        } else {
            // Tri par les autres colonnes directes
            $tourOperators->orderBy($this->sortField, $this->sortDirection);
        }

        $tourOperators = $tourOperators->paginate(10);

        return view('livewire.admin.tour-operator.tour-operator-list', [
            'tourOperators' => $tourOperators,
            'availableLocales' => $this->availableLocales,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterFeatured()
    {
        $this->resetPage();
    }

    public function updatingFilterLocale()
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

    public function toggleStatus($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['is_active' => ! $tourOperator->is_active]);

            $status = $tourOperator->is_active ? 'activé' : 'désactivé';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: '.$e->getMessage());
        }
    }

    public function toggleFeatured($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['featured' => ! $tourOperator->featured]);

            $status = $tourOperator->featured ? 'mis en avant' : 'retiré de la mise en avant';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut featured: '.$e->getMessage());
        }
    }

    public function delete($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->delete();

            session()->flash('message', 'Opérateur de tour supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }
}
