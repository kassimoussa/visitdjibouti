<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use App\Models\TourOperator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a listing of all activities
     */
    public function index(Request $request): View
    {
        $query = Activity::with(['tourOperator.translations', 'translations']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('translations', function ($tq) use ($search) {
                    $tq->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_operator_id')) {
            $query->where('tour_operator_id', $request->tour_operator_id);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->is_featured === '1');
        }

        // Tri
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'id') {
            $query->orderBy('id', $sortDirection);
        } elseif ($sortField === 'name') {
            // Tri par nom (via traduction)
            $locale = session('locale', 'fr');
            $query->leftJoin('activity_translations', function ($join) use ($locale) {
                $join->on('activities.id', '=', 'activity_translations.activity_id')
                    ->where('activity_translations.locale', '=', $locale);
            })
                ->orderBy('activity_translations.title', $sortDirection)
                ->select('activities.*');
        } elseif ($sortField === 'operator') {
            // Tri par opérateur
            $query->join('tour_operators', 'activities.tour_operator_id', '=', 'tour_operators.id')
                ->orderBy('tour_operators.name', $sortDirection)
                ->select('activities.*');
        } else {
            $query->orderBy('created_at', $sortDirection);
        }

        $activities = $query->paginate(20)->withQueryString();

        // Liste des opérateurs pour le filtre
        $tourOperators = TourOperator::with('translations')
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        return view('admin.activities.index', compact('activities', 'tourOperators'));
    }

    /**
     * Display the specified activity
     */
    public function show(Activity $activity): View
    {
        $activity->load([
            'tourOperator.translations',
            'featuredImage',
            'media',
            'translations',
            'registrations' => function ($query) {
                $query->latest()->take(10);
            },
            'registrations.appUser',
        ]);

        // Statistiques des inscriptions
        $registrationStats = [
            'total' => $activity->registrations()->count(),
            'pending' => $activity->registrations()->where('status', 'pending')->count(),
            'confirmed' => $activity->registrations()->where('status', 'confirmed')->count(),
            'completed' => $activity->registrations()->where('status', 'completed')->count(),
            'cancelled' => $activity->registrations()->whereIn('status', ['cancelled_by_user', 'cancelled_by_operator'])->count(),
        ];

        return view('admin.activities.show', compact('activity', 'registrationStats'));
    }

    /**
     * Toggle activity status (active/inactive only, not draft)
     */
    public function toggleStatus(Activity $activity)
    {
        if ($activity->status === 'draft') {
            return redirect()->back()->with('error', 'Les activités en brouillon ne peuvent pas être activées directement. L\'opérateur doit les finaliser.');
        }

        $newStatus = $activity->status === 'active' ? 'inactive' : 'active';
        $activity->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? 'Activité activée avec succès'
            : 'Activité désactivée avec succès';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Activity $activity)
    {
        $activity->update(['is_featured' => !$activity->is_featured]);

        $message = $activity->is_featured
            ? 'Activité mise en avant'
            : 'Activité retirée de la mise en avant';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Delete an activity (soft delete)
     */
    public function destroy(Activity $activity)
    {
        // Vérifier s'il y a des inscriptions confirmées
        $hasConfirmedRegistrations = $activity->registrations()
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if ($hasConfirmedRegistrations) {
            return redirect()->back()->with('error', 'Cette activité a des inscriptions confirmées et ne peut pas être supprimée.');
        }

        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activité supprimée avec succès');
    }

    /**
     * Display registrations for admin
     */
    public function registrations(Request $request): View
    {
        $query = ActivityRegistration::with(['activity.translations', 'activity.tourOperator.translations', 'appUser'])
            ->latest();

        // Filtres
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
        }

        if ($request->filled('tour_operator_id')) {
            $query->whereHas('activity', function ($q) use ($request) {
                $q->where('tour_operator_id', $request->tour_operator_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                    ->orWhere('guest_email', 'like', "%{$search}%")
                    ->orWhereHas('appUser', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $registrations = $query->paginate(20);

        // Liste des activités pour le filtre
        $activities = Activity::with('translations')
            ->orderBy('id', 'desc')
            ->get();

        // Liste des opérateurs pour le filtre
        $tourOperators = TourOperator::with('translations')
            ->where('is_active', true)
            ->get();

        // Statistiques
        $statistics = [
            'total' => ActivityRegistration::count(),
            'pending' => ActivityRegistration::where('status', 'pending')->count(),
            'confirmed' => ActivityRegistration::where('status', 'confirmed')->count(),
            'completed' => ActivityRegistration::where('status', 'completed')->count(),
            'cancelled' => ActivityRegistration::whereIn('status', ['cancelled_by_user', 'cancelled_by_operator'])->count(),
        ];

        return view('admin.activities.registrations', compact('registrations', 'activities', 'tourOperators', 'statistics'));
    }

    /**
     * Display a specific registration
     */
    public function showRegistration(ActivityRegistration $registration): View
    {
        $registration->load([
            'activity.translations',
            'activity.tourOperator.translations',
            'activity.featuredImage',
            'appUser',
        ]);

        return view('admin.activities.registration-show', compact('registration'));
    }
}
