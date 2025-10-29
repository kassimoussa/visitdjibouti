<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Mail\ActivityRegistrationCancelled;
use App\Mail\ActivityRegistrationConfirmed;
use App\Models\Activity;
use App\Models\ActivityRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ActivityRegistrationController extends Controller
{
    /**
     * Display a listing of registrations
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('operator')->user();
        $operatorId = $user->tour_operator_id;

        $query = ActivityRegistration::with(['activity', 'appUser'])
            ->whereHas('activity', function ($q) use ($operatorId) {
                $q->where('tour_operator_id', $operatorId);
            })
            ->latest();

        // Filtres
        if ($request->filled('activity_id')) {
            $query->where('activity_id', $request->activity_id);
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

        $registrations = $query->paginate(20);

        // Liste des activités pour le filtre
        $activities = Activity::forOperator($operatorId)
            ->with('translations')
            ->get();

        // Statistiques
        $statistics = [
            'total' => ActivityRegistration::whereHas('activity', function ($q) use ($operatorId) {
                $q->where('tour_operator_id', $operatorId);
            })->count(),
            'pending' => ActivityRegistration::whereHas('activity', function ($q) use ($operatorId) {
                $q->where('tour_operator_id', $operatorId);
            })->where('status', 'pending')->count(),
            'confirmed' => ActivityRegistration::whereHas('activity', function ($q) use ($operatorId) {
                $q->where('tour_operator_id', $operatorId);
            })->where('status', 'confirmed')->count(),
            'completed' => ActivityRegistration::whereHas('activity', function ($q) use ($operatorId) {
                $q->where('tour_operator_id', $operatorId);
            })->where('status', 'completed')->count(),
        ];

        return view('operator.activity-registrations.index', compact('registrations', 'activities', 'statistics'));
    }

    /**
     * Display the specified registration
     */
    public function show(ActivityRegistration $registration): View
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'inscription appartient à une activité de cet opérateur
        if ($registration->activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette inscription.');
        }

        $registration->load(['activity.translations', 'appUser']);

        return view('operator.activity-registrations.show', compact('registration'));
    }

    /**
     * Confirm a registration
     */
    public function confirm(ActivityRegistration $registration)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'inscription appartient à une activité de cet opérateur
        if ($registration->activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette inscription.');
        }

        if (! $registration->isPending()) {
            return redirect()->back()->with('error', 'Seules les inscriptions en attente peuvent être confirmées.');
        }

        // Vérifier la disponibilité
        if (! $registration->activity->hasAvailableSpots($registration->number_of_people)) {
            return redirect()->back()->with('error', 'Pas assez de places disponibles.');
        }

        $registration->confirm();

        // Envoyer email de confirmation au client
        try {
            $customerEmail = $registration->customer_email;
            if ($customerEmail) {
                Mail::to($customerEmail)->send(new ActivityRegistrationConfirmed($registration));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation d\'activité: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Inscription confirmée avec succès.');
    }

    /**
     * Cancel a registration
     */
    public function cancel(Request $request, ActivityRegistration $registration)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'inscription appartient à une activité de cet opérateur
        if ($registration->activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette inscription.');
        }

        if (! $registration->canBeCancelled()) {
            return redirect()->back()->with('error', 'Cette inscription ne peut pas être annulée.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $registration->cancel($request->cancellation_reason, 'operator');

        // Envoyer email d'annulation au client
        try {
            $customerEmail = $registration->customer_email;
            if ($customerEmail) {
                Mail::to($customerEmail)->send(new ActivityRegistrationCancelled($registration, 'operator'));
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email d\'annulation d\'activité: '.$e->getMessage());
        }

        return redirect()->back()->with('success', 'Inscription annulée avec succès.');
    }

    /**
     * Mark a registration as completed
     */
    public function complete(ActivityRegistration $registration)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'inscription appartient à une activité de cet opérateur
        if ($registration->activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette inscription.');
        }

        if (! $registration->isConfirmed()) {
            return redirect()->back()->with('error', 'Seules les inscriptions confirmées peuvent être marquées comme terminées.');
        }

        $registration->complete();

        return redirect()->back()->with('success', 'Inscription marquée comme terminée.');
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, ActivityRegistration $registration)
    {
        $user = Auth::guard('operator')->user();

        // Vérifier que l'inscription appartient à une activité de cet opérateur
        if ($registration->activity->tour_operator_id !== $user->tour_operator_id) {
            abort(403, 'Vous n\'avez pas accès à cette inscription.');
        }

        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded',
            'payment_method' => 'nullable|string|max:100',
        ]);

        $registration->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->back()->with('success', 'Statut de paiement mis à jour.');
    }
}
