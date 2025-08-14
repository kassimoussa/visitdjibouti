<?php

namespace App\Livewire\Admin\Example;

use App\Models\Media;
use Livewire\Component;
use Livewire\Attributes\On;

class MediaIntegration extends Component
{
    public $selectedMediaIds = [];
    public $selectedMediaData = [];
    public $articleTitle = '';
    public $articleContent = '';
    public $featuredImageId = null;

    /**
     * Ouvrir le sélecteur pour l'image à la une
     */
    public function selectFeaturedImage()
    {
        $this->dispatch('open-universal-media-selector', [
            'title' => 'Sélectionner une image à la une',
            'mode' => 'single',
            'allowedTypes' => ['image'],
            'callback' => 'featured-image-selected',
            'selected' => $this->featuredImageId ? [$this->featuredImageId] : []
        ]);
    }

    /**
     * Ouvrir le sélecteur pour la galerie
     */
    public function selectGalleryImages()
    {
        $this->dispatch('open-universal-media-selector', [
            'title' => 'Sélectionner des images pour la galerie',
            'mode' => 'multiple',
            'maxFiles' => 8,
            'allowedTypes' => ['image', 'video'],
            'callback' => 'gallery-images-selected',
            'selected' => $this->selectedMediaIds
        ]);
    }

    /**
     * Gérer la sélection de l'image à la une
     */
    #[On('featured-image-selected')]
    public function handleFeaturedImageSelection($data)
    {
        if (!empty($data['media'])) {
            $this->featuredImageId = $data['media'][0]['id'];
        } else {
            $this->featuredImageId = null;
        }
    }

    /**
     * Gérer la sélection de la galerie
     */
    #[On('gallery-images-selected')]
    public function handleGallerySelection($data)
    {
        $this->selectedMediaIds = array_column($data['media'], 'id');
        $this->selectedMediaData = $data['media'];
    }

    /**
     * Supprimer l'image à la une
     */
    public function removeFeaturedImage()
    {
        $this->featuredImageId = null;
    }

    /**
     * Supprimer une image de la galerie
     */
    public function removeFromGallery($mediaId)
    {
        $this->selectedMediaIds = array_values(array_filter($this->selectedMediaIds, function($id) use ($mediaId) {
            return $id != $mediaId;
        }));
        
        $this->selectedMediaData = array_values(array_filter($this->selectedMediaData, function($media) use ($mediaId) {
            return $media['id'] != $mediaId;
        }));
    }

    /**
     * Vider la galerie
     */
    public function clearGallery()
    {
        $this->selectedMediaIds = [];
        $this->selectedMediaData = [];
    }

    /**
     * Sauvegarder l'article (exemple)
     */
    public function saveArticle()
    {
        $this->validate([
            'articleTitle' => 'required|string|max:255',
            'articleContent' => 'required|string',
        ]);

        // Ici vous pourriez sauvegarder en base de données
        session()->flash('success', 'Article sauvegardé avec succès !');
        
        // Réinitialiser pour la démo
        $this->reset(['articleTitle', 'articleContent', 'featuredImageId', 'selectedMediaIds', 'selectedMediaData']);
    }

    /**
     * Obtenir l'image à la une
     */
    public function getFeaturedImageProperty()
    {
        return $this->featuredImageId ? Media::find($this->featuredImageId) : null;
    }

    public function render()
    {
        return view('livewire.admin.example.media-integration');
    }
}