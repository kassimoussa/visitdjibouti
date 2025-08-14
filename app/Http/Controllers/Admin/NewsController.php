<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of news articles.
     */
    public function index(Request $request)
    {
        $query = News::with(['translations', 'category', 'featuredImage', 'creator'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->where('news_category_id', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->whereHas('translations', function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }

        $news = $query->paginate(20);
        $categories = NewsCategory::with('translations')->where('is_active', true)->get();
        
        return view('admin.news.index', compact('news', 'categories'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create()
    {
        return view('admin.news.create');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news)
    {
        return view('admin.news.edit', compact('news'));
    }

    /**
     * Display the specified news article.
     */
    public function show(News $news)
    {
        $news->load(['translations', 'category.translations', 'featuredImage', 'media', 'tags.translations', 'creator']);
        
        return view('admin.news.show', compact('news'));
    }

    /**
     * Duplicate a news article.
     */
    public function duplicate(News $news)
    {
        $newNews = $news->replicate();
        $newNews->slug = $news->slug . '-copie';
        $newNews->status = 'draft';
        $newNews->published_at = null;
        $newNews->is_featured = false;
        $newNews->views_count = 0;
        $newNews->creator_id = auth()->guard('admin')->id();
        $newNews->save();

        // Copy translations
        foreach ($news->translations as $translation) {
            $newTranslation = $translation->replicate();
            $newTranslation->news_id = $newNews->id;
            $newTranslation->title = $translation->title . ' (Copie)';
            $newTranslation->save();
        }

        // Copy relations
        $newNews->categories()->sync($news->categories->pluck('id'));
        $newNews->tags()->sync($news->tags->pluck('id'));
        $newNews->media()->sync($news->media->pluck('id'));

        return redirect()->route('admin.news.edit', $newNews)
            ->with('success', 'Article dupliqué avec succès');
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('admin.news.index')
            ->with('success', 'Article supprimé avec succès');
    }

    /**
     * Bulk actions on news articles.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:publish,draft,archive,delete',
            'news_ids' => 'required|array|min:1',
            'news_ids.*' => 'exists:news,id'
        ]);

        $newsIds = $request->news_ids;
        $action = $request->action;

        switch ($action) {
            case 'publish':
                News::whereIn('id', $newsIds)->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = count($newsIds) . ' article(s) publié(s)';
                break;

            case 'draft':
                News::whereIn('id', $newsIds)->update(['status' => 'draft']);
                $message = count($newsIds) . ' article(s) mis en brouillon';
                break;

            case 'archive':
                News::whereIn('id', $newsIds)->update(['status' => 'archived']);
                $message = count($newsIds) . ' article(s) archivé(s)';
                break;

            case 'delete':
                News::whereIn('id', $newsIds)->delete();
                $message = count($newsIds) . ' article(s) supprimé(s)';
                break;
        }

        return redirect()->route('admin.news.index')
            ->with('success', $message);
    }

    /**
     * Export news articles.
     */
    public function export(Request $request)
    {
        // Implementation for CSV/Excel export
        // This would typically use a package like Laravel Excel
        
        return response()->json(['message' => 'Export feature coming soon']);
    }
}