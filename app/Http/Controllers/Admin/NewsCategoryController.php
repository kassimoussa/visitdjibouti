<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsCategoryController extends Controller
{
    /**
     * Display a listing of news categories.
     */
    public function index()
    {
        $categories = NewsCategory::with(['translations', 'children.translations', 'parent.translations'])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.news-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new news category.
     */
    public function create()
    {
        $parentCategories = NewsCategory::with('translations')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.news-categories.create', compact('parentCategories'));
    }

    /**
     * Show the form for editing the specified news category.
     */
    public function edit(NewsCategory $newsCategory)
    {
        $parentCategories = NewsCategory::with('translations')
            ->where('id', '!=', $newsCategory->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        return view('admin.news-categories.edit', compact('newsCategory', 'parentCategories'));
    }

    /**
     * Display the specified news category.
     */
    public function show(NewsCategory $newsCategory)
    {
        $newsCategory->load(['translations', 'parent.translations', 'children.translations']);
        
        $recentNews = $newsCategory->news()
            ->with(['translations', 'featuredImage'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.news-categories.show', compact('newsCategory', 'recentNews'));
    }

    /**
     * Remove the specified news category from storage.
     */
    public function destroy(NewsCategory $newsCategory)
    {
        // Check if category has news articles
        if ($newsCategory->news()->count() > 0) {
            return redirect()->route('admin.news-categories.index')
                ->with('error', 'Impossible de supprimer une catégorie qui contient des articles');
        }

        // Move children to parent level
        if ($newsCategory->children()->count() > 0) {
            $newsCategory->children()->update(['parent_id' => $newsCategory->parent_id]);
        }

        $newsCategory->delete();

        return redirect()->route('admin.news-categories.index')
            ->with('success', 'Catégorie supprimée avec succès');
    }

    /**
     * Update the sort order of categories.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:news_categories,id',
            'categories.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->categories as $categoryData) {
            NewsCategory::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Toggle category active status.
     */
    public function toggleStatus(NewsCategory $newsCategory)
    {
        $newsCategory->update(['is_active' => !$newsCategory->is_active]);

        $status = $newsCategory->is_active ? 'activée' : 'désactivée';
        
        return redirect()->back()
            ->with('success', "Catégorie {$status} avec succès");
    }
}