<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TourReservationController extends Controller
{
    /**
     * Store a newly created reservation in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tour_id' => 'required|exists:tours,id',
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255',
            'user_phone' => 'nullable|string|max:20',
            'participants_count' => 'required|integer|min:1',
            'special_requirements' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tour = Tour::find($request->tour_id);

        // Vérifier si le tour est actif
        if ($tour->status !== 'active') {
            return response()->json(['message' => 'Ce tour n\'est pas actif.'], 400);
        }

        // Vérifier si la date du tour n'est pas passée
        if ($tour->start_date < now()->toDateString()) {
            return response()->json(['message' => 'Ce tour est déjà passé.'], 400);
        }

        // Vérifier la disponibilité des places
        if ($tour->max_participants !== null) {
            $available_spots = $tour->max_participants - $tour->current_participants;
            if ($request->participants_count > $available_spots) {
                return response()->json(['message' => 'Nombre de places insuffisant.', 'available_spots' => $available_spots], 400);
            }
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'reservable_id' => $tour->id,
            'reservable_type' => Tour::class,
            'user_id' => auth()->id(), // Peut être null si l'utilisateur n'est pas connecté
            'user_name' => $request->user_name,
            'user_email' => $request->user_email,
            'user_phone' => $request->user_phone,
            'participants_count' => $request->participants_count,
            'status' => 'confirmed', // Ou 'pending' si une confirmation est requise
            'confirmation_number' => 'RES-' . strtoupper(Str::random(10)),
            'special_requirements' => $request->special_requirements,
        ]);

        // Mettre à jour le nombre de participants
        $tour->increment('current_participants', $request->participants_count);

        return response()->json([
            'message' => 'Réservation pour le tour effectuée avec succès.',
            'reservation' => $reservation
        ], 201);
    }
}