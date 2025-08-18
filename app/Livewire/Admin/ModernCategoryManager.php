<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class ModernCategoryManager extends Component
{
    use WithPagination;

    // États de l'interface
    public $viewMode = 'main'; // 'main', 'subcategories'
    public $selectedParentId = null;
    public $selectedParent = null;
    
    // Modal de création/édition
    public $showModal = false;
    public $modalMode = 'create'; // 'create', 'edit'
    public $editingCategory = null;
    
    // Formulaire
    public $categoryId = null;
    public $parent_id = null;
    public $slug = '';
    public $icon = 'fas fa-folder';
    public $color = '#3498db';
    public $is_active = true;
    public $sort_order = 1;
    public $translations = [];
    
    // Recherche et filtres
    public $search = '';
    public $searchSubcategories = '';

    protected $listeners = [
        'categoryDeleted' => '$refresh',
        'categoryUpdated' => '$refresh'
    ];

    public function mount()
    {
        $this->initializeTranslations();
    }

    public function initializeTranslations()
    {
        $locales = ['fr', 'en', 'ar'];
        foreach ($locales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => ''
            ];
        }
    }

    /**
     * Passer à la vue des sous-catégories
     */
    public function viewSubcategories($parentId)
    {
        $this->selectedParentId = $parentId;
        $this->selectedParent = Category::with('translations')->find($parentId);
        $this->viewMode = 'subcategories';
        $this->searchSubcategories = '';
    }

    /**
     * Retourner à la vue principale
     */
    public function backToMain()
    {
        $this->viewMode = 'main';
        $this->selectedParentId = null;
        $this->selectedParent = null;
        $this->search = '';
    }

    /**
     * Ouvrir le modal de création
     */
    public function create($parentId = null)
    {
        $this->resetForm();
        $this->parent_id = $parentId;
        $this->modalMode = 'create';
        $this->showModal = true;
        
        // Auto-générer sort_order
        $maxOrder = Category::where('parent_id', $parentId)->max('sort_order') ?? 0;
        $this->sort_order = $maxOrder + 1;
    }

    /**
     * Ouvrir le modal d'édition
     */
    public function edit($categoryId)
    {
        $category = Category::with('translations')->find($categoryId);
        
        $this->categoryId = $category->id;
        $this->parent_id = $category->parent_id;
        $this->slug = $category->slug;
        $this->icon = $category->icon;
        $this->color = $category->color;
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order;
        
        // Charger les traductions
        foreach ($category->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name,
                'description' => $translation->description ?? ''
            ];
        }
        
        $this->modalMode = 'edit';
        $this->showModal = true;
    }

    /**
     * Sauvegarder la catégorie
     */
    public function save()
    {
        $this->validate([
            'translations.fr.name' => 'required|string|max:255',
            'icon' => 'required|string',
            'color' => 'required|string',
            'sort_order' => 'required|integer|min:1'
        ]);

        // Générer le slug depuis le nom français
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->translations['fr']['name']);
        }

        if ($this->modalMode === 'create') {
            $category = Category::create([
                'parent_id' => $this->parent_id ?: null,
                'slug' => $this->slug,
                'icon' => $this->icon,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
                'is_active' => $this->is_active
            ]);
        } else {
            $category = Category::find($this->categoryId);
            $category->update([
                'parent_id' => $this->parent_id ?: null,
                'slug' => $this->slug,
                'icon' => $this->icon,
                'color' => $this->color,
                'sort_order' => $this->sort_order,
                'is_active' => $this->is_active
            ]);
        }

        // Sauvegarder les traductions
        foreach ($this->translations as $locale => $translation) {
            if (!empty($translation['name'])) {
                CategoryTranslation::updateOrCreate([
                    'category_id' => $category->id,
                    'locale' => $locale
                ], [
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? ''
                ]);
            }
        }

        $this->showModal = false;
        $this->resetForm();
        session()->flash('success', 'Catégorie sauvegardée avec succès !');
    }

    /**
     * Supprimer une catégorie
     */
    public function delete($categoryId)
    {
        $category = Category::find($categoryId);
        
        // Vérifier s'il y a des sous-catégories
        if ($category->children()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer une catégorie qui a des sous-catégories.');
            return;
        }

        $category->delete();
        session()->flash('success', 'Catégorie supprimée avec succès !');
    }

    /**
     * Basculer l'état actif/inactif
     */
    public function toggleActive($categoryId)
    {
        $category = Category::find($categoryId);
        $category->update(['is_active' => !$category->is_active]);
        
        session()->flash('success', 'État de la catégorie mis à jour !');
    }

    /**
     * Fermer le modal
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Réinitialiser le formulaire
     */
    public function resetForm()
    {
        $this->categoryId = null;
        $this->parent_id = null;
        $this->slug = '';
        $this->icon = 'fas fa-folder';
        $this->color = '#3498db';
        $this->is_active = true;
        $this->sort_order = 1;
        $this->initializeTranslations();
    }

    /**
     * Obtenir les catégories principales
     */
    public function getMainCategories()
    {
        return Category::with(['translations', 'children'])
            ->whereNull('parent_id')
            ->when($this->search, function($query) {
                $query->whereHas('translations', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Obtenir les sous-catégories
     */
    public function getSubcategories()
    {
        if (!$this->selectedParentId) {
            return collect();
        }

        return Category::with('translations')
            ->where('parent_id', $this->selectedParentId)
            ->when($this->searchSubcategories, function($query) {
                $query->whereHas('translations', function($q) {
                    $q->where('name', 'like', '%' . $this->searchSubcategories . '%');
                });
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.modern-category-manager', [
            'mainCategories' => $this->getMainCategories(),
            'subcategories' => $this->getSubcategories()
        ]);
    }
}