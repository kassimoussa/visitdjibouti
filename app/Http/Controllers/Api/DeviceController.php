<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use App\Models\UserLocationHistory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class DeviceController extends Controller
{
    /**
     * Update comprehensive device information for the authenticated user.
     */
    public function updateDeviceInfo(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            // Technical device info
            'device_type' => 'nullable|string|max:50',
            'device_brand' => 'nullable|string|max:100',
            'device_model' => 'nullable|string|max:150',
            'device_name' => 'nullable|string|max:150',
            'device_os' => 'nullable|string|max:50',
            'device_os_version' => 'nullable|string|max:50',
            'device_platform' => 'nullable|string|max:50',
            
            // Application info
            'app_version' => 'nullable|string|max:30',
            'app_build' => 'nullable|string|max:30',
            'app_bundle_id' => 'nullable|string|max:150',
            'app_debug_mode' => 'nullable|boolean',
            
            // Screen characteristics
            'screen_resolution' => 'nullable|string|max:30',
            'screen_density' => 'nullable|numeric|between:0,99.99',
            'screen_size' => 'nullable|string|max:20',
            'orientation' => 'nullable|string|in:portrait,landscape',
            
            // Network capabilities
            'network_type' => 'nullable|string|max:30',
            'carrier_name' => 'nullable|string|max:100',
            'connection_type' => 'nullable|string|max:30',
            'is_roaming' => 'nullable|boolean',
            
            // System information
            'total_memory' => 'nullable|integer|min:0',
            'available_memory' => 'nullable|integer|min:0',
            'total_storage' => 'nullable|integer|min:0',
            'available_storage' => 'nullable|integer|min:0',
            'battery_level' => 'nullable|numeric|between:0,100',
            'is_charging' => 'nullable|boolean',
            'is_low_power_mode' => 'nullable|boolean',
            
            // Notifications and permissions
            'push_token' => 'nullable|string|max:500',
            'push_provider' => 'nullable|string|in:fcm,apns',
            'location_permission' => 'nullable|boolean',
            'camera_permission' => 'nullable|boolean',
            'contacts_permission' => 'nullable|boolean',
            'storage_permission' => 'nullable|boolean',
            'notification_permission' => 'nullable|boolean',
            
            // User settings
            'device_languages' => 'nullable|array',
            'device_languages.*' => 'string|max:10',
            'keyboard_language' => 'nullable|string|max:10',
            'number_format' => 'nullable|string|max:10',
            'currency_format' => 'nullable|string|max:10',
            'dark_mode_enabled' => 'nullable|boolean',
            'accessibility_enabled' => 'nullable|boolean',
            
            // Tracking and analytics
            'user_agent' => 'nullable|string|max:500',
            'advertising_id' => 'nullable|string|max:100',
            'ad_tracking_enabled' => 'nullable|boolean',
            'first_install_at' => 'nullable|date',
            'last_app_update_at' => 'nullable|date',
            'installed_apps' => 'nullable|array',
            
            // Usage metrics
            'total_app_launches' => 'nullable|integer|min:0',
            'total_time_spent' => 'nullable|integer|min:0',
            'crashes_count' => 'nullable|integer|min:0',
            'last_crash_at' => 'nullable|date',
            'feature_usage' => 'nullable|array',
            
            // Security
            'is_jailbroken_rooted' => 'nullable|boolean',
            'developer_mode_enabled' => 'nullable|boolean',
            'mock_location_enabled' => 'nullable|boolean',
            'device_fingerprint' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $validator->validated();
        $updateData['device_info_updated_at'] = now();
        
        // Increment session count if this is a new session
        if ($request->has('new_session') && $request->boolean('new_session')) {
            $updateData['session_count'] = $user->session_count + 1;
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Device information updated successfully',
            'data' => [
                'device_info_updated_at' => $user->device_info_updated_at,
                'session_count' => $user->session_count
            ]
        ]);
    }

    /**
     * Update user's current location.
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'location_source' => 'nullable|string|in:gps,network,passive',
            'current_address' => 'nullable|string|max:500',
            'current_city' => 'nullable|string|max:100',
            'current_country' => 'nullable|string|max:100',
            'current_timezone' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $locationData = $validator->validated();
        $locationData['location_updated_at'] = now();

        // Update user's current location
        $user->update($locationData);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => [
                'latitude' => $user->current_latitude,
                'longitude' => $user->current_longitude,
                'location_updated_at' => $user->location_updated_at
            ]
        ]);
    }

    /**
     * Record detailed location history entry.
     */
    public function recordLocationHistory(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
            'altitude' => 'nullable|numeric',
            'speed' => 'nullable|numeric|min:0',
            'heading' => 'nullable|numeric|between:0,360',
            'location_source' => 'nullable|string|in:gps,network,passive',
            'activity_type' => 'nullable|string|max:50',
            'confidence_level' => 'nullable|integer|between:0,100',
            'address' => 'nullable|string|max:500',
            'street' => 'nullable|string|max:200',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'place_name' => 'nullable|string|max:200',
            'place_category' => 'nullable|string|max:50',
            'timezone' => 'nullable|string|max:50',
            'is_indoor' => 'nullable|boolean',
            'nearby_pois' => 'nullable|array',
            'weather_condition' => 'nullable|string|max:50',
            'temperature' => 'nullable|numeric|between:-50,60',
            'recorded_at' => 'nullable|date',
            'session_id' => 'nullable|string|max:100',
            'trigger' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $historyData = $validator->validated();
        $historyData['app_user_id'] = $user->id;
        $historyData['recorded_at'] = $historyData['recorded_at'] ?? now();

        $locationHistory = UserLocationHistory::create($historyData);

        // Also update user's current location if this is more recent
        if (!$user->location_updated_at || 
            $locationHistory->recorded_at->gt($user->location_updated_at)) {
            $user->update([
                'current_latitude' => $historyData['latitude'],
                'current_longitude' => $historyData['longitude'],
                'location_accuracy' => $historyData['accuracy'] ?? null,
                'altitude' => $historyData['altitude'] ?? null,
                'speed' => $historyData['speed'] ?? null,
                'heading' => $historyData['heading'] ?? null,
                'location_source' => $historyData['location_source'] ?? null,
                'current_address' => $historyData['address'] ?? null,
                'current_city' => $historyData['city'] ?? null,
                'current_country' => $historyData['country'] ?? null,
                'current_timezone' => $historyData['timezone'] ?? null,
                'location_updated_at' => $locationHistory->recorded_at,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location history recorded successfully',
            'data' => [
                'id' => $locationHistory->id,
                'recorded_at' => $locationHistory->recorded_at
            ]
        ]);
    }

    /**
     * Get user's location history.
     */
    public function getLocationHistory(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'limit' => 'nullable|integer|min:1|max:1000',
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date',
            'activity_type' => 'nullable|string|max:50',
            'near_latitude' => 'nullable|numeric|between:-90,90',
            'near_longitude' => 'nullable|numeric|between:-180,180',
            'radius_km' => 'nullable|numeric|min:0.001|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $user->locationHistory()->orderBy('recorded_at', 'desc');

        // Apply filters
        if ($request->filled('from_date')) {
            $query->where('recorded_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('recorded_at', '<=', $request->to_date);
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter by proximity if coordinates are provided
        if ($request->filled(['near_latitude', 'near_longitude'])) {
            $radiusKm = $request->get('radius_km', 1.0);
            $query->withinRadius(
                $request->near_latitude,
                $request->near_longitude,
                $radiusKm
            );
        }

        $limit = $request->get('limit', 100);
        $locations = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => $locations,
            'meta' => [
                'total' => $locations->count(),
                'limit' => $limit
            ]
        ]);
    }

    /**
     * Get device information summary.
     */
    public function getDeviceInfo(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $deviceInfo = [
            // Technical info
            'device' => [
                'type' => $user->device_type,
                'brand' => $user->device_brand,
                'model' => $user->device_model,
                'name' => $user->device_name,
                'os' => $user->device_os,
                'os_version' => $user->device_os_version,
                'platform' => $user->device_platform,
            ],
            
            // App info
            'app' => [
                'version' => $user->app_version,
                'build' => $user->app_build,
                'bundle_id' => $user->app_bundle_id,
                'debug_mode' => $user->app_debug_mode,
            ],
            
            // Current location
            'location' => [
                'latitude' => $user->current_latitude,
                'longitude' => $user->current_longitude,
                'accuracy' => $user->location_accuracy,
                'address' => $user->current_address,
                'city' => $user->current_city,
                'country' => $user->current_country,
                'updated_at' => $user->location_updated_at,
            ],
            
            // System metrics
            'system' => [
                'total_memory' => $user->total_memory,
                'available_memory' => $user->available_memory,
                'battery_level' => $user->battery_level,
                'is_charging' => $user->is_charging,
                'is_low_power_mode' => $user->is_low_power_mode,
            ],
            
            // Usage statistics
            'usage' => [
                'session_count' => $user->session_count,
                'total_app_launches' => $user->total_app_launches,
                'total_time_spent' => $user->total_time_spent,
                'crashes_count' => $user->crashes_count,
                'last_crash_at' => $user->last_crash_at,
            ],
            
            // Permissions
            'permissions' => [
                'location' => $user->location_permission,
                'camera' => $user->camera_permission,
                'contacts' => $user->contacts_permission,
                'storage' => $user->storage_permission,
                'notifications' => $user->notification_permission,
            ],
            
            'last_updated' => $user->device_info_updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $deviceInfo
        ]);
    }

    /**
     * Get nearby users based on current location (privacy-aware).
     */
    public function getNearbyUsers(Request $request): JsonResponse
    {
        $user = Auth::guard('sanctum')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // Check if user has location permission and current location
        if (!$user->location_permission || !$user->current_latitude || !$user->current_longitude) {
            return response()->json([
                'success' => false,
                'message' => 'Location permission required or location not available'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'radius_km' => 'nullable|numeric|min:0.1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $radiusKm = $request->get('radius_km', 5.0);

        // Find nearby users (privacy-aware - only return aggregated data)
        $nearbyCount = AppUser::whereNotNull(['current_latitude', 'current_longitude'])
            ->where('location_permission', true)
            ->where('id', '!=', $user->id)
            ->where('location_updated_at', '>', now()->subHours(6)) // Only recent locations
            ->whereRaw(
                "(6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(current_latitude)) * COS(RADIANS(current_longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(current_latitude)))) <= ?",
                [$user->current_latitude, $user->current_longitude, $user->current_latitude, $radiusKm]
            )
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'nearby_users_count' => $nearbyCount,
                'radius_km' => $radiusKm,
                'center' => [
                    'latitude' => $user->current_latitude,
                    'longitude' => $user->current_longitude,
                ]
            ]
        ]);
    }
}
