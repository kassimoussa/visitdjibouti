<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use Livewire\Component;

class MediaEdit extends Component
{
    // Propriétés pour le média
    public $mediaId;

    public $media;

    public $title;

    public $altText;

    public $description;

    // Règles de validation
    protected $rules = [
        'title' => 'nullable|string|max:255',
        'altText' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ];

    // Ajouter un hook de chargement initial
    public function mount($id)
    {
        $this->mediaId = $id;
        $this->media = Media::findOrFail($id);
        $this->title = $this->media->title;
        $this->altText = $this->media->alt_text;
        $this->description = $this->media->description;
    }

    /**
     * Méthode pour mettre à jour les détails du média
     */
    public function updateMediaDetails()
    {
        $this->validate();

        $this->media->update([
            'title' => $this->title,
            'alt_text' => $this->altText,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Informations mises à jour avec succès !');

        // Rediriger vers la liste des médias
        return redirect()->route('admin.media.index');
    }

    /**
     * Méthode pour le rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.media.media-edit');
    }
}
