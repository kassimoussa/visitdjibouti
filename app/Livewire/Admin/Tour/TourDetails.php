<?php

namespace App\Livewire\Admin\Tour;

use App\Models\Tour;
use Livewire\Component;

class TourDetails extends Component
{
    public $tour;

    public $currentLocale;

    /**
     * Montage du composant
     */
    public function mount($tourId)
    {
        // Charger le tour avec ses relations
        $this->tour = Tour::with([
            'tourOperator',
            'target',
            'media',
            'featuredImage',
            'translations',
            'createdBy',
            'approvedBy',
        ])
            ->findOrFail($tourId);

        // Initialiser la langue courante avec la langue de l'application
        $this->currentLocale = app()->getLocale();
    }

    /**
     * Changer la langue d'affichage
     */
    public function changeLocale($locale)
    {
        if (in_array($locale, ['fr', 'en'])) {
            $this->currentLocale = $locale;
        }
        // Émettre un événement pour signaler le changement de langue
        $this->dispatch('tour-locale-updated');
    }

    /**
     * Obtenir le nom du niveau de difficulté
     */
    public function getDifficultyLabel($level)
    {
        $labels = [
            'easy' => 'Facile',
            'moderate' => 'Modéré',
            'difficult' => 'Difficile',
            'expert' => 'Expert',
        ];

        return $labels[$level] ?? 'Non spécifié';
    }

    /**
     * Obtenir la couleur pour le niveau de difficulté
     */
    public function getDifficultyColor($level)
    {
        $colors = [
            'easy' => 'success',
            'moderate' => 'warning',
            'difficult' => 'danger',
            'expert' => 'dark',
        ];

        return $colors[$level] ?? 'secondary';
    }

    /**
     * Obtenir le badge de statut
     */
    public function getStatusBadge()
    {
        $statuses = [
            'draft' => ['label' => 'Brouillon', 'color' => 'secondary'],
            'pending_approval' => ['label' => 'En attente', 'color' => 'warning'],
            'approved' => ['label' => 'Approuvé', 'color' => 'success'],
            'rejected' => ['label' => 'Rejeté', 'color' => 'danger'],
            'active' => ['label' => 'Actif', 'color' => 'primary'],
            'inactive' => ['label' => 'Inactif', 'color' => 'secondary'],
        ];

        return $statuses[$this->tour->status] ?? ['label' => 'Inconnu', 'color' => 'secondary'];
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.tour.tour-details', [
            'availableLocales' => ['fr', 'en'],
        ]);
    }
}
