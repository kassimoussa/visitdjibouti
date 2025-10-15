<?php

namespace App\Livewire\Admin\Poi;

use App\Models\Poi;
use Livewire\Component;

class PoiDetails extends Component
{
    public $poi;

    public $currentLocale;

    /**
     * Montage du composant
     */
    public function mount($poiId)
    {
        // Charger le POI avec ses relations
        $this->poi = Poi::with(['categories', 'media', 'creator', 'featuredImage', 'translations'])
            ->findOrFail($poiId);

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
        $this->dispatch('poi-locale-updated');
    }

    /**
     * Obtenir l'icône pour un type de contact
     */
    public function getContactTypeIcon($type)
    {
        $icons = [
            'general' => 'fas fa-phone',
            'restaurant' => 'fas fa-utensils',
            'tour_operator' => 'fas fa-map-marked-alt',
            'guide' => 'fas fa-user-tie',
            'accommodation' => 'fas fa-bed',
            'park_office' => 'fas fa-tree',
            'emergency' => 'fas fa-ambulance',
            'transport' => 'fas fa-bus',
            'shop' => 'fas fa-shopping-bag',
            'other' => 'fas fa-info-circle',
        ];

        return $icons[$type] ?? 'fas fa-phone';
    }

    /**
     * Obtenir la couleur pour un type de contact
     */
    public function getContactTypeColor($type)
    {
        $colors = [
            'general' => '#6c757d',
            'restaurant' => '#fd7e14',
            'tour_operator' => '#20c997',
            'guide' => '#0d6efd',
            'accommodation' => '#6f42c1',
            'park_office' => '#198754',
            'emergency' => '#dc3545',
            'transport' => '#ffc107',
            'shop' => '#e91e63',
            'other' => '#6c757d',
        ];

        return $colors[$type] ?? '#6c757d';
    }

    /**
     * Obtenir le nom du type de contact
     */
    public function getContactTypeName($type)
    {
        $types = [
            'general' => 'Contact général',
            'restaurant' => 'Restaurant',
            'tour_operator' => 'Opérateur de tourisme',
            'guide' => 'Guide local',
            'accommodation' => 'Hébergement',
            'park_office' => 'Bureau du parc',
            'emergency' => 'Urgence',
            'transport' => 'Transport',
            'shop' => 'Boutique/Commerce',
            'other' => 'Autre',
        ];

        return $types[$type] ?? 'Type inconnu';
    }

    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.poi.poi-details', [
            'availableLocales' => ['fr', 'en'],
        ]);
    }
}
