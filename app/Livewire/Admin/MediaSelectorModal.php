<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use Livewire\Component;
use Livewire\Attributes\On;

class MediaSelectorModal extends Component
{
    // Configuration du modal
    public $isOpen = false;
    public $selectionMode = 'single'; // 'single', 'multiple'
    public $selectedImages = [];
    public $preselectedImages = [];

    // Filtres et recherche
    public $search = '';
    public $typeFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';


    // Lazy Loading
    public $loadedItems = 24; // Nombre initial d'items à charger
    public $itemsPerLoad = 24; // Nombre d'items à charger à chaque scroll

    // Preview
    public $previewImage = null;
    public $showPreview = false;


    protected $listeners = ['openMediaSelector', 'closeMediaSelector'];

    /**
     * Réinitialiser le chargement lors du changement de recherche
     */
    public function updatingSearch()
    {
        $this->loadedItems = $this->itemsPerLoad;
    }

    public function updatingTypeFilter()
    {
        $this->loadedItems = $this->itemsPerLoad;
    }

    public function updatingSortBy()
    {
        $this->loadedItems = $this->itemsPerLoad;
    }

    /**
     * Charger plus de médias
     */
    public function loadMore()
    {
        $this->loadedItems += $this->itemsPerLoad;
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
        $this->loadedItems = $this->itemsPerLoad;
        $this->dispatch('modal-opened');
    }

    /**
     * Fermer le modal
     */
    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['search', 'typeFilter', 'selectedImages', 'showPreview', 'previewImage']);
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
            // Support des deux formats (pluriel et singulier)
            if ($this->typeFilter === 'images') {
                $query->where(function($q) {
                    $q->where('type', 'images')->orWhere('type', 'image');
                });
            } elseif ($this->typeFilter === 'documents') {
                $query->where(function($q) {
                    $q->where('type', 'documents')->orWhere('type', 'document');
                });
            } elseif ($this->typeFilter === 'videos') {
                $query->where(function($q) {
                    $q->where('type', 'videos')->orWhere('type', 'video');
                });
            } else {
                $query->where('type', $this->typeFilter);
            }
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

        // Tri
        switch ($this->sortBy) {
            case 'original_name':
                $query->orderBy('original_name', $this->sortDirection);
                break;
            case 'size':
                $query->orderBy('size', $this->sortDirection);
                break;
            default:
                $query->orderBy('created_at', $this->sortDirection);
        }

        return $query;
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
     * Render du composant
     */
    public function render()
    {
        $query = $this->getMediaQuery();
        $totalCount = $query->count();
        $media = $query->limit($this->loadedItems)->get();

        $stats = [
            'total' => Media::count(),
            'images' => Media::where('type', 'images')->count() + Media::where('type', 'image')->count(),
            'documents' => Media::where('type', 'documents')->count() + Media::where('type', 'document')->count(),
            'videos' => Media::where('type', 'videos')->count() + Media::where('type', 'video')->count(),
        ];

        return view('livewire.admin.media-selector-modal', [
            'media' => $media,
            'stats' => $stats,
            'totalCount' => $totalCount,
            'hasMore' => $this->loadedItems < $totalCount,
        ]);
    }
}