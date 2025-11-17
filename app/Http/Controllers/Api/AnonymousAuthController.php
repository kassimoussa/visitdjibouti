<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AnonymousAuthController extends Controller
{
    /**
     * Créer un utilisateur anonyme et retourner un token d'accès.
     */
    public function createAnonymous(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'nullable|string|max:255',
                'preferred_language' => 'nullable|string|in:fr,en,ar',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Vérifier si un utilisateur anonyme existe déjà avec ce device_id
            $deviceId = $request->device_id;
            if ($deviceId) {
                $existingUser = AppUser::findByDeviceId($deviceId);
                if ($existingUser) {
                    // Retourner l'utilisateur existant avec un nouveau token
                    $token = $existingUser->createToken('anonymous-access')->plainTextToken;

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Utilisateur anonyme existant récupéré',
                        'data' => [
                            'user' => $existingUser->toArray(),
                            'token' => $token,
                            'anonymous_id' => $existingUser->anonymous_id,
                            'is_existing' => true,
                        ],
                    ]);
                }
            }

            // Créer un nouvel utilisateur anonyme
            $user = AppUser::createAnonymous($deviceId);

            // Appliquer la langue préférée si fournie
            if ($request->preferred_language) {
                $user->update(['preferred_language' => $request->preferred_language]);
            }

            // Créer un token d'accès
            $token = $user->createToken('anonymous-access')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur anonyme créé avec succès',
                'data' => [
                    'user' => $user->toArray(),
                    'token' => $token,
                    'anonymous_id' => $user->anonymous_id,
                    'is_existing' => false,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de l\'utilisateur anonyme',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne',
            ], 500);
        }
    }

    /**
     * Récupérer un utilisateur anonyme par son anonymous_id.
     */
    public function getAnonymous(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'anonymous_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'anonymous_id requis',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = AppUser::findByAnonymousId($request->anonymous_id);

            if (! $user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur anonyme non trouvé',
                ], 404);
            }

            // Créer un nouveau token pour cette session
            $token = $user->createToken('anonymous-access')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur anonyme récupéré',
                'data' => [
                    'user' => $user->toArray(),
                    'token' => $token,
                    'anonymous_id' => $user->anonymous_id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la récupération de l\'utilisateur',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne',
            ], 500);
        }
    }

    /**
     * Convertir un utilisateur anonyme en utilisateur complet.
     */
    public function convertToComplete(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user->isAnonymous()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'L\'utilisateur n\'est pas anonyme',
                ], 400);
            }

            // Vérifier d'abord si l'email existe déjà
            if ($request->email && AppUser::where('email', $request->email)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cet email est déjà utilisé. Veuillez vous connecter avec cet email ou utiliser un autre email.',
                    'error_code' => 'EMAIL_ALREADY_EXISTS',
                    'suggestion' => 'login',
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:app_users,email',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|string|in:male,female,other',
                'city' => 'nullable|string|max:255',
                'country' => 'nullable|string|max:255',
                'conversion_source' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'city' => $request->city,
                'country' => $request->country,
                'provider' => 'email',
            ];

            $source = $request->conversion_source ?? 'manual_registration';

            if ($user->convertToComplete($userData, $source)) {
                // Révoquer les anciens tokens et créer un nouveau token pour l'utilisateur complet
                $user->tokens()->delete();
                $token = $user->createToken('app-access')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Compte converti avec succès',
                    'data' => [
                        'user' => $user->fresh()->toArray(),
                        'token' => $token,
                    ],
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la conversion du compte',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la conversion',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne',
            ], 500);
        }
    }

    /**
     * Mettre à jour les préférences d'un utilisateur anonyme.
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user->isAnonymous()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette route est uniquement pour les utilisateurs anonymes',
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'preferred_language' => 'nullable|string|in:fr,en,ar',
                'push_notifications_enabled' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Données invalides',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $updateData = [];
            if ($request->has('preferred_language')) {
                $updateData['preferred_language'] = $request->preferred_language;
            }
            if ($request->has('push_notifications_enabled')) {
                $updateData['push_notifications_enabled'] = $request->push_notifications_enabled;
            }

            if (! empty($updateData)) {
                $user->update($updateData);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Préférences mises à jour',
                'data' => [
                    'user' => $user->fresh()->toArray(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour des préférences',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne',
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur anonyme et toutes ses données.
     */
    public function deleteAnonymous(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (! $user->isAnonymous()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cette route est uniquement pour les utilisateurs anonymes',
                ], 400);
            }

            // Supprimer les tokens
            $user->tokens()->delete();

            // Supprimer l'utilisateur (les favoris et réservations seront supprimés par cascade)
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur anonyme supprimé avec succès',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la suppression',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne',
            ], 500);
        }
    }
}
