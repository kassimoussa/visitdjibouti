<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    /**
     * Get all external links from organization
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', config('app.fallback_locale', 'fr'));

            $links = Link::with(['translations', 'organizationInfo'])
                ->orderBy('order')
                ->get();

            $transformedLinks = $links->map(function ($link) use ($locale) {
                return $this->transformLink($link, $locale);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'links' => $transformedLinks,
                    'total' => $links->count(),
                ],
                'locale' => $locale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch external links',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get a specific external link by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', config('app.fallback_locale', 'fr'));

            $link = Link::with(['translations', 'organizationInfo'])->find($id);

            if (! $link) {
                return response()->json([
                    'success' => false,
                    'message' => 'External link not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'link' => $this->transformLink($link, $locale),
                ],
                'locale' => $locale,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch external link',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Transform link for API response
     */
    private function transformLink(Link $link, string $locale = 'fr'): array
    {
        $translation = $link->translation($locale);

        return [
            'id' => $link->id,
            'name' => $translation ? $translation->name : '',
            'url' => $link->url,
            'platform' => $link->platform,
            'order' => $link->order,
            'icon' => $link->icon,
            'color' => $link->color,
            'is_external' => $this->isExternalUrl($link->url),
            'domain' => $this->extractDomain($link->url),
            'organization_info_id' => $link->organization_info_id,
            'created_at' => $link->created_at->toISOString(),
            'updated_at' => $link->updated_at->toISOString(),
        ];
    }

    /**
     * Check if URL is external (not from the same domain)
     */
    private function isExternalUrl(string $url): bool
    {
        $currentDomain = request()->getHost();
        $urlDomain = parse_url($url, PHP_URL_HOST);

        return $urlDomain && $urlDomain !== $currentDomain;
    }

    /**
     * Extract domain from URL
     */
    private function extractDomain(string $url): ?string
    {
        $domain = parse_url($url, PHP_URL_HOST);

        if (! $domain) {
            return null;
        }

        // Remove 'www.' prefix if present
        return preg_replace('/^www\./', '', $domain);
    }
}
