<?php

namespace App\Livewire\Admin;

use App\Models\ExternalLink;
use Livewire\Component;
use Livewire\WithPagination;

class ExternalLinkManager extends Component
{
    use WithPagination;

    public $showModal = false;

    public $modalMode = 'create'; // create or edit

    public $modalTitle = 'Nouveau lien externe';

    public $linkId = null;

    // Propriétés du formulaire
    public $name = '';

    public $url = '';

    public $status = true;

    // Recherche
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'url' => 'required|url|max:255',
        'status' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom est obligatoire.',
        'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'url.required' => 'L\'URL est obligatoire.',
        'url.url' => 'L\'URL doit être valide (ex: https://example.com).',
        'url.max' => 'L\'URL ne peut pas dépasser 255 caractères.',
    ];

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $links = ExternalLink::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('url', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.external-links.external-link-manager', [
            'links' => $links,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->modalMode = 'create';
        $this->modalTitle = 'Nouveau lien externe';
        $this->showModal = true;
    }

    public function openEditModal($linkId)
    {
        $link = ExternalLink::findOrFail($linkId);

        $this->linkId = $link->id;
        $this->name = $link->name;
        $this->url = $link->url;
        $this->status = $link->status;

        $this->modalMode = 'edit';
        $this->modalTitle = 'Modifier le lien externe';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->modalMode === 'create') {
                ExternalLink::create([
                    'name' => $this->name,
                    'url' => $this->url,
                    'status' => $this->status,
                ]);

                session()->flash('message', 'Lien externe créé avec succès.');
            } else {
                $link = ExternalLink::findOrFail($this->linkId);
                $link->update([
                    'name' => $this->name,
                    'url' => $this->url,
                    'status' => $this->status,
                ]);

                session()->flash('message', 'Lien externe modifié avec succès.');
            }

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: '.$e->getMessage());
        }
    }

    public function toggleStatus($linkId)
    {
        $link = ExternalLink::findOrFail($linkId);
        $link->update(['status' => ! $link->status]);

        $status = $link->status ? 'activé' : 'désactivé';
        session()->flash('message', "Lien externe {$status} avec succès.");
    }

    public function delete($linkId)
    {
        try {
            $link = ExternalLink::findOrFail($linkId);
            $link->delete();

            session()->flash('message', 'Lien externe supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la suppression: '.$e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->url = '';
        $this->status = true;
        $this->linkId = null;
        $this->resetValidation();
    }
}
