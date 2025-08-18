<?php

namespace App\Livewire\Admin\Event;

use App\Models\Category;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;
    
    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';
    
    // Filtres
    public $search = '';
    public $status = '';
    public $parentCategory = ''; // Catégorie parente
    public $subcategory = ''; // Sous-catégorie
    public $dateFilter = ''; // all, upcoming, ongoing, past
    public $view = 'list'; // 'list' ou 'calendar'
    public $currentLocale = ''; // Langue courante pour l'affichage des événements
    
    // Modal de suppression
    public $eventToDelete = null;
    public $deleteModalVisible = false;
    
    public function mount()
    {
        // Initialiser la langue courante avec la langue de l'application
        $this->currentLocale = app()->getLocale();
    }
    
    /**
     * Basculer entre la vue liste et la vue calendrier
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
    
    public function updatedParentCategory()
    {
        $this->resetPage();
        // Réinitialiser la sous-catégorie quand on change de catégorie parent
        $this->subcategory = '';
    }
    
    public function updatedSubcategory()
    {
        $this->resetPage();
    }
    
    public function updatedDateFilter()
    {
        $this->resetPage();
    }
    
    /**
     * Confirmer la suppression
     */
    public function confirmDelete($eventId)
    {
        $this->eventToDelete = Event::findOrFail($eventId);
        $this->deleteModalVisible = true;
    }
    
    /**
     * Annuler la suppression
     */
    public function cancelDelete()
    {
        $this->eventToDelete = null;
        $this->deleteModalVisible = false;
    }
    
    /**
     * Supprimer l'événement
     */
    public function delete()
    {
        if ($this->eventToDelete) {
            // Détacher toutes les relations avant la suppression
            $this->eventToDelete->categories()->detach();
            $this->eventToDelete->media()->detach();
            
            // Supprimer les inscriptions liées (soft delete)
            $this->eventToDelete->registrations()->delete();
            
            // Supprimer les avis liés (soft delete)
            $this->eventToDelete->reviews()->delete();
            
            // Les traductions seront supprimées automatiquement grâce à la contrainte onDelete cascade
            
            // Supprimer l'événement (soft delete)
            $this->eventToDelete->delete();
            
            // Feedback à l'utilisateur
            session()->flash('success', 'Événement supprimé avec succès.');
            
            // Fermer le modal
            $this->deleteModalVisible = false;
            $this->eventToDelete = null;
        }
    }
    
    /**
     * Obtenir les filtres de date
     */
    public function getDateFilters()
    {
        return [
            'all' => 'Tous les événements',
            'upcoming' => 'À venir',
            'ongoing' => 'En cours',
            'past' => 'Terminés',
        ];
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
        
        $dateFilters = $this->getDateFilters();
        $availableLocales = ['fr', 'en', 'ar'];
        
        $query = Event::with(['categories', 'featuredImage', 'translations']);
        
        // Recherche dans les traductions
        if ($this->search) {
            $query->whereHas('translations', function ($subQuery) {
                $subQuery->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('short_description', 'like', '%' . $this->search . '%');
            })->orWhere('slug', 'like', '%' . $this->search . '%')
              ->orWhere('organizer', 'like', '%' . $this->search . '%')
              ->orWhere('location', 'like', '%' . $this->search . '%');
        }
        
        // Filtre de statut
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
        
        // Filtre de date
        if ($this->dateFilter) {
            $today = now()->toDateString();
            
            switch ($this->dateFilter) {
                case 'upcoming':
                    $query->where('start_date', '>', $today);
                    break;
                case 'ongoing':
                    $query->where('start_date', '<=', $today)
                          ->where('end_date', '>=', $today);
                    break;
                case 'past':
                    $query->where('end_date', '<', $today);
                    break;
            }
        }
        
        // Tri par date de début
        $query->orderBy('start_date', 'desc');
        
        // Pagination
        $events = $query->paginate(10);
        
        return view('livewire.admin.event.event-list', [
            'events' => $events,
            'parentCategories' => $parentCategories,
            'subcategories' => $subcategories,
            'dateFilters' => $dateFilters,
            'availableLocales' => $availableLocales,
            'currentLocale' => $this->currentLocale,
        ]);
    }
}