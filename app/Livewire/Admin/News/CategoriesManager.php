<?php

namespace App\Livewire\Admin\News;

use App\Models\NewsCategory;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class CategoriesManager extends Component
{
    use WithPagination;

    // Propriétés pour la gestion des catégories
    public $search = '';
    public $sortField = 'sort_order';
    public $sortDirection = 'asc';
    public $perPage = 10;

    // Propriétés pour les modals
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    
    // Propriétés pour l'édition
    public $editingCategoryId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $color = '#3498db';
    public $is_active = true;
    public $sort_order = 0;
    
    // Propriétés pour la suppression
    public $deletingCategoryId = null;
    public $deletingCategoryName = '';

    protected $rules = [
        'name' => 'required|max:255',
        'slug' => 'nullable|max:255|unique:news_categories,slug',
        'description' => 'nullable|max:500',
        'color' => 'nullable|regex:/^#[a-fA-F0-9]{6}$/',
        'is_active' => 'boolean',
        'sort_order' => 'nullable|integer|min:0',
    ];

    protected $messages = [
        'name.required' => 'Le nom de la catégorie est obligatoire.',
        'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'slug.unique' => 'Ce slug est déjà utilisé par une autre catégorie.',
        'color.regex' => 'Le format de couleur doit être hexadécimal (#RRGGBB).',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedName()
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function openEditModal($categoryId)
    {
        $category = NewsCategory::findOrFail($categoryId);
        
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->color = $category->color ?? '#3498db';
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order ?? 0;
        
        $this->rules['slug'] = 'nullable|max:255|unique:news_categories,slug,' . $categoryId;
        
        $this->showEditModal = true;
    }

    public function openDeleteModal($categoryId)
    {
        $category = NewsCategory::findOrFail($categoryId);
        
        $this->deletingCategoryId = $category->id;
        $this->deletingCategoryName = $category->name;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function store()
    {
        $this->validate();

        try {
            if (empty($this->slug)) {
                $this->slug = Str::slug($this->name);
            }

            NewsCategory::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'color' => $this->color,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Catégorie créée avec succès'
            ]);

            $this->closeModal();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $category = NewsCategory::findOrFail($this->editingCategoryId);
            
            if (empty($this->slug)) {
                $this->slug = Str::slug($this->name);
            }

            $category->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'color' => $this->color,
                'is_active' => $this->is_active,
                'sort_order' => $this->sort_order,
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Catégorie modifiée avec succès'
            ]);

            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        try {
            $category = NewsCategory::findOrFail($this->deletingCategoryId);
            
            // Vérifier s'il y a des articles dans cette catégorie
            $newsCount = $category->news()->count();
            
            if ($newsCount > 0) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'message' => "Impossible de supprimer cette catégorie car elle contient {$newsCount} article(s)"
                ]);
                $this->closeModal();
                return;
            }

            $category->delete();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Catégorie supprimée avec succès'
            ]);

            $this->closeModal();
            $this->resetPage();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ]);
        }
    }

    public function toggleStatus($categoryId)
    {
        try {
            $category = NewsCategory::findOrFail($categoryId);
            $category->update(['is_active' => !$category->is_active]);
            
            $status = $category->is_active ? 'activée' : 'désactivée';
            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "Catégorie {$status} avec succès"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors du changement de statut'
            ]);
        }
    }

    protected function resetForm()
    {
        $this->editingCategoryId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->color = '#3498db';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->deletingCategoryId = null;
        $this->deletingCategoryName = '';
        
        // Reset unique rule
        $this->rules['slug'] = 'nullable|max:255|unique:news_categories,slug';
    }

    public function getFilteredCategories()
    {
        try {
            // Version simplifiée sans withCount pour debug
            $query = NewsCategory::query();
            
            if ($this->search) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                    if (\Schema::hasColumn('news_categories', 'description')) {
                        $q->orWhere('description', 'like', '%' . $this->search . '%');
                    }
                });
            }
            
            return $query->orderBy($this->sortField, $this->sortDirection)
                         ->paginate($this->perPage);
                         
        } catch (\Exception $e) {
            \Log::error('Error in getFilteredCategories: ' . $e->getMessage());
            // Retourner une collection vide en cas d'erreur
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage);
        }
    }

    public function render()
    {
        try {
            $categories = $this->getFilteredCategories();
            return view('livewire.admin.news.categories-manager', [
                'categories' => $categories
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in CategoriesManager render: ' . $e->getMessage());
            return view('livewire.admin.news.categories-manager', [
                'categories' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->perPage)
            ]);
        }
    }
}