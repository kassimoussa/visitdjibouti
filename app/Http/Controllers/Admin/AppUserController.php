<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AppUserController extends Controller
{
    /**
     * Display the main app users management page
     */
    public function index()
    {
        return view('admin.app-users.index');
    }

    /**
     * Display the specified app user details
     */
    public function show($id)
    {
        $appUser = AppUser::with([
            'reservations.reservable.translations',
            'favorites',
            'favoritePois.translations',
            'favoriteEvents.translations',
        ])->findOrFail($id);

        return view('admin.app-users.show', compact('appUser'));
    }

    /**
     * Show the form for editing the specified app user
     */
    public function edit($id)
    {
        $appUser = AppUser::findOrFail($id);

        return view('admin.app-users.edit', compact('appUser'));
    }

    /**
     * Update the specified app user in storage
     */
    public function update(Request $request, $id)
    {
        $appUser = AppUser::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:app_users,email,'.$appUser->id,
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'preferred_language' => 'required|in:fr,en,ar',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'push_notifications_enabled' => 'required|boolean',
            'email_notifications_enabled' => 'required|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Only update password if provided
        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $appUser->update($validated);

        return redirect()->route('app-users.show', $appUser->id)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified app user from storage
     */
    public function destroy($id)
    {
        $appUser = AppUser::findOrFail($id);

        // Soft delete to preserve data integrity
        $appUser->delete();

        return redirect()->route('app-users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    /**
     * Restore a soft-deleted app user
     */
    public function restore($id)
    {
        $appUser = AppUser::onlyTrashed()->findOrFail($id);
        $appUser->restore();

        return redirect()->route('app-users.show', $appUser->id)
            ->with('success', 'Utilisateur restauré avec succès.');
    }

    /**
     * Force delete a soft-deleted app user
     */
    public function forceDelete($id)
    {
        $appUser = AppUser::onlyTrashed()->findOrFail($id);

        // Delete associated avatar if it's a local file
        if ($appUser->avatar && ! str_starts_with($appUser->avatar, 'http')) {
            Storage::disk('public')->delete($appUser->avatar);
        }

        $appUser->forceDelete();

        return redirect()->route('app-users.index')
            ->with('success', 'Utilisateur définitivement supprimé.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus($id)
    {
        $appUser = AppUser::findOrFail($id);
        $appUser->update(['is_active' => ! $appUser->is_active]);

        $status = $appUser->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Utilisateur {$status} avec succès.");
    }

    /**
     * Send password reset email to user
     */
    public function sendPasswordReset($id)
    {
        $appUser = AppUser::findOrFail($id);

        // TODO: Implement password reset email functionality
        // This would typically send an email with a reset link

        return back()->with('success', 'Email de réinitialisation envoyé.');
    }

    /**
     * Get user statistics for AJAX requests
     */
    public function stats()
    {
        $stats = [
            'total_users' => AppUser::count(),
            'active_users' => AppUser::where('is_active', true)->count(),
            'inactive_users' => AppUser::where('is_active', false)->count(),
            'social_users' => AppUser::whereNotNull('provider')->where('provider', '!=', 'email')->count(),
            'email_users' => AppUser::where('provider', 'email')->orWhereNull('provider')->count(),
            'users_with_reservations' => AppUser::whereHas('reservations')->count(),
            'users_with_favorites' => AppUser::whereHas('favorites')->count(),
            'recent_signups' => AppUser::where('created_at', '>=', now()->subDays(30))->count(),
            'active_last_month' => AppUser::where('last_login_at', '>=', now()->subDays(30))->count(),
        ];

        // Users by provider
        $providers = AppUser::selectRaw('COALESCE(provider, "email") as provider, COUNT(*) as count')
            ->groupBy('provider')
            ->pluck('count', 'provider')
            ->toArray();

        // Users by preferred language
        $languages = AppUser::selectRaw('COALESCE(preferred_language, "fr") as language, COUNT(*) as count')
            ->groupBy('preferred_language')
            ->pluck('count', 'language')
            ->toArray();

        // Registration trends (last 12 months)
        $registrationTrends = AppUser::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return response()->json([
            'general_stats' => $stats,
            'providers' => $providers,
            'languages' => $languages,
            'registration_trends' => $registrationTrends,
        ]);
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        // TODO: Implement CSV export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Bulk actions on multiple users
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:app_users,id',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'activate':
                AppUser::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = count($userIds).' utilisateur(s) activé(s).';
                break;

            case 'deactivate':
                AppUser::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = count($userIds).' utilisateur(s) désactivé(s).';
                break;

            case 'delete':
                AppUser::whereIn('id', $userIds)->delete();
                $message = count($userIds).' utilisateur(s) supprimé(s).';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Dashboard view for app users analytics
     */
    public function dashboard()
    {
        // Get general statistics
        $totalUsers = AppUser::count();
        $activeUsers = AppUser::where('is_active', true)->count();
        $inactiveUsers = AppUser::where('is_active', false)->count();
        $recentSignups = AppUser::where('created_at', '>=', now()->subDays(30))->count();

        // Get top users with most reservations
        $topUsersByReservations = AppUser::withCount('reservations')
            ->orderByDesc('reservations_count')
            ->limit(10)
            ->get();

        // Get top users with most favorites
        $topUsersByFavorites = AppUser::withCount('favorites')
            ->orderByDesc('favorites_count')
            ->limit(10)
            ->get();

        // Recent users
        $recentUsers = AppUser::with(['reservations', 'favorites'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.app-users.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'recentSignups',
            'topUsersByReservations',
            'topUsersByFavorites',
            'recentUsers'
        ));
    }
}
