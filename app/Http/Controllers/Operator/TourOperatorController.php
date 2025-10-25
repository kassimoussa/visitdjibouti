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
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator()->with(['logo', 'translations', 'media'])->first();

        return view('operator.tour-operator.edit', compact('user', 'tourOperator'));
    }

    /**
     * Mettre à jour les informations de l'opérateur
     */
    public function update(Request $request)
    {
        $user = Auth::guard('operator')->user();
        $tourOperator = $user->tourOperator;

        // Valider les données
        $validated = $request->validate([
            'phones' => 'nullable|array',
            'emails' => 'nullable|array',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo_id' => 'nullable|exists:media,id',
            'translations' => 'required|array',
            'translations.*.name' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        // Mettre à jour les informations du tour operator
        $tourOperator->update([
            'phones' => $validated['phones'] ?? [],
            'emails' => $validated['emails'] ?? [],
            'website' => $validated['website'] ?? null,
            'address' => $validated['address'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'logo_id' => $validated['logo_id'] ?? null,
        ]);

        // Mettre à jour les traductions
        foreach ($validated['translations'] as $locale => $translationData) {
            $tourOperator->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name' => $translationData['name'],
                    'description' => $translationData['description'] ?? '',
                ]
            );
        }

        return redirect()
            ->route('operator.tour-operator.show')
            ->with('success', 'Informations mises à jour avec succès.');
    }
}
