<?php

namespace App\Livewire\Admin\News;

use App\Models\News;
use Livewire\Component;
use Livewire\Attributes\On;

class DeleteNewsModal extends Component
{
    public $showModal = false;
    public $newsId = null;
    public $newsTitle = '';
    public $isDeleting = false;

    #[On('confirm-delete-news')]
    public function confirmDelete($newsId, $newsTitle)
    {
        $this->newsId = $newsId;
        $this->newsTitle = $newsTitle;
        $this->showModal = true;
    }

    public function deleteNews()
    {
        if (!$this->newsId) {
            return;
        }

        $this->isDeleting = true;

        try {
            $news = News::findOrFail($this->newsId);
            $title = $news->title;
            
            // Supprimer l'article
            $news->delete();

            $this->closeModal();

            // Dispatch événement pour rafraîchir la liste
            $this->dispatch('news-deleted', [
                'message' => "L'article \"$title\" a été supprimé avec succès."
            ]);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => "Article supprimé avec succès"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ]);
        }

        $this->isDeleting = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->newsId = null;
        $this->newsTitle = '';
        $this->isDeleting = false;
    }

    public function render()
    {
        return view('livewire.admin.news.delete-news-modal');
    }
}