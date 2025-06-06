<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Traits\WithModal;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;
    use WithModal; // Utiliser notre trait de modal

    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';

    // Propriétés pour le formulaire
    public $categoryId = null;
    public $slug = '';
    public $icon = 'fas fa-folder';
    public const DEFAULT_COLOR = '#2563eb'; // Couleur bleue par défaut
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
        $this->availableLocales = config('app.available_locales', ['fr']);
        
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
                ? 'nullable|string|max:255|unique:categories,slug,' . $this->categoryId
                : 'nullable|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:50',
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
        $this->openModal('Ajouter une nouvelle catégorie', 'modal-lg');
    }

    /**
     * Méthode pour ouvrir le modal d'édition
     */
    public function openEditModal($id)
    {
        $this->resetForm();
        $this->modalMode = 'edit';
        $this->categoryId = $id;

        $category = Category::with('translations')->find($id);

        $this->slug = $category->slug;
        $this->icon = $category->icon ?? 'fas fa-folder';
        $this->is_active = $category->is_active;
        
        // Charger les traductions existantes
        foreach ($category->translations as $translation) {
            $this->translations[$translation->locale] = [
                'name' => $translation->name,
                'description' => $translation->description
            ];
        }

        // Utiliser notre méthode du trait WithModal
        $this->openModal('Modifier la catégorie', 'modal-lg');
    }

    /**
     * Méthode pour vider le formulaire
     */
    public function resetForm()
    {
        $this->reset(['categoryId', 'slug', 'is_active']);
        $this->icon = 'fas fa-folder';
        $this->is_active = true;
        
        // Réinitialiser les traductions
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'name' => '',
                'description' => ''
            ];
        }
        
        $this->resetValidation();
    }

    /**
     * Surcharge de la méthode handleModalClosed du trait WithModal
     */
    public function handleModalClosed()
    {
        parent::handleModalClosed();
        $this->resetForm();
    }

    /**
     * Surcharge de la méthode handleModalAction du trait WithModal
     */
    public function handleModalAction()
    {
        $this->save();
    }

    /**
     * Méthode pour enregistrer une catégorie
     */
    public function save()
    {
        // Validation des données
        $this->validate();

        // Si le slug est vide, le générer à partir du nom dans la langue par défaut
        $defaultLocale = config('app.fallback_locale', 'fr');
        if (empty($this->slug) && !empty($this->translations[$defaultLocale]['name'])) {
            $this->slug = Str::slug($this->translations[$defaultLocale]['name']);
        }

        if ($this->modalMode === 'create') {
            // Création d'une nouvelle catégorie (données non traduisibles)
            $category = Category::create([
                'slug' => $this->slug,
                'icon' => $this->icon,
                'color' => self::DEFAULT_COLOR,
                'is_active' => $this->is_active,
            ]);

            // Ajouter les traductions
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['name'])) {
                    CategoryTranslation::create([
                        'category_id' => $category->id,
                        'locale' => $locale,
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null
                    ]);
                }
            }

            // Notification de succès
            session()->flash('success', 'Catégorie créée avec succès !');
        } else {
            // Mise à jour d'une catégorie existante (données non traduisibles)
            $category = Category::find($this->categoryId);
            $category->update([
                'slug' => $this->slug,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);

            // Mettre à jour ou créer les traductions
            foreach ($this->translations as $locale => $translation) {
                if (!empty($translation['name'])) {
                    CategoryTranslation::updateOrCreate(
                        ['category_id' => $category->id, 'locale' => $locale],
                        [
                            'name' => $translation['name'],
                            'description' => $translation['description'] ?? null
                        ]
                    );
                }
            }

            // Notification de succès
            session()->flash('success', 'Catégorie mise à jour avec succès !');
        }

        // Fermer le modal
        $this->closeModal();
    }

    /**
     * Méthode pour supprimer une catégorie
     */
    public function delete($id)
    {
        $category = Category::find($id);
        
        // Les traductions seront supprimées automatiquement grâce à la cascade
        $category->delete();

        // Notification de succès
        session()->flash('success', 'Catégorie supprimée avec succès !');
    }

    /**
     * Méthode de rendu du composant
     */
    public function render()
    {
        $defaultLocale = config('app.fallback_locale', 'fr');
        
        $categories = Category::with(['translations' => function($query) use ($defaultLocale) {
            $query->where('locale', $defaultLocale);
        }])
        ->when($this->search, function ($query) use ($defaultLocale) {
            $query->whereHas('translations', function($q) use ($defaultLocale) {
                $q->where('locale', $defaultLocale)
                  ->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhere('slug', 'like', '%' . $this->search . '%');
        })
        ->orderBy('slug')
        ->paginate(10);

        return view('livewire.admin.category-manager', [
            'categories' => $categories,
            'availableLocales' => $this->availableLocales
        ]);
    }

    // Ajoutez un écouteur pour l'événement envoyé par le sélecteur d'icônes
    protected function getListeners()
    {
        return ['icon-selected' => 'updateIcon'];
    }

    // Méthode pour gérer la sélection d'icône
    public function updateIcon($icon)
    {
        $this->icon = $icon;
    }
}