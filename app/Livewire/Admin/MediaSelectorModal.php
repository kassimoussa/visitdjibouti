<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class MediaSelectorModal extends Component
{
    use WithPagination, WithFileUploads;

    // Configuration du modal
    public $isOpen = false;
    public $selectionMode = 'single'; // 'single', 'multiple'
    public $selectedImages = [];
    public $preselectedImages = [];
    
    // Filtres et recherche
    public $search = '';
    public $typeFilter = 'images';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    
    // Upload
    public $showUploadArea = false;
    public $uploadFiles = [];
    public $isUploading = false;
    
    // Pagination
    public $perPage = 20;
    
    // Preview
    public $previewImage = null;
    public $showPreview = false;

    protected $rules = [
        'uploadFiles.*' => 'image|max:10240|mimes:jpeg,png,jpg,gif,webp',
    ];
    
    protected $messages = [
        'uploadFiles.*.image' => 'Le fichier doit être une image.',
        'uploadFiles.*.max' => 'Le fichier ne doit pas dépasser 10 MB.',
        'uploadFiles.*.mimes' => 'Le fichier doit être au format: jpeg, png, jpg, gif, webp.',
    ];

    protected $listeners = ['openMediaSelector', 'closeMediaSelector'];

    /**
     * Réinitialiser la pagination lors du changement de recherche
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    /**
     * Ouvrir le modal de sélection
     */
    #[On('open-media-selector')]
    public function openMediaSelector($mode = 'single', $preselected = [])
    {
        $this->isOpen = true;
        $this->selectionMode = $mode;
        $this->preselectedImages = is_array($preselected) ? $preselected : [];
        $this->selectedImages = $this->preselectedImages;
        $this->resetPage();
        $this->dispatch('modal-opened');
    }

    /**
     * Fermer le modal
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['search', 'typeFilter', 'selectedImages', 'showUploadArea', 'uploadFiles', 'showPreview', 'previewImage']);
        $this->dispatch('modal-closed');
    }

    /**
     * Sélectionner/désélectionner une image
     */
    public function toggleImage($mediaId)
    {
        if ($this->selectionMode === 'single') {
            $this->selectedImages = [$mediaId];
        } else {
            if (in_array($mediaId, $this->selectedImages)) {
                $this->selectedImages = array_values(array_diff($this->selectedImages, [$mediaId]));
            } else {
                $this->selectedImages[] = $mediaId;
            }
        }
    }

    /**
     * Sélectionner toutes les images visibles
     */
    public function selectAll()
    {
        if ($this->selectionMode === 'multiple') {
            $mediaIds = $this->getMediaQuery()->pluck('id')->toArray();
            $this->selectedImages = array_unique(array_merge($this->selectedImages, $mediaIds));
        }
    }

    /**
     * Désélectionner toutes les images
     */
    public function deselectAll()
    {
        $this->selectedImages = [];
    }

    /**
     * Confirmer la sélection
     */
    public function confirmSelection()
    {
        $this->dispatch('media-selected', $this->selectedImages);
        $this->closeModal();
    }

    /**
     * Afficher l'aperçu d'une image
     */
    public function showImagePreview($mediaId)
    {
        $this->previewImage = Media::find($mediaId);
        $this->showPreview = true;
    }

    /**
     * Fermer l'aperçu
     */
    public function closePreview()
    {
        $this->showPreview = false;
        $this->previewImage = null;
    }

    /**
     * Basculer la zone d'upload
     */
    public function toggleUploadArea()
    {
        $this->showUploadArea = !$this->showUploadArea;
        if (!$this->showUploadArea) {
            $this->uploadFiles = [];
        }
    }

    /**
     * Upload de nouveaux fichiers
     */
    public function uploadFiles()
    {
        \Log::info('Upload files called', [
            'uploadFiles_type' => gettype($this->uploadFiles),
            'uploadFiles_count' => is_array($this->uploadFiles) ? count($this->uploadFiles) : 0,
            'uploadFiles_empty' => empty($this->uploadFiles)
        ]);

        // Vérifier la présence de fichiers
        if (!$this->uploadFiles || !is_array($this->uploadFiles) || count($this->uploadFiles) === 0) {
            session()->flash('warning', 'Aucun fichier sélectionné. Veuillez sélectionner des fichiers avant d\'uploader.');
            return;
        }

        // Validation des fichiers
        try {
            $this->validate();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur de validation: ' . $e->getMessage());
            \Log::error('Validation error:', ['error' => $e->getMessage()]);
            return;
        }
        
        $this->isUploading = true;
        $uploadedIds = [];
        $errors = [];

        try {
            foreach ($this->uploadFiles as $index => $file) {
                try {
                    \Log::info('Processing file', [
                        'index' => $index,
                        'original_name' => $file ? $file->getClientOriginalName() : 'null',
                        'size' => $file ? $file->getSize() : 'null'
                    ]);
                    
                    if (!$file) {
                        $errors[] = "Fichier à l'index {$index} est null";
                        continue;
                    }
                    
                    $media = $this->createMediaFromFile($file);
                    $uploadedIds[] = $media->id;
                    \Log::info('File uploaded successfully', ['media_id' => $media->id]);
                } catch (\Exception $e) {
                    $filename = $file ? $file->getClientOriginalName() : "fichier_{$index}";
                    $errors[] = "Erreur avec {$filename}: {$e->getMessage()}";
                    \Log::error('Erreur upload fichier: ' . $e->getMessage(), [
                        'file' => $filename,
                        'index' => $index
                    ]);
                }
            }

            if (count($uploadedIds) > 0) {
                // Sélectionner automatiquement les images uploadées
                if ($this->selectionMode === 'single' && count($uploadedIds) > 0) {
                    $this->selectedImages = [end($uploadedIds)];
                } else {
                    $this->selectedImages = array_unique(array_merge($this->selectedImages, $uploadedIds));
                }

                $this->reset(['uploadFiles', 'showUploadArea']);
                $this->dispatch('media-uploaded', count($uploadedIds));
                
                $message = count($uploadedIds) . ' fichier(s) uploadé(s) avec succès.';
                if (!empty($errors)) {
                    $message .= ' Erreurs: ' . implode(', ', $errors);
                }
                session()->flash('success', $message);
                
                // Recharger la liste des médias
                $this->resetPage();
            } else {
                session()->flash('error', 'Aucun fichier n\'a pu être uploadé. ' . implode(', ', $errors));
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur générale lors de l\'upload: ' . $e->getMessage());
            \Log::error('Erreur upload générale: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    /**
     * Supprimer un fichier de la liste d'upload
     */
    public function removeUploadFile($index)
    {
        if (isset($this->uploadFiles[$index])) {
            unset($this->uploadFiles[$index]);
            $this->uploadFiles = array_values($this->uploadFiles);
        }
    }

    /**
     * Créer un média à partir d'un fichier
     */
    private function createMediaFromFile($file)
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . rand(1000, 9999) . '.' . $extension;
        
        // Créer le dossier s'il n'existe pas
        $uploadPath = 'media/images';
        if (!Storage::disk('public')->exists($uploadPath)) {
            Storage::disk('public')->makeDirectory($uploadPath);
        }
        
        // Stocker le fichier original
        $path = $file->storeAs($uploadPath, $filename, 'public');
        
        // Créer une miniature
        $thumbnailPath = $this->createThumbnail($file, $filename);
        
        // Créer l'enregistrement en base
        $media = Media::create([
            'filename' => $filename,
            'original_name' => $originalName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'path' => 'storage/' . $path,
            'thumbnail_path' => $thumbnailPath,
            'type' => 'images',
        ]);

        // Créer les traductions par défaut
        foreach (['fr', 'en', 'ar'] as $locale) {
            $media->translations()->create([
                'locale' => $locale,
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'alt_text' => pathinfo($originalName, PATHINFO_FILENAME),
                'description' => null,
            ]);
        }

        return $media;
    }

    /**
     * Créer une miniature
     */
    private function createThumbnail($file, $filename)
    {
        try {
            // Vérifier si c'est une image
            if (!str_starts_with($file->getMimeType(), 'image/')) {
                return null;
            }
            
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            
            // Redimensionner en conservant les proportions
            $image->scale(300, 300);
            
            $thumbnailName = 'thumb_' . $filename;
            $thumbnailDir = storage_path('app/public/media/images');
            
            // Créer le dossier s'il n'existe pas
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0755, true);
            }
            
            $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
            $image->save($thumbnailPath);
            
            return 'storage/media/images/' . $thumbnailName;
        } catch (\Exception $e) {
            \Log::error('Erreur création miniature: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Changer le tri
     */
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    /**
     * Construire la requête des médias
     */
    private function getMediaQuery()
    {
        $query = Media::query()
            ->with(['translations' => function($q) {
                $q->where('locale', app()->getLocale())
                  ->orWhere('locale', config('app.fallback_locale', 'fr'));
            }]);

        // Filtrer par type
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        // Recherche
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('original_name', 'like', '%' . $this->search . '%')
                  ->orWhere('filename', 'like', '%' . $this->search . '%')
                  ->orWhereHas('translations', function($tq) {
                      $tq->where('title', 'like', '%' . $this->search . '%')
                         ->orWhere('description', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Tri
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query;
    }

    /**
     * Formater la taille des fichiers
     */
    public function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 1) . ' ' . $units[$unit];
    }

    /**
     * Vérifier la configuration
     */
    public function checkConfiguration()
    {
        $checks = [
            'storage_linked' => is_link(public_path('storage')),
            'upload_dir_writable' => is_writable(storage_path('app/public')),
            'media_dir_exists' => Storage::disk('public')->exists('media'),
            'media_images_dir_exists' => Storage::disk('public')->exists('media/images'),
        ];
        
        \Log::info('Configuration check:', $checks);
        return $checks;
    }

    /**
     * Test d'upload simple
     */
    public function testUpload()
    {
        try {
            $this->checkConfiguration();
            
            // Créer les dossiers nécessaires
            if (!Storage::disk('public')->exists('media/images')) {
                Storage::disk('public')->makeDirectory('media/images');
            }
            
            // Tester l'état des fichiers uploadés
            $uploadInfo = [
                'uploadFiles_count' => is_array($this->uploadFiles) ? count($this->uploadFiles) : 0,
                'uploadFiles_type' => gettype($this->uploadFiles),
                'uploadFiles_empty' => empty($this->uploadFiles),
            ];
            
            \Log::info('Upload files state:', $uploadInfo);
            
            session()->flash('success', 'Test de configuration réussi. Files: ' . json_encode($uploadInfo));
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur de configuration: ' . $e->getMessage());
            \Log::error('Erreur test upload: ' . $e->getMessage());
        }
    }

    /**
     * Debug de l'état des fichiers
     */
    public function debugUploadFiles()
    {
        $debug = [
            'uploadFiles' => $this->uploadFiles,
            'count' => is_array($this->uploadFiles) ? count($this->uploadFiles) : 0,
            'type' => gettype($this->uploadFiles),
            'empty' => empty($this->uploadFiles),
        ];
        
        \Log::info('Debug upload files:', $debug);
        session()->flash('info', 'Debug info logged. Count: ' . (is_array($this->uploadFiles) ? count($this->uploadFiles) : 0));
    }

    /**
     * Render du composant
     */
    public function render()
    {
        $media = $this->getMediaQuery()->paginate($this->perPage);
        
        $stats = [
            'total' => Media::count(),
            'images' => Media::where('type', 'images')->count(),
            'documents' => Media::where('type', 'documents')->count(),
            'videos' => Media::where('type', 'videos')->count(),
        ];

        return view('livewire.admin.media-selector-modal', [
            'media' => $media,
            'stats' => $stats,
        ]);
    }
}