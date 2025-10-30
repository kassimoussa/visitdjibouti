<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperator;
use Livewire\Component;

class TourOperatorDetail extends Component
{
    public TourOperator $tourOperator;

    public $activeLocale = 'fr';

    public $availableLocales = ['fr', 'en'];

    public $showEditModal = false;

    public function mount(TourOperator $tourOperator)
    {
        $this->tourOperator = $tourOperator->load([
            'translations',
            'logo',
            'media',
            'pois.translations',
            'tours.translations',
            'tours.featuredImage',
            'activities.translations',
            'activities.featuredImage'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.tour-operator.tour-operator-detail');
    }

    public function setActiveLocale($locale)
    {
        if (in_array($locale, $this->availableLocales)) {
            $this->activeLocale = $locale;
        }
    }

    public function toggleStatus()
    {
        try {
            $this->tourOperator->update(['is_active' => ! $this->tourOperator->is_active]);
            $this->tourOperator->refresh();

            $status = $this->tourOperator->is_active ? 'activé' : 'désactivé';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut: '.$e->getMessage());
        }
    }

    public function toggleFeatured()
    {
        try {
            $this->tourOperator->update(['featured' => ! $this->tourOperator->featured]);
            $this->tourOperator->refresh();

            $status = $this->tourOperator->featured ? 'mis en avant' : 'retiré de la mise en avant';
            session()->flash('message', "Opérateur de tour {$status} avec succès.");
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors du changement de statut featured: '.$e->getMessage());
        }
    }

    public function openEditModal()
    {
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
    }

    public function redirectToEdit()
    {
        return redirect()->route('tour-operators.edit', $this->tourOperator->id);
    }

    public function delete()
    {
        try {
            $name = $this->tourOperator->getTranslatedName($this->activeLocale);
            $this->tourOperator->delete();

            session()->flash('message', "Opérateur de tour '{$name}' supprimé avec succès.");

            return redirect()->route('tour-operators.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }

    public function getTranslation()
    {
        return $this->tourOperator->getTranslation($this->activeLocale);
    }

    public function getPhonesArray()
    {
        return $this->tourOperator->phones_array ?? [];
    }

    public function getEmailsArray()
    {
        return $this->tourOperator->emails_array ?? [];
    }

    public function getServedPoisCount()
    {
        return $this->tourOperator->pois()->count();
    }

    public function getMediaCount()
    {
        return $this->tourOperator->media()->count();
    }

    public function getToursCount()
    {
        return $this->tourOperator->tours()->count();
    }

    public function getActivitiesCount()
    {
        return $this->tourOperator->activities()->count();
    }
}
