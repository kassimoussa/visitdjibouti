<?php

namespace App\Livewire\Admin\Poi;

use App\Models\Category;
use App\Models\Poi;
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
    public $currentLocale = ''; // Langue courante pour l'affichage des POIs
    
    // Modal de suppression
    public $poiToDelete = null;
    public $deleteModalVisible = false;
    
    public function mount()
    {
        // Initialiser la langue courante avec la langue de l'application
        $this->currentLocale = app()->getLocale();
    }
    
    /**
     * Basculer entre la vue liste et la vue carte
     */
    public function toggleView($view)
    {
        $this->view = $view;
        $this->dispatch('viewChanged', viewMode: $view);
    }
    
    /**
     * Changer la langue d'affichage
     */
    public function changeLocale($locale)
    {
        $this->currentLocale = $locale;
        $this->resetPage();
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
        $this->poiToDelete = Poi::findOrFail($poiId);
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
            
            // Les traductions seront supprimées automatiquement grâce à la contrainte onDelete cascade
            
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
        // Récupérer les catégories avec leurs traductions
        // et les trier par nom dans la langue courante
        $fallbackLocale = config('app.fallback_locale', 'fr');
        $locale = $this->currentLocale ?: app()->getLocale();
        
        // Si les catégories ont également un système multilingue similaire
        $categories = Category::with(['translations' => function($query) use ($locale, $fallbackLocale) {
            $query->where('locale', $locale)
                  ->orWhere('locale', $fallbackLocale);
        }])->get()->sortBy(function($category) use ($locale, $fallbackLocale) {
            // Récupérer la traduction dans la langue courante ou dans la langue par défaut
            $translation = $category->translation($locale);
            return $translation ? $translation->name : '';
        });
        
        $regions = $this->getRegionsList();
        $availableLocales = ['fr', 'en', 'ar']; // Langues disponibles dans votre application
        
        $query = Poi::with(['categories', 'featuredImage', 'translations']);
        
        // Recherche dans les traductions
        if ($this->search) {
            $query->whereHas('translations', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            })->orWhere('slug', 'like', '%' . $this->search . '%');
        }
        
        // Filtres additionnels
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->category) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->category);
            });
        }
        
        if ($this->region) {
            $query->where('region', $this->region);
        }
        
        // Tri par date de mise à jour
        $query->orderBy('updated_at', 'desc');
        
        // Pagination
        $pois = $query->paginate(10);
        
        return view('livewire.admin.poi.poi-list', [
            'pois' => $pois,
            'categories' => $categories,
            'regions' => $regions,
            'availableLocales' => $availableLocales,
            'currentLocale' => $this->currentLocale,
        ]);
    }
}