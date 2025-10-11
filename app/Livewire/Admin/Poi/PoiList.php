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
    public $parentCategory = ''; // Catégorie parente
    public $subcategory = ''; // Sous-catégorie
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
        
        // Si on passe en vue carte, envoyer les données
        if ($view === 'map') {
            $this->dispatchMapUpdate();
        }
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
        $this->dispatchMapUpdate();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
        $this->dispatchMapUpdate();
    }
    
    public function updatedParentCategory()
    {
        $this->resetPage();
        // Réinitialiser la sous-catégorie quand on change de catégorie parent
        $this->subcategory = '';
        $this->dispatchMapUpdate();
    }
    
    public function updatedSubcategory()
    {
        $this->resetPage();
        $this->dispatchMapUpdate();
    }
    
    public function updatedRegion()
    {
        $this->resetPage();
        $this->dispatchMapUpdate();
    }
    
    /**
     * Dispatch map update avec les données filtrées
     */
    private function dispatchMapUpdate()
    {
        if ($this->view === 'map') {
            $poisForMap = $this->getFilteredPoisForMap();
            $this->dispatch('mapDataUpdated', pois: $poisForMap->toArray(), locale: $this->currentLocale);
        }
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
     * Récupérer tous les POI filtrés (sans pagination) pour la carte
     */
    private function getFilteredPoisForMap()
    {
        $query = Poi::with(['categories.translations', 'featuredImage', 'translations'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
        
        // Appliquer les mêmes filtres que pour la liste
        if ($this->search) {
            $query->whereHas('translations', function ($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            })->orWhere('slug', 'like', '%' . $this->search . '%');
        }
        
        if ($this->status) {
            $query->where('status', $this->status);
        }
        
        if ($this->parentCategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->parentCategory);
            });
        }
        
        if ($this->subcategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->subcategory);
            });
        }
        
        if ($this->region) {
            $query->where('region', $this->region);
        }
        
        return $query->get();
    }
    
    /**
     * Rendu du composant
     */
    public function render()
    {
        // Récupérer les catégories avec leurs traductions
        $fallbackLocale = config('app.fallback_locale', 'fr');
        $locale = $this->currentLocale ?: app()->getLocale();
        
        // Catégories principales (parents seulement)
        $parentCategories = Category::with(['translations' => function($query) use ($locale, $fallbackLocale) {
            $query->where('locale', $locale)
                  ->orWhere('locale', $fallbackLocale);
        }])->whereNull('parent_id')->get()->sortBy(function($category) use ($locale, $fallbackLocale) {
            $translation = $category->translation($locale);
            return $translation ? $translation->name : '';
        });
        
        // Sous-catégories basées sur la catégorie parent sélectionnée
        $subcategories = collect();
        if ($this->parentCategory) {
            $subcategories = Category::with(['translations' => function($query) use ($locale, $fallbackLocale) {
                $query->where('locale', $locale)
                      ->orWhere('locale', $fallbackLocale);
            }])->where('parent_id', $this->parentCategory)->get()->sortBy(function($category) use ($locale, $fallbackLocale) {
                $translation = $category->translation($locale);
                return $translation ? $translation->name : '';
            });
        }
        
        $regions = $this->getRegionsList();
        $availableLocales = ['fr', 'en']; // Langues disponibles dans votre application
        
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
        
        // Filtrage par catégorie parent
        if ($this->parentCategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->parentCategory);
            });
        }
        
        // Filtrage par sous-catégorie
        if ($this->subcategory) {
            $query->whereHas('categories', function ($q) {
                $q->where('categories.id', $this->subcategory);
            });
        }
        
        if ($this->region) {
            $query->where('region', $this->region);
        }
        
        // Tri par date de mise à jour
        $query->orderBy('updated_at', 'desc');
        
        // Pagination
        $pois = $query->paginate(10);
        
        // POI filtrés pour la carte (tous les résultats sans pagination)
        $poisForMap = $this->getFilteredPoisForMap();
        
        return view('livewire.admin.poi.poi-list', [
            'pois' => $pois,
            'poisForMap' => $poisForMap,
            'parentCategories' => $parentCategories,
            'subcategories' => $subcategories,
            'regions' => $regions,
            'availableLocales' => $availableLocales,
            'currentLocale' => $this->currentLocale,
        ]);
    }
}