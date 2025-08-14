<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to the OAuth provider
     */
    public function redirectToProvider(string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not supported'
            ], 400);
        }

        try {
            $redirectUrl = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'redirect_url' => $redirectUrl
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('OAuth redirect error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'OAuth redirect failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle the OAuth callback
     */
    public function handleProviderCallback(string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not supported'
            ], 400);
        }

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
            
            // Chercher l'utilisateur existant avec ce provider
            $existingUser = AppUser::where('provider', $provider)
                                  ->where('provider_id', $socialUser->getId())
                                  ->first();

            if ($existingUser) {
                // Utilisateur existant, mettre à jour les informations
                $existingUser->update([
                    'name' => $socialUser->getName() ?? $existingUser->name,
                    'email' => $socialUser->getEmail() ?? $existingUser->email,
                    'avatar' => $socialUser->getAvatar() ?? $existingUser->avatar,
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);

                $user = $existingUser;
            } else {
                // Vérifier si un utilisateur avec cet email existe déjà
                $existingEmailUser = AppUser::where('email', $socialUser->getEmail())->first();
                
                if ($existingEmailUser) {
                    // Lier le compte social à l'utilisateur existant
                    $existingEmailUser->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar() ?? $existingEmailUser->avatar,
                        'last_login_at' => now(),
                        'last_login_ip' => request()->ip(),
                    ]);
                    
                    $user = $existingEmailUser;
                } else {
                    // Créer un nouvel utilisateur
                    $user = AppUser::create([
                        'name' => $socialUser->getName() ?? 'User',
                        'email' => $socialUser->getEmail(),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(), // Les comptes sociaux sont considérés comme vérifiés
                        'preferred_language' => 'fr', // Langue par défaut
                        'is_active' => true,
                        'last_login_at' => now(),
                        'last_login_ip' => request()->ip(),
                    ]);
                }
            }

            // Créer le token
            $token = $user->createToken('mobile-app-' . $provider)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Social authentication successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'provider' => $provider
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OAuth callback error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Social authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle mobile app OAuth (with access token)
     * For mobile apps that handle OAuth flow themselves
     */
    public function authenticateWithToken(Request $request, string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not supported'
            ], 400);
        }

        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // Utiliser le token d'accès pour obtenir les informations de l'utilisateur
            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->access_token);
            
            // Même logique que dans handleProviderCallback
            $existingUser = AppUser::where('provider', $provider)
                                  ->where('provider_id', $socialUser->getId())
                                  ->first();

            if ($existingUser) {
                // Utilisateur existant
                $existingUser->update([
                    'name' => $socialUser->getName() ?? $existingUser->name,
                    'email' => $socialUser->getEmail() ?? $existingUser->email,
                    'avatar' => $socialUser->getAvatar() ?? $existingUser->avatar,
                    'last_login_at' => now(),
                    'last_login_ip' => $request->ip(),
                ]);

                $user = $existingUser;
            } else {
                // Vérifier l'email existant
                $existingEmailUser = AppUser::where('email', $socialUser->getEmail())->first();
                
                if ($existingEmailUser) {
                    // Lier le compte social
                    $existingEmailUser->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar() ?? $existingEmailUser->avatar,
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);
                    
                    $user = $existingEmailUser;
                } else {
                    // Nouveau utilisateur
                    $user = AppUser::create([
                        'name' => $socialUser->getName() ?? 'User',
                        'email' => $socialUser->getEmail(),
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(),
                        'preferred_language' => 'fr',
                        'is_active' => true,
                        'last_login_at' => now(),
                        'last_login_ip' => $request->ip(),
                    ]);
                }
            }

            // Créer le token
            $token = $user->createToken('mobile-app-' . $provider)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Social authentication successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'provider' => $provider
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('OAuth token authentication error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Social authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unlink social account
     */
    public function unlinkSocialAccount(Request $request, string $provider): JsonResponse
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            return response()->json([
                'success' => false,
                'message' => 'Provider not supported'
            ], 400);
        }

        $user = $request->user();

        // Vérifier si l'utilisateur a un mot de passe (pour pouvoir se connecter autrement)
        if (!$user->password && $user->provider === $provider) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot unlink the only authentication method. Please set a password first.'
            ], 422);
        }

        try {
            $user->update([
                'provider' => $user->provider === $provider ? 'email' : $user->provider,
                'provider_id' => $user->provider === $provider ? null : $user->provider_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' account unlinked successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlink social account',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's linked social accounts
     */
    public function getLinkedAccounts(Request $request): JsonResponse
    {
        $user = $request->user();

        $linkedAccounts = [
            'email' => !empty($user->password),
            'google' => $user->provider === 'google' || !empty($user->google_id),
            'facebook' => $user->provider === 'facebook' || !empty($user->facebook_id),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'linked_accounts' => $linkedAccounts,
                'primary_provider' => $user->provider ?? 'email'
            ]
        ]);
    }
}