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

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $tourOperators = TourOperator::with(['translations', 'logo'])
            ->when($this->search, function ($query) {
                $query->whereHas('translations', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterFeatured !== '', function ($query) {
                $query->where('featured', $this->filterFeatured);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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

    public function toggleStatus($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['is_active' => !$tourOperator->is_active]);
            
            $status = $tourOperator->is_active ? 'activé' : 'désactivé';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    public function toggleFeatured($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->update(['featured' => !$tourOperator->featured]);
            
            $status = $tourOperator->featured ? 'mis en avant' : 'retiré de la mise en avant';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut featured: ' . $e->getMessage());
        }
    }

    public function delete($tourOperatorId)
    {
        try {
            $tourOperator = TourOperator::findOrFail($tourOperatorId);
            $tourOperator->delete();
            
            session()->flash('message', 'Opérateur de tour supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}