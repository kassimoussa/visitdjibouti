<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppSettingController extends Controller
{
    /**
     * Get all app settings grouped by type.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', app()->getLocale());

            // Validate locale
            $supportedLocales = ['fr', 'en', 'ar'];
            if (! in_array($locale, $supportedLocales)) {
                $locale = config('app.fallback_locale', 'fr');
            }

            $settings = AppSetting::getAllByType($locale);

            return response()->json([
                'success' => true,
                'data' => $settings,
                'locale' => $locale,
                'message' => 'App settings retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get all app settings as flat array.
     */
    public function flat(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', app()->getLocale());

            // Validate locale
            $supportedLocales = ['fr', 'en', 'ar'];
            if (! in_array($locale, $supportedLocales)) {
                $locale = config('app.fallback_locale', 'fr');
            }

            $settings = AppSetting::getAllFlat($locale);

            return response()->json([
                'success' => true,
                'data' => $settings,
                'locale' => $locale,
                'message' => 'App settings retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve app settings',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get settings by type.
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', app()->getLocale());

            // Validate locale
            $supportedLocales = ['fr', 'en', 'ar'];
            if (! in_array($locale, $supportedLocales)) {
                $locale = config('app.fallback_locale', 'fr');
            }

            // Validate type
            $validTypes = ['image', 'text', 'config', 'mixed'];
            if (! in_array($type, $validTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid setting type',
                    'valid_types' => $validTypes,
                ], 400);
            }

            $settings = AppSetting::active()
                ->byType($type)
                ->with('media')
                ->get();

            $result = [];
            foreach ($settings as $setting) {
                $translatedContent = $setting->getTranslatedContent($locale);

                // Add media URL if media exists
                if ($setting->media) {
                    $translatedContent['media_url'] = $setting->media->url;
                    $translatedContent['thumbnail_url'] = $setting->media->thumbnail_url;
                }

                $result[$setting->key] = $translatedContent;
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'type' => $type,
                'locale' => $locale,
                'message' => "Settings of type '{$type}' retrieved successfully",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve settings by type',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get a specific setting by key.
     */
    public function show(Request $request, string $key): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', app()->getLocale());

            // Validate locale
            $supportedLocales = ['fr', 'en', 'ar'];
            if (! in_array($locale, $supportedLocales)) {
                $locale = config('app.fallback_locale', 'fr');
            }

            $setting = AppSetting::active()
                ->where('key', $key)
                ->with('media')
                ->first();

            if (! $setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting not found',
                ], 404);
            }

            $translatedContent = $setting->getTranslatedContent($locale);

            // Add media URL if media exists
            if ($setting->media) {
                $translatedContent['media_url'] = $setting->media->url;
                $translatedContent['thumbnail_url'] = $setting->media->thumbnail_url;
            }

            // Add metadata
            $result = [
                'key' => $setting->key,
                'type' => $setting->type,
                'content' => $translatedContent,
                'updated_at' => $setting->updated_at->toISOString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $result,
                'locale' => $locale,
                'message' => 'Setting retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve setting',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Get splash screens specifically (convenience endpoint).
     */
    public function splashScreens(Request $request): JsonResponse
    {
        try {
            $locale = $request->header('Accept-Language', app()->getLocale());

            // Validate locale
            $supportedLocales = ['fr', 'en', 'ar'];
            if (! in_array($locale, $supportedLocales)) {
                $locale = config('app.fallback_locale', 'fr');
            }

            $setting = AppSetting::active()
                ->where('key', 'splash_screens')
                ->with('media')
                ->first();

            if (! $setting) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'locale' => $locale,
                    'message' => 'No splash screens configured',
                ]);
            }

            $translatedContent = $setting->getTranslatedContent($locale);

            return response()->json([
                'success' => true,
                'data' => $translatedContent,
                'locale' => $locale,
                'message' => 'Splash screens retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve splash screens',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}
