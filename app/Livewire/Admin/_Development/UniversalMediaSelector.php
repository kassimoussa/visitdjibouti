<?php

namespace App\Livewire\Admin\_Development;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class UniversalMediaSelector extends Component
{
    use WithPagination, WithFileUploads;

    // ===== CONFIGURATION DU MODAL =====
    public $isOpen = false;
    public $modalTitle = 'Sélectionner des médias';
    public $selectionMode = 'single'; // 'single', 'multiple'
    public $maxFiles = 50;
    public $allowedTypes = ['image', 'video', 'audio', 'document'];
    public $allowedMimeTypes = [];
    public $maxFileSize = 10240; // KB
    public $callbackEvent = 'media-selected';
    
    // ===== ÉTAT DE SÉLECTION =====
    public $selectedMedia = [];
    public $lastSelectedId = null;
    public $selectionCount = 0;
    
    // ===== ONGLETS ET VUES =====
    public $currentTab = 'library'; // 'library', 'upload', 'url'
    public $viewMode = 'grid'; // 'grid', 'list'
    public $detailsPanel = false;
    public $selectedForDetails = null;
    
    // ===== FILTRES ET RECHERCHE =====
    public $search = '';
    public $dateFilter = 'all'; // 'all', 'today', 'week', 'month', 'year'
    public $typeFilter = 'all'; // 'all', 'image', 'video', 'audio', 'document'
    public $authorFilter = 'all';
    public $sortBy = 'date'; // 'date', 'title', 'size', 'random'
    public $sortDirection = 'desc';
    public $perPage = 40;
    
    // ===== UPLOAD =====
    public $uploadFiles = [];
    public $uploadProgress = [];
    public $isUploading = false;
    public $uploadErrors = [];
    public $uploadSuccess = [];
    public $dragActive = false;
    
    // ===== UPLOAD PAR URL =====
    public $uploadUrl = '';
    public $urlUploading = false;
    
    // ===== ÉDITION INLINE =====
    public $editingMedia = null;
    public $editingField = '';
    public $editingValue = '';
    
    // ===== BULK ACTIONS =====
    public $bulkAction = '';
    public $bulkActionConfirm = false;
    
