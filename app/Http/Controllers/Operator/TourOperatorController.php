<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TourOperatorController extends Controller
{
    /**
     * Afficher les informations de l'opérateur
     */
    public function show(): View
    {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator()->with(['logo', 'translations', 'users'])->first();

        $statistics = [
            'total_events' => $user->managedEvents()->count(),
            'total_reservations' => $user->managedReservations()->count(),
            'total_tours' => $user->managedTours()->count(),
            'total_revenue' => $user->managedReservations()
                ->where('status', 'confirmed')
                ->sum('payment_amount'),
        ];

        return view('operator.tour-operator.show', compact('user', 'tourOperator', 'statistics'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(): View
    {
        return view('operator.tour-operator.edit');
    }
}
