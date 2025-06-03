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
        if (in_array($locale, ['fr', 'en', 'ar'])) {
            $this->currentLocale = $locale;
        }
        // Émettre un événement pour signaler le changement de langue
            $this->dispatch('poi-locale-updated');
    }
    
    /**
     * Rendu du composant
     */
    public function render()
    {
        return view('livewire.admin.poi.poi-details', [
            'availableLocales' => ['fr', 'en', 'ar'],
        ]);
    }
}