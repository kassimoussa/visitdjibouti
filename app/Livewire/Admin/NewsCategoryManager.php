<?php

namespace App\Livewire\Admin;

use App\Models\NewsCategory;
use App\Models\NewsCategoryTranslation;
use App\Traits\WithModal;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class NewsCategoryManager extends Component
{
    use WithPagination;
    use WithModal;

    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';

    // Propriétés pour le formulaire
    public $categoryId = null;
    public $slug = '';
    public $is_active = true;
    
    // Propriétés pour les traductions
    public $translations = [];
    public $availableLocales = [];

    // Propriété pour la recherche
    public $search = '';

    // État du modal (create ou edit)
    public $modalMode = 'create';

    public function mount()
    {
        // Initialiser les langues disponibles
        $this->availableLocales = ['fr', 'en', 'ar'];
        
        // Initialiser le tableau des traductions avec des entrées vides pour chaque langue
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => ''
            ];
        }
    }

    // Règles de validation
    protected function rules()
    {
        $rules = [
            'slug' => $this->modalMode === 'edit' 
                ? 'nullable|string|max:255|unique:news_categories,slug,' . $this->categoryId
                : 'nullable|string|max:255|unique:news_categories,slug',
            'is_active' => 'boolean',
        ];

        // Ajouter des règles pour chaque langue
        $defaultLocale = config('app.fallback_locale', 'fr');
        
        foreach ($this->availableLocales as $locale) {
            // Le nom est obligatoire uniquement pour la langue par défaut
            if ($locale === $defaultLocale) {
                $rules["translations.{$locale}.name"] = 'required|string|max:255';
            } else {
                $rules["translations.{$locale}.name"] = 'nullable|string|max:255';
            }
            
            $rules["translations.{$locale}.description"] = 'nullable|string';
        }

        return $rules;
    }

    /**
     * Méthode appelée quand le nom de la langue par défaut change pour générer le slug
     */
    public function updatedTranslations()
    {
        $defaultLocale = config('app.fallback_locale', 'fr');
        
        if (empty($this->slug) && !empty($this->translations[$defaultLocale]['name'])) {
            $this->slug = Str::slug($this->translations[$defaultLocale]['name']);
        }
    }

    /**
     * Méthode pour ouvrir le modal de création
     */
    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';

        // Utiliser notre méthode du trait WithModal
        $this->openModal('Ajouter une nouvelle catégorie d\'actualité', 'modal-lg');
    }

    /**
     * Méthode pour ouvrir le modal d'édition
     */
    public function openEditModal($id)
    {
        $this->resetForm();
        $this->categoryId = $id;
        $this->modalMode = 'edit';

        // Récupérer la catégorie avec ses traductions
        $category = NewsCategory::with('translations')->findOrFail($id);
        
        $this->slug = $category->slug;
        $this->is_active = $category->is_active;

        // Charger les traductions existantes
        foreach ($category->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name,
                'description' => $translation->description,
            ];
        }

        // Utiliser notre méthode du trait WithModal
        $this->openModal('Modifier la catégorie d\'actualité', 'modal-lg');
    }

    /**
     * Réinitialiser le formulaire
     */
    public function resetForm()
    {
        $this->categoryId = null;
        $this->slug = '';
        $this->is_active = true;

        // Réinitialiser les traductions
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => ''
            ];
        }

        $this->resetErrorBag();
        $this->resetValidation();
    }

    /**
     * Sauvegarder la catégorie
     */
    public function save()
    {
        $this->validate();

        // Générer le slug s'il est vide
        if (empty($this->slug)) {
            $defaultLocale = config('app.fallback_locale', 'fr');
            if (!empty($this->translations[$defaultLocale]['name'])) {
                $this->slug = Str::slug($this->translations[$defaultLocale]['name']);
            }
        }

        if ($this->modalMode === 'create') {
            // Créer une nouvelle catégorie
            $category = NewsCategory::create([
                'slug' => $this->slug,
                'is_active' => $this->is_active,
            ]);
        } else {
            // Mettre à jour la catégorie existante
            $category = NewsCategory::findOrFail($this->categoryId);
            $category->update([
                'slug' => $this->slug,
                'is_active' => $this->is_active,
            ]);
        }

        // Sauvegarder les traductions
        foreach ($this->translations as $locale => $translation) {
            if (!empty($translation['name'])) {
                $category->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'],
                        'description' => $translation['description'],
                    ]
                );
            }
        }

        // Fermer le modal et afficher un message de succès
        $this->closeModal();
        session()->flash('message', $this->modalMode === 'create' 
            ? 'Catégorie créée avec succès!' 
            : 'Catégorie modifiée avec succès!');

        $this->resetForm();
    }

    /**
     * Supprimer une catégorie
     */
    public function delete($id)
    {
        $category = NewsCategory::findOrFail($id);

        // Vérifier s'il y a des actualités liées
        if ($category->news()->count() > 0) {
            session()->flash('error', 'Impossible de supprimer une catégorie qui contient des actualités');
            return;
        }

        $category->delete();

        session()->flash('message', 'Catégorie supprimée avec succès!');
    }

    /**
     * Basculer le statut d'une catégorie
     */
    public function toggleStatus($id)
    {
        $category = NewsCategory::findOrFail($id);
        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activée' : 'désactivée';
        session()->flash('message', "Catégorie {$status} avec succès!");
    }

    /**
     * Mise à jour de la recherche
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        $categories = NewsCategory::with(['translations' => function($query) {
            $query->where('locale', config('app.fallback_locale', 'fr'));
        }])
        ->withCount('news')
        ->when($this->search, function ($query) {
            $query->whereHas('translations', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('livewire.admin.news-category-manager', [
            'categories' => $categories
        ]);
    }
}