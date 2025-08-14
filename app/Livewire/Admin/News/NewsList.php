<?php

namespace App\Livewire\Admin\News;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\AdminUser;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class NewsList extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $status = '';

    #[Url(except: '')]
    public $category = '';

    #[Url(except: '')]
    public $author = '';

    public $categories = [];
    public $authors = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadCategories();
        $this->loadAuthors();
    }

    protected function loadCategories()
    {
        $this->categories = NewsCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                ];
            });
    }

    protected function loadAuthors()
    {
        $this->authors = AdminUser::select('id', 'name')
            ->whereHas('createdNews')
            ->orderBy('name')
            ->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingAuthor()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'category', 'author']);
        $this->resetPage();
    }

    public function confirmDelete($newsId, $newsTitle)
    {
        $this->dispatch('confirm-delete-news', $newsId, $newsTitle);
    }

    #[On('news-deleted')]
    public function handleNewsDeleted($data)
    {
        // Rafraîchir la liste après suppression
        $this->resetPage();
    }

    public function getNewsProperty()
    {
        return News::with(['translations', 'category.translations', 'creator', 'featuredImage'])
            ->when($this->search, function ($query) {
                $query->whereHas('translations', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->category, function ($query) {
                $query->where('news_category_id', $this->category);
            })
            ->when($this->author, function ($query) {
                $query->where('creator_id', $this->author);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin.news.news-list', [
            'news' => $this->news,
        ]);
    }
}