    // ===== MESSAGES =====
    public $successMessage = '';
    public $errorMessage = '';
    public $infoMessage = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'date'],
        'currentTab' => ['except' => 'library']
    ];

    protected $listeners = [
        'refresh-media' => '$refresh',
        'media-uploaded' => 'handleUploadComplete'
    ];

    /**
     * Règles de validation pour l'upload
     */
    public function getRules()
    {
        \Log::info('getRules called');
        \Log::info('allowedMimeTypes: ' . json_encode($this->allowedMimeTypes));
        \Log::info('maxFileSize: ' . $this->maxFileSize);
        
        $mimeTypes = !empty($this->allowedMimeTypes) 
            ? implode(',', $this->allowedMimeTypes)
            : 'jpeg,png,jpg,gif,webp,mp4,mov,avi,mp3,wav,pdf,doc,docx';

        $rules = [
            'uploadFiles.*' => [
                'required',
                'file',
                'max:' . $this->maxFileSize,
                'mimes:' . $mimeTypes
            ]
        ];
        
        \Log::info('Validation rules: ' . json_encode($rules));
        return $rules;
    }

    /**
     * Ouvrir le modal
     */
    #[On('open-universal-media-selector')]
    public function openModal($config = [])
    {
        // Configuration du modal
        $this->modalTitle = $config['title'] ?? 'Sélectionner des médias';
        $this->selectionMode = $config['mode'] ?? 'single';
        $this->maxFiles = $config['maxFiles'] ?? ($this->selectionMode === 'single' ? 1 : 50);
        $this->allowedTypes = $config['allowedTypes'] ?? ['image'];
        $this->allowedMimeTypes = $config['allowedMimeTypes'] ?? [];
        $this->callbackEvent = $config['callback'] ?? 'media-selected';
        
        // Pré-sélection
        $this->selectedMedia = $config['selected'] ?? [];
        $this->selectionCount = count($this->selectedMedia);
        
        // Réinitialiser les filtres si demandé
        if ($config['resetFilters'] ?? true) {
            $this->resetFilters();
        }
        
        // Ouvrir le modal
        $this->isOpen = true;
        $this->currentTab = 'library';
        $this->resetPage();
        
        $this->dispatch('modal-opened');
    }

    /**
     * Fermer le modal
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset([
            'selectedMedia', 'selectionCount', 'lastSelectedId',
            'uploadFiles', 'uploadProgress', 'isUploading', 'uploadErrors', 'uploadSuccess',
            'uploadUrl', 'urlUploading', 'editingMedia', 'editingField', 'editingValue',
            'successMessage', 'errorMessage', 'infoMessage', 'detailsPanel', 'selectedForDetails'
        ]);
        
        $this->dispatch('modal-closed');
    }

    /**
     * Réinitialiser les filtres
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->dateFilter = 'all';
        $this->typeFilter = 'all';
        $this->authorFilter = 'all';
        $this->sortBy = 'date';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Changer d'onglet
     */
    public function switchTab($tab)
    {
        $this->currentTab = $tab;
        $this->resetValidation();
        $this->clearMessages();
    }

    /**
     * Basculer le mode de vue
     */
    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    /**
     * Sélectionner/désélectionner un média
     */
    public function toggleMedia($mediaId, $shiftKey = false)
    {
        if ($this->selectionMode === 'single') {
            $this->selectedMedia = in_array($mediaId, $this->selectedMedia) ? [] : [$mediaId];
        } else {
            // Sélection multiple avec Shift
            if ($shiftKey && $this->lastSelectedId && $this->lastSelectedId !== $mediaId) {
                $this->selectRange($this->lastSelectedId, $mediaId);
            } else {
                if (in_array($mediaId, $this->selectedMedia)) {
                    $this->selectedMedia = array_values(array_diff($this->selectedMedia, [$mediaId]));
                } else {
                    if (count($this->selectedMedia) < $this->maxFiles) {
                        $this->selectedMedia[] = $mediaId;
                    } else {
                        $this->showError("Maximum {$this->maxFiles} fichiers autorisés");
                        return;
                    }
                }
            }
        }
        
        $this->lastSelectedId = $mediaId;
        $this->selectionCount = count($this->selectedMedia);
    }

    /**
     * Sélectionner une plage de médias (avec Shift)
     */
    private function selectRange($startId, $endId)
    {
        $media = $this->getFilteredMediaQuery()->pluck('id')->toArray();
        $startIndex = array_search($startId, $media);
        $endIndex = array_search($endId, $media);
        
        if ($startIndex !== false && $endIndex !== false) {
            $rangeStart = min($startIndex, $endIndex);
            $rangeEnd = max($startIndex, $endIndex);
            
            for ($i = $rangeStart; $i <= $rangeEnd; $i++) {
                if (!in_array($media[$i], $this->selectedMedia) && count($this->selectedMedia) < $this->maxFiles) {
                    $this->selectedMedia[] = $media[$i];
                }
            }
        }
    }

    /**
     * Sélectionner tout (page actuelle)
     */
    public function selectAll()
    {
        if ($this->selectionMode === 'single') return;
        
        $pageMedia = $this->getFilteredMediaQuery()
            ->skip(($this->getPage() - 1) * $this->perPage)
            ->take($this->perPage)
            ->pluck('id')
            ->toArray();
        
        foreach ($pageMedia as $mediaId) {
            if (!in_array($mediaId, $this->selectedMedia) && count($this->selectedMedia) < $this->maxFiles) {
                $this->selectedMedia[] = $mediaId;
            }
        }
        
        $this->selectionCount = count($this->selectedMedia);
    }

    /**
     * Désélectionner tout
     */
    public function deselectAll()
    {
        $this->selectedMedia = [];
        $this->selectionCount = 0;
        $this->lastSelectedId = null;
    }

    /**
     * Confirmer la sélection
     */
    public function confirmSelection()
    {
        if (empty($this->selectedMedia)) {
            $this->showError('Veuillez sélectionner au moins un média');
            return;
        }

        $selectedMediaData = Media::whereIn('id', $this->selectedMedia)
            ->with('translations')
            ->get()
            ->map(function ($media) {
                return [
                    'id' => $media->id,
                    'filename' => $media->filename,
                    'original_name' => $media->original_name,
                    'path' => $media->path,
                    'thumbnail_path' => $media->thumbnail_path,
                    'url' => $media->url,
                    'thumbnail_url' => $media->thumbnail_url,
                    'type' => $media->type,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'title' => $media->title,
                    'alt_text' => $media->alt_text,
                    'description' => $media->description,
                ];
            });

        $this->dispatch($this->callbackEvent, [
            'media' => $selectedMediaData->toArray(),
            'count' => $this->selectionCount,
            'mode' => $this->selectionMode
        ]);

        $this->closeModal();
    }

    /**
     * Méthode de test pour vérifier si Livewire fonctionne
     */
    public function testUpload()
    {
        \Log::info('TEST: testUpload method called');
        
        // Test de sélection d'un média fictif
        $this->selectedMedia = [999]; // ID fictif
        $this->selectionCount = 1;
        $this->currentTab = 'library';
        
        $this->showSuccess('Test réussi ! Livewire fonctionne. Média fictif sélectionné.');
    }

    /**
     * Upload de fichiers
     */
    public function uploadFiles()
    {
        \Log::info('UniversalMediaSelector: uploadFiles() called');
        \Log::info('Upload files count: ' . count($this->uploadFiles ?? []));
        
        // Test immédiat
        if (empty($this->uploadFiles)) {
            \Log::warning('No files to upload');
            $this->showError('Aucun fichier sélectionné pour l\'upload');
            return;
        }
        
        try {
            $this->validate();
        } catch (\Exception $e) {
            \Log::error('Validation failed: ' . $e->getMessage());
            $this->showError('Erreur de validation : ' . $e->getMessage());
            return;
        }

        $this->isUploading = true;
        $this->uploadErrors = [];
        $this->uploadSuccess = [];
        $uploadedIds = [];

        foreach ($this->uploadFiles as $index => $file) {
            try {
                \Log::info('Processing file: ' . $file->getClientOriginalName());
                $this->uploadProgress[$index] = 0;
                
                // Créer le média
                $media = $this->createMediaFromFile($file);
                
                if ($media) {
                    \Log::info('Media created successfully with ID: ' . $media->id);
                    $uploadedIds[] = $media->id;
                    $this->uploadSuccess[] = [
                        'name' => $file->getClientOriginalName(),
                        'id' => $media->id
                    ];
                    $this->uploadProgress[$index] = 100;
                } else {
                    \Log::error('Failed to create media for file: ' . $file->getClientOriginalName());
                    $this->uploadErrors[] = [
                        'name' => $file->getClientOriginalName(),
                        'error' => 'Échec de la création du média'
                    ];
                }
                
            } catch (\Exception $e) {
                \Log::error('Upload error for file ' . $file->getClientOriginalName() . ': ' . $e->getMessage());
                \Log::error('Stack trace: ' . $e->getTraceAsString());
                $this->uploadErrors[] = [
                    'name' => $file->getClientOriginalName(),
                    'error' => $e->getMessage()
                ];
            }
        }

        $this->isUploading = false;

        // Auto-sélectionner les fichiers uploadés
        if (!empty($uploadedIds)) {
            if ($this->selectionMode === 'single') {
                $this->selectedMedia = [end($uploadedIds)];
            } else {
                $this->selectedMedia = array_unique(array_merge($this->selectedMedia, $uploadedIds));
            }
            $this->selectionCount = count($this->selectedMedia);
            
            // Passer à l'onglet bibliothèque
            $this->currentTab = 'library';
            $this->resetPage();
        }

        // Messages de résultat
        $successCount = count($this->uploadSuccess);
        $errorCount = count($this->uploadErrors);

        if ($successCount > 0) {
            $this->showSuccess("{$successCount} fichier(s) uploadé(s) avec succès");
        }
        
        if ($errorCount > 0) {
            $this->showError("{$errorCount} erreur(s) d'upload");
        }

        // Réinitialiser
        $this->uploadFiles = [];
        $this->uploadProgress = [];
        
        $this->dispatch('media-uploaded');
    }

    /**
     * Upload par URL
     */
    public function uploadFromUrl()
    {
        $this->validate([
            'uploadUrl' => 'required|url'
        ]);

        $this->urlUploading = true;

        try {
            // Télécharger le fichier depuis l'URL
            $content = file_get_contents($this->uploadUrl);
            
            if ($content === false) {
                throw new \Exception('Impossible de télécharger le fichier');
            }

            // Déterminer le nom et l'extension du fichier
            $urlParts = parse_url($this->uploadUrl);
            $filename = basename($urlParts['path']) ?: 'download';
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            
            if (empty($extension)) {
                // Essayer de deviner l'extension depuis le Content-Type
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($content);
                $extension = $this->getExtensionFromMimeType($mimeType);
                $filename .= '.' . $extension;
            }

            // Créer un fichier temporaire
            $tempFile = tmpfile();
            fwrite($tempFile, $content);
            $tempPath = stream_get_meta_data($tempFile)['uri'];

            // Créer le média
            $media = $this->createMediaFromPath($tempPath, $filename, filesize($tempPath));
            
            fclose($tempFile);

            if ($media) {
                // Auto-sélectionner
                if ($this->selectionMode === 'single') {
                    $this->selectedMedia = [$media->id];
                } else {
                    $this->selectedMedia[] = $media->id;
                }
                $this->selectionCount = count($this->selectedMedia);
                
                $this->showSuccess('Fichier importé avec succès depuis l\'URL');
                $this->currentTab = 'library';
                $this->uploadUrl = '';
            }

        } catch (\Exception $e) {
            $this->showError('Erreur lors de l\'import : ' . $e->getMessage());
        }

        $this->urlUploading = false;
    }

    /**
     * Créer un média depuis un fichier uploadé
     */
    private function createMediaFromFile($file)
    {
        \Log::info('createMediaFromFile called');
        
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        \Log::info("File details: name={$originalName}, ext={$extension}, mime={$mimeType}, size={$size}");
        
        return $this->createMediaFromPath($file->getPathname(), $originalName, $size, $extension, $mimeType);
    }

    /**
     * Créer un média depuis un chemin de fichier
     */
    private function createMediaFromPath($filepath, $originalName, $size, $extension = null, $mimeType = null)
    {
        \Log::info('createMediaFromPath called');
        \Log::info("Parameters: filepath={$filepath}, originalName={$originalName}, size={$size}");
        
        if (!$extension) {
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        }
        
        if (!$mimeType) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($filepath);
        }

        \Log::info("Final details: extension={$extension}, mimeType={$mimeType}");

        // Générer un nom de fichier unique
        $filename = time() . '_' . Str::random(10) . '.' . $extension;
        
        // Déterminer le type de média
        $type = $this->getMediaType($mimeType);
        \Log::info("Media type determined: {$type}");
        
        // Créer le dossier de destination
        $uploadPath = 'media/' . $type . 's';
        \Log::info("Upload path: {$uploadPath}");
        
        if (!Storage::disk('public')->exists($uploadPath)) {
            \Log::info("Creating directory: {$uploadPath}");
            Storage::disk('public')->makeDirectory($uploadPath, 0755, true);
        }
        
        // Copier le fichier
        $storagePath = $uploadPath . '/' . $filename;
        \Log::info("Storing file to: {$storagePath}");
        
        Storage::disk('public')->put($storagePath, file_get_contents($filepath));
        \Log::info("File stored successfully");
        
        // Créer une miniature pour les images
        $thumbnailPath = null;
        $dimensions = null;
        
        if ($type === 'image') {
            try {
                \Log::info("Creating thumbnail for image");
                $dimensions = $this->getImageDimensions($filepath);
                $thumbnailPath = $this->createThumbnail($filepath, $filename, $type);
                \Log::info("Thumbnail created: {$thumbnailPath}");
            } catch (\Exception $e) {
                \Log::warning('Thumbnail creation failed: ' . $e->getMessage());
            }
        }
        
        // Créer l'enregistrement en base
        \Log::info("Creating Media record in database");
        
        $mediaData = [
            'filename' => $filename,
            'original_name' => $originalName,
            'path' => 'storage/' . $storagePath,
            'thumbnail_path' => $thumbnailPath,
            'type' => $type,
            'mime_type' => $mimeType,
            'size' => $size,
            'dimensions' => $dimensions,
            'is_optimized' => $type === 'image',
        ];
        
        \Log::info("Media data: " . json_encode($mediaData));
        
        $media = Media::create($mediaData);
        \Log::info("Media created with ID: " . $media->id);

        // Créer les traductions par défaut
        \Log::info("Creating translations");
        foreach (['fr', 'en', 'ar'] as $locale) {
            $media->translations()->create([
                'locale' => $locale,
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'alt_text' => pathinfo($originalName, PATHINFO_FILENAME),
                'description' => null,
            ]);
        }
        \Log::info("Translations created");

        return $media;
    }

    /**
     * Créer une miniature
     */
    private function createThumbnail($filepath, $filename, $type)
    {
        if ($type !== 'image') return null;

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($filepath);
            
            // Différentes tailles de miniatures
            $sizes = [
                'thumb' => [300, 300],
                'medium' => [600, 400],
                'small' => [150, 150]
            ];
            
            foreach ($sizes as $sizeName => $dimensions) {
                $resized = clone $image;
                $resized->scale($dimensions[0], $dimensions[1]);
                
                $thumbnailName = $sizeName . '_' . $filename;
                $thumbnailDir = storage_path('app/public/media/images');
                
                if (!file_exists($thumbnailDir)) {
                    mkdir($thumbnailDir, 0755, true);
                }
                
                $thumbnailPath = $thumbnailDir . '/' . $thumbnailName;
                $resized->save($thumbnailPath);
            }
            
            return 'storage/media/images/thumb_' . $filename;
            
        } catch (\Exception $e) {
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir les dimensions d'une image
     */
    private function getImageDimensions($filepath)
    {
        try {
            $imageInfo = getimagesize($filepath);
            return $imageInfo ? [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1]
            ] : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Déterminer le type de média
     */
    private function getMediaType($mimeType)
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } else {
            return 'document';
        }
    }

    /**
     * Obtenir l'extension depuis le MIME type
     */
    private function getExtensionFromMimeType($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/quicktime' => 'mov',
            'audio/mpeg' => 'mp3',
            'audio/wav' => 'wav',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        ];
        
        return $extensions[$mimeType] ?? 'bin';
    }

    /**
     * Supprimer un média
     */
    public function deleteMedia($mediaId)
    {
        try {
            $media = Media::findOrFail($mediaId);
            
            // Supprimer les fichiers physiques
            if (Storage::disk('public')->exists(str_replace('storage/', '', $media->path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $media->path));
            }
            
            // Supprimer les miniatures
            if ($media->thumbnail_path && Storage::disk('public')->exists(str_replace('storage/', '', $media->thumbnail_path))) {
                Storage::disk('public')->delete(str_replace('storage/', '', $media->thumbnail_path));
            }
            
            // Supprimer de la base de données
            $media->delete();
            
            // Retirer de la sélection
            $this->selectedMedia = array_values(array_diff($this->selectedMedia, [$mediaId]));
            $this->selectionCount = count($this->selectedMedia);
            
            $this->showSuccess('Média supprimé avec succès');
            
        } catch (\Exception $e) {
            $this->showError('Erreur lors de la suppression : ' . $e->getMessage());
        }
    }

    /**
     * Édition inline d'un média
     */
    public function startEditing($mediaId, $field)
    {
        $media = Media::find($mediaId);
        if (!$media) return;
        
        $this->editingMedia = $mediaId;
        $this->editingField = $field;
        
        // Obtenir la valeur actuelle
        switch ($field) {
            case 'title':
                $this->editingValue = $media->title ?? $media->original_name;
                break;
            case 'alt_text':
                $this->editingValue = $media->alt_text ?? '';
                break;
            case 'description':
                $this->editingValue = $media->description ?? '';
                break;
        }
    }

    /**
     * Sauvegarder l'édition inline
     */
    public function saveEditing()
    {
        if (!$this->editingMedia || !$this->editingField) return;
        
        try {
            $media = Media::find($this->editingMedia);
            if (!$media) return;
            
            // Mettre à jour la traduction française par défaut
            $translation = $media->translations()->where('locale', 'fr')->first();
            if ($translation) {
                $translation->update([
                    $this->editingField => $this->editingValue
                ]);
            }
            
            $this->showSuccess('Modifications sauvegardées');
            $this->cancelEditing();
            
        } catch (\Exception $e) {
            $this->showError('Erreur lors de la sauvegarde : ' . $e->getMessage());
        }
    }

    /**
     * Annuler l'édition inline
     */
    public function cancelEditing()
    {
        $this->editingMedia = null;
        $this->editingField = '';
        $this->editingValue = '';
    }

    /**
     * Afficher/masquer le panneau de détails
     */
    public function toggleDetailsPanel($mediaId = null)
    {
        if ($this->selectedForDetails === $mediaId) {
            $this->detailsPanel = false;
            $this->selectedForDetails = null;
        } else {
            $this->detailsPanel = true;
            $this->selectedForDetails = $mediaId;
        }
    }

    /**
     * Construire la requête des médias filtrés
     */
    private function getFilteredMediaQuery()
    {
        $query = Media::query()->with(['translations' => function($q) {
            $q->where('locale', app()->getLocale())
              ->orWhere('locale', config('app.fallback_locale', 'fr'));
        }]);

        // Filtrer par types autorisés
        if (!empty($this->allowedTypes) && !in_array('all', $this->allowedTypes)) {
            $query->whereIn('type', $this->allowedTypes);
        }

        // Filtre par type sélectionné
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
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhere('alt_text', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtre par date
        if ($this->dateFilter !== 'all') {
            $date = match($this->dateFilter) {
                'today' => now()->startOfDay(),
                'week' => now()->startOfWeek(),
                'month' => now()->startOfMonth(),
                'year' => now()->startOfYear(),
                default => null
            };
            
            if ($date) {
                $query->where('created_at', '>=', $date);
            }
        }

        // Tri
        switch ($this->sortBy) {
            case 'title':
                $query->orderBy('original_name', $this->sortDirection);
                break;
            case 'size':
                $query->orderBy('size', $this->sortDirection);
                break;
            case 'random':
                $query->inRandomOrder();
                break;
            default:
                $query->orderBy('created_at', $this->sortDirection);
        }

        return $query;
    }

    /**
     * Obtenir les statistiques des médias
     */
    public function getMediaStats()
    {
        $baseQuery = Media::query();
        
        // Appliquer les filtres de types autorisés
        if (!empty($this->allowedTypes) && !in_array('all', $this->allowedTypes)) {
            $baseQuery->whereIn('type', $this->allowedTypes);
        }

        return [
            'total' => (clone $baseQuery)->count(),
            'image' => (clone $baseQuery)->where('type', 'image')->count(),
            'video' => (clone $baseQuery)->where('type', 'video')->count(),
            'audio' => (clone $baseQuery)->where('type', 'audio')->count(),
            'document' => (clone $baseQuery)->where('type', 'document')->count(),
            'selected' => $this->selectionCount,
            'maxFiles' => $this->maxFiles,
        ];
    }

    /**
     * Formater la taille de fichier
     */
    public function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;
        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }
        return round($bytes, 1) . ' ' . $units[$unit];
    }

    /**
     * Afficher un message de succès
     */
    private function showSuccess($message)
    {
        $this->successMessage = $message;
        $this->dispatch('show-message', ['type' => 'success', 'message' => $message]);
    }

    /**
     * Afficher un message d'erreur
     */
    private function showError($message)
    {
        $this->errorMessage = $message;
        $this->dispatch('show-message', ['type' => 'error', 'message' => $message]);
    }

    /**
     * Afficher un message d'information
     */
    private function showInfo($message)
    {
        $this->infoMessage = $message;
        $this->dispatch('show-message', ['type' => 'info', 'message' => $message]);
    }

    /**
     * Effacer les messages
     */
    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
        $this->infoMessage = '';
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        try {
            $media = $this->getFilteredMediaQuery()->paginate($this->perPage);
            $stats = $this->getMediaStats();
            
            // Debug: Log des informations pour vérifier
            if ($this->isOpen && $media->count() === 0) {
                // Si aucun média, créer des données de test
                $media = $this->createTestMediaData();
            }
            
            return view('livewire.admin._development.universal-media-selector', [
                'media' => $media,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser des données de test
            $media = $this->createTestMediaData();
            $stats = ['total' => 15, 'images' => 9, 'videos' => 3, 'documents' => 3];
            
            return view('livewire.admin._development.universal-media-selector', [
                'media' => $media,
                'stats' => $stats,
            ]);
        }
    }

    /**
     * Gérer les fichiers droppés (méthode pour Alpine.js)
     */
    public function handleFilesDrop($files)
    {
        // Cette méthode sera appelée par Alpine.js
        // Pour l'instant, on simule juste un message
        $this->showSuccess('Fichiers détectés : ' . count($files));
    }

    /**
     * Gérer l'upload de fichiers
     */
    public function handleFileUpload($files)
    {
        // Cette méthode sera appelée par Alpine.js
        $this->showSuccess('Upload de ' . count($files) . ' fichier(s) en cours...');
    }

    /**
     * Créer des données de test pour le debug
     */
    private function createTestMediaData()
    {
        // Créer plus de données pour tester le scroll
        $colors = ['0073aa', '667eea', 'f093fb', '4facfe', '43e97b', 'fa709a', 'ff7675', '6c5ce7', 'a29bfe', '74b9ff'];
        $types = ['image', 'image', 'image', 'video', 'document', 'image', 'image', 'video', 'document', 'image'];
        
        $testDataArray = [];
        
        for ($i = 1; $i <= 15; $i++) {
            $color = $colors[($i - 1) % count($colors)];
            $type = $types[($i - 1) % count($types)];
            
            $testDataArray[] = (object) [
                'id' => $i,
                'type' => $type,
                'path' => "https://via.placeholder.com/300x200/{$color}/white?text={$type}+Test+{$i}",
                'thumbnail_path' => "https://via.placeholder.com/150x100/{$color}/white?text=Thumb+{$i}",
                'original_name' => "test-{$type}-{$i}." . ($type === 'image' ? 'jpg' : ($type === 'video' ? 'mp4' : 'pdf')),
                'title' => ucfirst($type) . " de test {$i}",
                'alt_text' => ucfirst($type) . " de test numéro {$i}",
                'size' => 1024000 + ($i * 100000),
                'file_size' => 1024000 + ($i * 100000),
                'mime_type' => $type === 'image' ? 'image/jpeg' : ($type === 'video' ? 'video/mp4' : 'application/pdf'),
                'dimensions' => $type !== 'document' ? ['width' => 300, 'height' => 200] : null,
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 10)),
                'description' => "Description du " . $type . " de test numéro {$i}",
                'url' => "https://via.placeholder.com/300x200/{$color}/white?text={$type}+Test+{$i}",
                'thumbnail_url' => "https://via.placeholder.com/150x100/{$color}/white?text=Thumb+{$i}",
            ];
        }
        
        $testData = collect($testDataArray);

        // Simuler la pagination
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $testData,
            15,
            $this->perPage,
            1,
            ['path' => request()->url()]
        );
    }
}