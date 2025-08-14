<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories with hierarchy
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');
            
            // Get all categories with their translations and relationships
            $categories = Category::with([
                'translations',
                'parent.translations',
                'children.translations'
            ])
            ->roots() // Start with root categories
            ->where('is_active', true)
            ->get();

            $formattedCategories = $categories->map(function ($category) use ($locale) {
                return $this->formatCategoryWithChildren($category, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $formattedCategories,
                    'total' => $categories->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flat list of all categories
     */
    public function flat(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');
            
            $categories = Category::with(['translations', 'parent.translations'])
                ->where('is_active', true)
                ->orderBy('parent_id')
                ->orderBy('sort_order')
                ->get();

            $formattedCategories = $categories->map(function ($category) use ($locale) {
                return $this->formatCategory($category, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $formattedCategories,
                    'total' => $categories->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get category details with children
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');
            
            $category = Category::with([
                'translations',
                'parent.translations',
                'children.translations',
                'pois.translations'
            ])->find($id);

            if (!$category || !$category->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found'
                ], 404);
            }

            $formattedCategory = $this->formatCategoryWithChildren($category, $locale);
            $formattedCategory['pois_count'] = $category->pois()->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => $formattedCategory
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format category for API response
     */
    private function formatCategory(Category $category, string $locale): array
    {
        return [
            'id' => $category->id,
            'name' => $category->translation($locale)->name ?? $category->name,
            'description' => $category->translation($locale)->description ?? '',
            'slug' => $category->slug,
            'icon' => $category->icon,
            'color' => $category->color,
            'level' => $category->level,
            'sort_order' => $category->sort_order,
            'parent_id' => $category->parent_id,
            'parent_name' => $category->parent ? 
                ($category->parent->translation($locale)->name ?? $category->parent->name) : null,
            'breadcrumb' => $category->getBreadcrumb(' > '),
            'is_root' => $category->isRoot(),
            'has_children' => $category->hasChildren(),
            'children_count' => $category->children()->count(),
        ];
    }

    /**
     * Format category with children for hierarchical response
     */
    private function formatCategoryWithChildren(Category $category, string $locale): array
    {
        $formatted = $this->formatCategory($category, $locale);
        
        if ($category->children()->count() > 0) {
            $formatted['children'] = $category->children->map(function ($child) use ($locale) {
                return $this->formatCategoryWithChildren($child, $locale);
            });
        } else {
            $formatted['children'] = [];
        }

        return $formatted;
    }
}