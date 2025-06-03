<?php

namespace App\Livewire\Admin\Poi;

use App\Models\Category;
use App\Models\PointOfInterest;
use Livewire\Component;
use Livewire\WithPagination;

class PoiList extends Component
{
    use WithPagination;

    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';

    // Filtres
    public $search = '';
    public $status = '';
    public $category = '';
    public $region = '';
    public $view = 'list'; // 'list' ou 'map'

    // Modal de suppression
    public $poiToDelete = null;
    public $deleteModalVisible = false;

    /**
     * Basculer entre la vue liste et la vue carte
     */
    public function toggleView($view)
    {
        $this->view = $view;
        $this->dispatch('viewChanged', viewMode: $view);
    }

    /**
     * Reset pagination quand les filtres changent
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedRegion()
    {
        $this->resetPage();
    }

    /**
     * Confirmer la suppression
     */
    public function confirmDelete($poiId)
    {
        $this->poiToDelete = PointOfInterest::findOrFail($poiId);
        $this->deleteModalVisible = true;
    }

    /**
     * Annuler la suppression
     */
    public function cancelDelete()
    {
        $this->poiToDelete = null;
        $this->deleteModalVisible = false;
    }

    /**
     * Supprimer le POI
     */
    public function delete()
    {
        if ($this->poiToDelete) {
            // Détacher toutes les relations avant la suppression
            $this->poiToDelete->categories()->detach();
            $this->poiToDelete->media()->detach();

            // Supprimer le POI
            $this->poiToDelete->delete();

            // Feedback à l'utilisateur
            session()->flash('success', 'Point d\'intérêt supprimé avec succès.');

            // Fermer le modal
            $this->deleteModalVisible = false;
            $this->poiToDelete = null;
        }
    }

    /**
     * Obtenir la liste des régions pour Djibouti
     */
    public function getRegionsList()
    {
        return [
            'Djibouti' => 'Djibouti',
            'Ali Sabieh' => 'Ali Sabieh',
            'Dikhil' => 'Dikhil',
            'Tadjourah' => 'Tadjourah',
            'Obock' => 'Obock',
            'Arta' => 'Arta',
        ];
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $regions = $this->getRegionsList();

        $pois = PointOfInterest::with(['categories', 'featuredImage'])
            ->when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                return $query->where('status', $this->status);
            })
            ->when($this->category, function ($query) {
                return $query->whereHas('categories', function ($q) {
                    $q->where('categories.id', $this->category);
                });
            })
            ->when($this->region, function ($query) {
                return $query->where('region', $this->region);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.poi.poi-list', [
            'pois' => $pois,
            'categories' => $categories,
            'regions' => $regions,
        ]);
    }
}
