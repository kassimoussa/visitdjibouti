<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExternalLink;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalLinkController extends Controller
{
    /**
     * Get all active external links
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $links = ExternalLink::active()->orderBy('name')->get();

            $transformedLinks = $links->map(function ($link) {
                return $this->transformLink($link);
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'links' => $transformedLinks,
                    'total' => $links->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch external links',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific external link by ID
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $link = ExternalLink::active()->find($id);

            if (!$link) {
                return response()->json([
                    'success' => false,
                    'message' => 'External link not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'link' => $this->transformLink($link)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch external link',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transform external link for API response
     */
    private function transformLink(ExternalLink $link): array
    {
        return [
            'id' => $link->id,
            'name' => $link->name,
            'url' => $link->url,
            'status' => $link->status,
            'is_external' => $this->isExternalUrl($link->url),
            'domain' => $this->extractDomain($link->url),
            'created_at' => $link->created_at->toISOString(),
            'updated_at' => $link->updated_at->toISOString()
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
        
        if (!$domain) {
            return null;
        }

        // Remove 'www.' prefix if present
        return preg_replace('/^www\./', '', $domain);
    }
}