<?php

namespace App\Livewire\Admin\_Development;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class MediaList extends Component
{
    use WithPagination;
    
    // Bootstrap pagination
    protected $paginationTheme = 'bootstrap';
    
    // Propriétés pour la recherche et le filtrage
    public $search = '';
    public $typeFilter = '';
    public $dateFilter = '';
    public $viewMode = 'grid'; // 'grid' ou 'list'
    
    // Propriétés pour les actions en masse
    public $selectedItems = [];
    
    // Propriété pour la suppression
    public $mediaToDelete = null;
    
    /**
     * Méthode pour changer le mode d'affichage (grille/liste)
     */
    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
    }
    
    /**
     * Méthode pour supprimer un média
     */
    public function deleteMedia($id)
    {
        $media = Media::find($id);
        
        if (!$media) {
            $this->dispatch('show-toast', message: 'Média introuvable.', type: 'error');
            return;
        }
        
        // Supprimer le fichier
        Storage::delete(str_replace('storage/', 'public/', $media->path));
        
        // Supprimer la miniature si elle existe
        if ($media->thumbnail_path) {
            Storage::delete(str_replace('storage/', 'public/', $media->thumbnail_path));
        }
        
        // Supprimer l'enregistrement
        $media->delete();
        
        $this->dispatch('show-toast', message: 'Média supprimé avec succès !', type: 'success');
    }
    
    /**
     * Méthode pour supprimer plusieurs médias à la fois
     */
    public function deleteSelected()
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('show-toast', message: 'Aucun média sélectionné.', type: 'error');
            return;
        }
        
        foreach ($this->selectedItems as $id) {
            $this->deleteMedia($id);
        }
        
        $this->reset('selectedItems');
    }
    
    /**
     * Méthode pour ouvrir le modal de suppression d'un média
     */
    public function openDeleteModal($id)
    {
        $this->mediaToDelete = $id;
        $this->dispatch('openDeleteModal');
    }
    
    /**
     * Méthode pour ouvrir le modal de suppression en masse
     */
    public function openDeleteSelectedModal()
    {
        $this->dispatch('openDeleteSelectedModal');
    }
    
    /**
     * Méthode pour le rendu du composant
     */
    public function render()
    {
        $query = Media::query();
        
        // Filtrer par recherche
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('filename', 'like', '%' . $this->search . '%')
                  ->orWhere('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
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
        
        return view('livewire.admin._development.media-list', [
            'media' => $media,
        ]);
    }
}