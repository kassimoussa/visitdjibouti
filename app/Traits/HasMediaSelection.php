<?php

namespace App\Traits;

use App\Models\Media;
use Livewire\Attributes\On;

trait HasMediaSelection
{
    public $showMediaSelector = false;
    public $mediaSelectorMode = 'single';
    public $mediaSelectorTarget = null;

    /**
     * Ouvrir le sélecteur de médias
     */
    public function openMediaSelector($mode = 'single', $target = null, $preselected = [])
    {
        $this->mediaSelectorMode = $mode;
        $this->mediaSelectorTarget = $target;
        $this->dispatch('open-media-selector', $mode, $preselected);
    }

    /**
     * Gérer la sélection de médias depuis le modal
     */
    #[On('media-selected')]
    public function handleMediaSelection($selectedIds)
    {
        switch ($this->mediaSelectorTarget) {
            case 'featured':
                $this->handleFeaturedImageSelection($selectedIds);
                break;
            case 'gallery':
                $this->handleGallerySelection($selectedIds);
                break;
            default:
                $this->handleDefaultMediaSelection($selectedIds);
                break;
        }
    }

    /**
     * Gérer la sélection d'image principale
     */
    protected function handleFeaturedImageSelection($selectedIds)
    {
        if (property_exists($this, 'featuredImageId')) {
            $this->featuredImageId = !empty($selectedIds) ? $selectedIds[0] : null;
        }
    }

    /**
     * Gérer la sélection de galerie
     */
    protected function handleGallerySelection($selectedIds)
    {
        if (property_exists($this, 'selectedMedia')) {
            $this->selectedMedia = $selectedIds;
        }
    }

    /**
     * Gérer la sélection par défaut
     */
    protected function handleDefaultMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->handleFeaturedImageSelection($selectedIds);
        } else {
            $this->handleGallerySelection($selectedIds);
        }
    }

    /**
     * Obtenir les informations d'un média
     */
    public function getMediaInfo($mediaId)
    {
        return Media::with('translations')->find($mediaId);
    }

    /**
     * Formater la taille d'un fichier
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
     * Obtenir l'URL d'une miniature
     */
    public function getThumbnailUrl($media)
    {
        if (!$media) return null;
        return asset($media->thumbnail_path ?? $media->path);
    }

    /**
     * Obtenir l'URL complète d'un média
     */
    public function getMediaUrl($media)
    {
        if (!$media) return null;
        return asset($media->path);
    }
}