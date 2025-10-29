<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityController extends Controller
{
    /**
     * Display a listing of activities
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();
        $operatorId = $user->tour_operator_id;

        $query = Activity::with(['tourOperator', 'featuredImage', 'translations'])
            ->forOperator($operatorId)
            ->latest();

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty_level', $request->difficulty);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        $activities = $query->paginate(15);

        // Statistiques
        $statistics = [
            'draft' => Activity::forOperator($operatorId)->where('status', 'draft')->count(),
            'active' => Activity::forOperator($operatorId)->where('status', 'active')->count(),
            'inactive' => Activity::forOperator($operatorId)->where('status', 'inactive')->count(),
            'total' => Activity::forOperator($operatorId)->count(),
        ];

        return view('operator.activities.index', compact('activities', 'statistics'));
    }

    /**
     * Show the form for creating a new activity
     */
    public function create(): View
    {
        return view('operator.activities.create');
    }

    /**
     * Display the specified activity
     */
    public function show(Activity $activity): View
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'activité appartient à cet opérateur
        if ($activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette activité.');
        }

        $activity->load([
            'tourOperator',
            'featuredImage',
            'media',
            'translations',
            'createdBy',
        ]);

        // Statistiques des inscriptions
        $registrationStats = [
            'total' => $activity->registrations()->count(),
            'pending' => $activity->pendingRegistrations()->count(),
            'confirmed' => $activity->confirmedRegistrations()->count(),
            'completed' => $activity->registrations()->where('status', 'completed')->count(),
        ];

        return view('operator.activities.show', compact('activity', 'registrationStats'));
    }

    /**
     * Show the form for editing the specified activity
     */
    public function edit(Activity $activity): View
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'activité appartient à cet opérateur
        if ($activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette activité.');
        }

        $activity->load(['translations', 'featuredImage', 'media']);

        return view('operator.activities.edit', compact('activity'));
    }

    /**
     * Remove the specified activity from storage
     */
    public function destroy(Activity $activity)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'activité appartient à cet opérateur
        if ($activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette activité.');
        }

        // Vérifier s'il y a des inscriptions confirmées
        if ($activity->confirmedRegistrations()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer une activité avec des inscriptions confirmées.');
        }

        $activity->delete();

        return redirect()->route('operator.activities.index')
            ->with('success', 'Activité supprimée avec succès.');
    }

    /**
     * Toggle activity status (active/inactive)
     */
    public function toggleStatus(Activity $activity)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'activité appartient à cet opérateur
        if ($activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette activité.');
        }

        // Ne peut pas activer si c'est un brouillon
        if ($activity->status === 'draft') {
            return redirect()->back()->with('error', 'Veuillez compléter l\'activité avant de l\'activer.');
        }

        $newStatus = $activity->status === 'active' ? 'inactive' : 'active';
        $activity->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Activité activée avec succès.' : 'Activité désactivée avec succès.';

        return redirect()->back()->with('success', $message);
    }
}
