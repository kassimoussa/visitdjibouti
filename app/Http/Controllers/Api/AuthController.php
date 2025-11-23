<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Mail\PasswordResetOtpMail;
use App\Models\AppUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:app_users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'preferred_language' => 'nullable|string|in:fr,en,ar',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = AppUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'preferred_language' => $request->preferred_language ?? 'fr',
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'provider' => 'email',
                'is_active' => true,
            ]);

            // Record login activity
            $user->recordLogin($request->ip());

            // Create token
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = AppUser::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated',
            ], 403);
        }

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        // Record login activity
        $user->recordLogin($request->ip());

        // Revoke existing tokens (optional - single device login)
        // $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user(),
            ],
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'preferred_language' => 'sometimes|string|in:fr,en,ar',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'city' => 'nullable|string|max:255',
            'push_notifications_enabled' => 'sometimes|boolean',
            'email_notifications_enabled' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $request->user();
            $user->update($request->only([
                'name', 'phone', 'preferred_language', 'date_of_birth',
                'gender', 'city', 'push_notifications_enabled',
                'email_notifications_enabled',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => [
                    'user' => $user->fresh(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 422);
        }

        try {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Revoke all tokens except current
            $currentTokenId = $request->user()->currentAccessToken()->id;
            $user->tokens()->where('id', '!=', $currentTokenId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password change failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password is incorrect',
            ], 422);
        }

        try {
            // Revoke all tokens
            $user->tokens()->delete();

            // Deactivate account instead of deleting (for data integrity)
            $user->update([
                'is_active' => false,
                'email' => 'deleted_'.time().'_'.$user->email, // Anonymize email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Account deletion failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:app_users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = AppUser::where('email', $request->email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }

            if (! $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is deactivated',
                ], 403);
            }

            // Generate 6-digit OTP
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

            // Generate token for web (backward compatibility)
            $token = Str::random(64);

            // Delete old tokens for this email
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Store new token with OTP
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'otp' => $otp, // Store OTP in plain text for mobile
                'attempts' => 0,
                'created_at' => now(),
            ]);

            // Send email with OTP for mobile users
            Mail::to($user->email)->send(new PasswordResetOtpMail(
                otp: $otp,
                email: $user->email,
                userName: $user->name
            ));

            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset email',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:app_users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get token from database
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (! $resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token',
                ], 400);
            }

            // Check if token is expired (60 minutes)
            if (now()->diffInMinutes($resetRecord->created_at) > 60) {
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Reset token has expired',
                ], 400);
            }

            // Verify token
            if (! Hash::check($request->token, $resetRecord->token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid reset token',
                ], 400);
            }

            // Update password
            $user = AppUser::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Delete token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Revoke all tokens for security
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Password reset failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password with OTP (for mobile users)
     */
    public function resetPasswordWithOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:app_users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Get OTP record from database
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->first();

            if (! $resetRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune demande de réinitialisation trouvée pour cet email',
                ], 404);
            }

            // Check if OTP is expired (15 minutes)
            if (now()->diffInMinutes($resetRecord->created_at) > 15) {
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Le code OTP a expiré. Veuillez demander un nouveau code.',
                    'error_code' => 'OTP_EXPIRED',
                ], 400);
            }

            // Check max attempts (3 attempts)
            if ($resetRecord->attempts >= 3) {
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Nombre maximum de tentatives dépassé. Veuillez demander un nouveau code.',
                    'error_code' => 'MAX_ATTEMPTS_EXCEEDED',
                ], 400);
            }

            // Verify OTP
            if ($request->otp !== $resetRecord->otp) {
                // Increment attempts
                DB::table('password_reset_tokens')
                    ->where('email', $request->email)
                    ->update(['attempts' => $resetRecord->attempts + 1]);

                $remainingAttempts = 3 - ($resetRecord->attempts + 1);

                return response()->json([
                    'success' => false,
                    'message' => "Code OTP incorrect. Il vous reste {$remainingAttempts} tentative(s).",
                    'error_code' => 'INVALID_OTP',
                    'remaining_attempts' => $remainingAttempts,
                ], 400);
            }

            // OTP is valid - update password
            $user = AppUser::where('email', $request->email)->first();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Delete OTP record
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            // Create new token for immediate login
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe réinitialisé avec succès',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Échec de la réinitialisation du mot de passe',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
