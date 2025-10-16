<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TourReservationController extends Controller
{
    /**
     * Display a listing of the user's tour reservations.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $reservations = TourReservation::where('app_user_id', $user->id)
            ->with(['tour.translations', 'tour.tourOperator.translations']) // Eager load tour and operator details
            ->latest() // Order by created_at descending
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reservations,
        ]);
    }

    /**
     * Store a newly created tour reservation in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Tour $tour)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'number_of_people' => 'required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:1000',
        ];

        if (! $user) {
            $rules['guest_name'] = 'required|string|max:255';
            $rules['guest_email'] = 'required|email|max:255';
            $rules['guest_phone'] = 'nullable|string|max:20';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $requestedSpots = $validatedData['number_of_people'];

        try {
            $tourReservation = DB::transaction(function () use ($tour, $validatedData, $user, $requestedSpots) {
                // Re-fetch the tour with a pessimistic lock to prevent race conditions
                $tour = Tour::lockForUpdate()->findOrFail($tour->id);

                if ($tour->available_spots < $requestedSpots) {
                    // Not enough spots, so we throw an exception to automatically rollback the transaction
                    // and it will be caught by the outer catch block.
                    throw new \Exception('Not enough available spots for the number of people requested.');
                }

                $reservationData = [
                    'tour_id' => $tour->id,
                    'number_of_people' => $requestedSpots,
                    'notes' => $validatedData['notes'] ?? null,
                    'status' => 'pending',
                ];

                if ($user) {
                    $reservationData['app_user_id'] = $user->id;
                } else {
                    $reservationData['guest_name'] = $validatedData['guest_name'];
                    $reservationData['guest_email'] = $validatedData['guest_email'];
                    $reservationData['guest_phone'] = $validatedData['guest_phone'] ?? null;
                }

                $tourReservation = TourReservation::create($reservationData);

                // Increment the participants count
                $tour->increment('current_participants', $requestedSpots);

                return $tourReservation;
            });

            return response()->json([
                'success' => true,
                'message' => 'Tour reservation request sent successfully. It is pending confirmation from the operator.',
                'reservation' => $tourReservation,
            ], 201);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'Not enough available spots for the number of people requested.') {
                 return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'available_spots' => $tour->refresh()->available_spots,
                ], 422);
            }

            // For other unexpected errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while processing your reservation.',
            ], 500);
        }
    }

    /**
     * Display the specified tour reservation.
     *
     * @param  \Illuminate\Http
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, TourReservation $reservation)
    {
        // Authorize that the user owns the reservation
        if ($request->user()->id !== $reservation->app_user_id) {
            return response()->json(['success' => false, 'message' => 'This action is unauthorized.'], 403);
        }

        // Eager load details for the response
        $reservation->load(['tour.translations', 'tour.tourOperator.translations']);

        return response()->json([
            'success' => true,
            'data' => $reservation,
        ]);
    }

    /**
     * Update the specified tour reservation in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, TourReservation $reservation)
    {
        // Authorize that the user owns the reservation
        if ($request->user()->id !== $reservation->app_user_id) {
            return response()->json(['success' => false, 'message' => 'This action is unauthorized.'], 403);
        }

        // Check if the reservation can be updated
        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This reservation cannot be updated.',
                'current_status' => $reservation->status,
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'number_of_people' => 'sometimes|required|integer|min:1|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        // Optional: Check for availability if number_of_people changes
        if (isset($validatedData['number_of_people'])) {
            // Placeholder for availability logic on the Tour model
            // e.g., check if $tour->isAvailableFor($validatedData['number_of_people'])
        }

        $reservation->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tour reservation successfully updated.',
            'reservation' => $reservation,
        ]);
    }

    /**
     * Cancel the specified tour reservation.
     *
     * @param  \Illuminate\Http
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(Request $request, TourReservation $reservation)
    {
        // Authorize that the user owns the reservation
        if ($request->user()->id !== $reservation->app_user_id) {
            return response()->json(['success' => false, 'message' => 'This action is unauthorized.'], 403);
        }

        // Check if the reservation can be cancelled (e.g., not already cancelled or past)
        if (! in_array($reservation->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'This reservation cannot be cancelled.',
                'current_status' => $reservation->status,
            ], 400);
        }

        $reservation->status = 'cancelled_by_user';
        $reservation->save();

        return response()->json([
            'success' => true,
            'message' => 'Tour reservation successfully cancelled.',
            'reservation' => $reservation,
        ]);
    }
}
