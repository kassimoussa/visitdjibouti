<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use App\Models\MediaTranslation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaManager extends Component
{
    use WithPagination, WithFileUploads;
    
    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $typeFilter = '';
    public $dateFilter = '';
    public $viewMode = 'grid'; // 'grid' ou 'list'
    
    // Propriétés pour les actions en masse
    public $selectedItems = [];
    
    // Propriétés pour la suppression
    public $mediaToDelete = null;
    
    // Propriétés pour l'upload
    public $files = []; // Pour supporter le téléversement multiple
    public $uploadView = false;
    
    // Propriétés pour l'édition
    public $editingMedia = null;
    public $currentLocale = 'fr'; // Langue par défaut
    public $availableLocales = ['fr', 'en', 'ar']; // Langues disponibles
    
    // Propriétés pour les traductions
    public $translations = []; // Stockage temporaire des traductions
    
    // Écouteurs d'événements Livewire
    protected $listeners = [
        'refreshList' => '$refresh',
    ];
    
    public function mount()
    {
        // Initialiser les traductions vides pour toutes les langues disponibles
        $this->resetTranslations();
    }
    
    // Règles de validation
    protected function rules()
    {
        return [
            'files.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xls,xlsx,mp4,webm',
            'translations.*.title' => 'nullable|string|max:255',
            'translations.*.alt_text' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ];
    }
    
    // Messages de validation personnalisés
    protected function messages()
    {
        return [
            'files.*.max' => 'La taille de chaque fichier ne doit pas dépasser 10 Mo.',
            'files.*.mimes' => 'Type de fichier non supporté. Utilisez des formats d\'image, document ou vidéo standards.',
            'translations.fr.title.required' => 'Le titre en français est obligatoire.',
        ];
    }
    
    // Afficher la vue téléversement
    public function showUploadForm()
    {
        $this->uploadView = true;
        $this->editingMedia = null;
        $this->resetTranslations();
        $this->resetValidation();
    }
    
    // Afficher la vue édition
    public function showEditForm($id)
    {
        $this->editingMedia = Media::with('translations')->findOrFail($id);
        $this->resetTranslations();
        
        // Charger les traductions existantes
        foreach ($this->editingMedia->translations as $translation) {
            $this->translations[$translation->locale] = [
                'title' => $translation->title,
                'alt_text' => $translation->alt_text,
                'description' => $translation->description,
            ];
        }
        
        // S'assurer que toutes les langues disponibles ont une structure de données
        foreach ($this->availableLocales as $locale) {
            if (!isset($this->translations[$locale])) {
                $this->translations[$locale] = [
                    'title' => '',
                    'alt_text' => '',
                    'description' => '',
                ];
            }
        }
        
        $this->uploadView = false;
        $this->resetValidation();
    }
    
    // Réinitialiser les traductions
    private function resetTranslations()
    {
        $this->translations = [];
        
        foreach ($this->availableLocales as $locale) {
            $this->translations[$locale] = [
                'title' => '',
                'alt_text' => '',
                'description' => '',
            ];
        }
    }
    
    // Revenir à la liste
    public function backToList()
    {
        $this->uploadView = false;
        $this->editingMedia = null;
        $this->resetTranslations();
        $this->resetExcept(['search', 'typeFilter', 'dateFilter', 'viewMode', 'selectedItems', 'availableLocales', 'currentLocale']);
    }
    
    // Changer la langue en cours d'édition
    public function changeLocale($locale)
    {
        if (in_array($locale, $this->availableLocales)) {
            $this->currentLocale = $locale;
        }
    }
    
    // Changer le mode d'affichage
    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
    }
    
    // Téléverser des fichiers
    public function save()
    {
        $this->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xls,xlsx,mp4,webm',
            'translations.fr.title' => 'required|string|max:255', // Au moins le titre en français est requis
        ]);
        
        try {
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($this->files as $file) {
                try {
                    $originalName = $file->getClientOriginalName();
                    $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
                    
                    // Déterminer le type de média
                    $type = $this->getMediaType($file->getClientOriginalExtension());
                    
                    // Stocker le fichier
                    $path = $file->storeAs("media/{$type}", $fileName, 'public');
                    
                    // Créer l'enregistrement dans la base de données
                    $media = Media::create([
                        'filename' => $fileName,
                        'original_name' => $originalName,
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'path' => 'storage/' . $path,
                        'thumbnail_path' => null,
                        'type' => $type,
                    ]);
                    
                    // Sauvegarder les traductions
                    foreach ($this->translations as $locale => $data) {
                        // Pour le premier fichier utiliser les traductions saisies
                        // Pour les autres fichiers, utiliser le nom du fichier comme titre par défaut
                        MediaTranslation::create([
                            'media_id' => $media->id,
                            'locale' => $locale,
                            'title' => $locale == 'fr' ? ($data['title'] ?: pathinfo($originalName, PATHINFO_FILENAME)) : ($data['title'] ?? ''),
                            'alt_text' => $data['alt_text'] ?? '',
                            'description' => $data['description'] ?? '',
                        ]);
                    }
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }
            
            $this->reset('files');
            $this->resetTranslations();
            
            if ($successCount > 0 && $errorCount == 0) {
                session()->flash('message', $successCount . ' fichier(s) téléversé(s) avec succès !');
            } elseif ($successCount > 0 && $errorCount > 0) {
                session()->flash('message', $successCount . ' fichier(s) téléversé(s) avec succès, mais ' . $errorCount . ' ont échoué.');
            } else {
                session()->flash('error', 'Aucun fichier n\'a pu être téléversé.');
            }
            
            $this->backToList();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du téléversement: ' . $e->getMessage());
        }
    }
    
    // Mettre à jour les détails du média
    public function updateMediaDetails()
    {
        $this->validate([
            'translations.fr.title' => 'required|string|max:255', // Au moins le titre en français est requis
            'translations.*.alt_text' => 'nullable|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);
        
        try {
            // Mettre à jour ou créer les traductions pour chaque langue
            foreach ($this->translations as $locale => $data) {
                MediaTranslation::updateOrCreate(
                    [
                        'media_id' => $this->editingMedia->id,
                        'locale' => $locale,
                    ],
                    [
                        'title' => $data['title'] ?? '',
                        'alt_text' => $data['alt_text'] ?? '',
                        'description' => $data['description'] ?? '',
                    ]
                );
            }
            
            session()->flash('message', 'Média mis à jour avec succès !');
            $this->backToList();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }
    
    // Confirmer la suppression
    public function confirmDelete($id)
    {
        $this->mediaToDelete = $id;
        $this->dispatch('openDeleteModal');
    }
    
    // Supprimer un média
    public function deleteMedia()
    {
        $media = Media::find($this->mediaToDelete);
        
        if (!$media) {
            session()->flash('error', 'Média introuvable.');
            return;
        }
        
        // Supprimer le fichier
        Storage::delete(str_replace('storage/', 'public/', $media->path));
        
        // Supprimer la miniature si elle existe
        if ($media->thumbnail_path) {
            Storage::delete(str_replace('storage/', 'public/', $media->thumbnail_path));
        }
        
        // Supprimer l'enregistrement (les traductions seront supprimées par la cascade)
        $media->delete();
        
        $this->reset('mediaToDelete');
        session()->flash('message', 'Média supprimé avec succès !');
    }
    
    // Supprimer plusieurs médias
    public function deleteSelected()
    {
        if (empty($this->selectedItems)) {
            session()->flash('error', 'Aucun média sélectionné.');
            return;
        }
        
        foreach ($this->selectedItems as $id) {
            $media = Media::find($id);
            if ($media) {
                Storage::delete(str_replace('storage/', 'public/', $media->path));
                if ($media->thumbnail_path) {
                    Storage::delete(str_replace('storage/', 'public/', $media->thumbnail_path));
                }
                $media->delete(); // Supprime également les traductions grâce à onDelete('cascade')
            }
        }
        
        $this->reset('selectedItems');
        session()->flash('message', 'Médias supprimés avec succès !');
    }
    
    // Déterminer le type de média
    private function getMediaType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $videoExtensions = ['mp4', 'webm'];
        
        if (in_array(strtolower($extension), $imageExtensions)) {
            return 'images';
        } elseif (in_array(strtolower($extension), $documentExtensions)) {
            return 'documents';
        } elseif (in_array(strtolower($extension), $videoExtensions)) {
            return 'videos';
        }
        
        return 'others';
    }
    
    // Helper pour formater la taille des fichiers
    public function humanFileSize($size) 
    {
        if ($size >= 1073741824) {
            $size = number_format($size / 1073741824, 2) . ' GB';
        } elseif ($size >= 1048576) {
            $size = number_format($size / 1048576, 2) . ' MB';
        } elseif ($size >= 1024) {
            $size = number_format($size / 1024, 2) . ' KB';
        } elseif ($size > 1) {
            $size = $size . ' bytes';
        } elseif ($size == 1) {
            $size = $size . ' byte';
        } else {
            $size = '0 bytes';
        }
        
        return $size;
    }
    
    // Rendu du composant
    public function render()
    {
        $query = Media::with(['translations' => function($query) {
            $query->where('locale', $this->currentLocale)
                  ->orWhere('locale', config('app.fallback_locale'));
        }]);
        
        // Filtrer par recherche
        if ($this->search) {
            $query->whereHas('translations', function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            })->orWhere('filename', 'like', '%' . $this->search . '%');
        }
        
        // Filtrer par type
        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }
        
        // Filtrer par date
        if ($this->dateFilter) {
            $date = null;
            
            if ($this->dateFilter === 'today') {
                $date = now()->startOfDay();
            } elseif ($this->dateFilter === 'week') {
                $date = now()->subWeek();
            } elseif ($this->dateFilter === 'month') {
                $date = now()->subMonth();
            } elseif ($this->dateFilter === 'year') {
                $date = now()->subYear();
            }
            
            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }
        
        // Trier les résultats (récents d'abord)
        $query->orderBy('created_at', 'desc');
        
        // Paginer les résultats
        $media = $query->paginate(15);
        
        return view('livewire.admin.media-manager', [
            'media' => $media,
        ]);
    }
}