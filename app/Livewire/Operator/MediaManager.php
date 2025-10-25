<?php

namespace App\Livewire\Operator;

use App\Livewire\Admin\MediaManager as AdminMediaManager;

class MediaManager extends AdminMediaManager
{
    /**
     * Désactiver la suppression pour les opérateurs
     */
    public function confirmDelete($id)
    {
        session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer des médias.');
    }

    /**
     * Désactiver la suppression pour les opérateurs
     */
    public function deleteMedia()
    {
        session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer des médias.');
    }

    /**
     * Désactiver la suppression multiple pour les opérateurs
     */
    public function deleteSelected()
    {
        session()->flash('error', 'Vous n\'avez pas les permissions pour supprimer des médias.');
    }

    /**
     * Rendu du composant avec la vue de l'opérateur
     */
    public function render()
    {
        $query = \App\Models\Media::with(['translations' => function ($query) {
            $query->where('locale', $this->currentLocale)
                ->orWhere('locale', config('app.fallback_locale'));
        }]);

        // Filtrer par recherche
        if ($this->search) {
            $query->whereHas('translations', function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })->orWhere('filename', 'like', '%'.$this->search.'%');
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

        return view('livewire.operator.media-manager', [
            'media' => $media,
        ]);
    }
}
