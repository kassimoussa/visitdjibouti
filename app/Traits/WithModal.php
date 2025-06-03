<?php

namespace App\Traits;

use Livewire\Attributes\On;

trait WithModal
{
    // Propriétés pour le modal
    public $modalTitle = '';
    public $modalSize = '';
    public $showModal = false;
    
    /**
     * Ouvre un modal avec les options spécifiées
     *
     * @param string $title Titre du modal
     * @param string $size Taille du modal (modal-sm, modal-lg, modal-xl, modal-fullscreen)
     * @return void
     */
    public function openModal($title = '', $size = '')
    {
        $this->modalTitle = $title;
        $this->modalSize = $size;
        $this->showModal = true;
        
        $this->dispatch('openModal', [
            'title' => $title,
            'size' => $size
        ]);
    }
    
    /**
     * Ferme le modal actif
     *
     * @return void
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->dispatch('closeModal');
    }
    
    /**
     * Méthode appelée quand le modal est fermé
     */
    #[On('modalClosed')]
    public function handleModalClosed()
    {
        $this->showModal = false;
        // Vous pouvez ajouter d'autres actions ici, comme réinitialiser un formulaire
    }
    
    /**
     * Méthode appelée quand le bouton d'action principal du modal est cliqué
     */
    #[On('modalAction')]
    public function handleModalAction()
    {
        // À surcharger dans le composant qui utilise ce trait
        // Par exemple: $this->save();
    }
}