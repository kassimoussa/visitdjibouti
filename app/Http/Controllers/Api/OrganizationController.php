<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrganizationInfo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    /**
     * Get organization information
     */
    public function getInfo(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', 'fr');

            // Get the first (and likely only) organization info record
            $organization = OrganizationInfo::with(['logo', 'translations'])->first();

            if (! $organization) {
                return response()->json([
                    'success' => false,
                    'message' => 'Organization information not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'organization' => $this->transformOrganization($organization, $locale),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch organization information',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform organization for API response
     */
    private function transformOrganization(OrganizationInfo $organization, string $locale = 'fr'): array
    {
        return [
            'id' => $organization->id,
            'name' => $organization->getTranslatedName($locale),
            'description' => $organization->getTranslatedDescription($locale),
            'email' => $organization->email,
            'phone' => $organization->phone,
            'address' => $organization->address,
            'opening_hours' => $organization->getTranslatedOpeningHours($locale),
            'logo' => $organization->logo ? [
                'id' => $organization->logo->id,
                'url' => $organization->logo->getImageUrl(),
                'alt' => $organization->logo->translation($locale)->alt_text ?? 'Logo',
            ] : null,
            'created_at' => $organization->created_at->toISOString(),
            'updated_at' => $organization->updated_at->toISOString(),
        ];
    }
}
