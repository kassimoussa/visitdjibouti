<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\TourOperatorUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Display the login form.
     */
    public function showLoginForm(): View
    {
        return view('operator.auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('operator')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::guard('operator')->user();

            // Verify user and tour operator are active
            if (!$user->is_active || !$user->tourOperator->is_active) {
                Auth::guard('operator')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Votre compte ou votre opérateur touristique a été désactivé.',
                ]);
            }

            // Record login timestamp
            $user->recordLogin();

            // Set user's preferred language
            session(['locale' => $user->preferred_language]);

            return redirect()->intended(route('operator.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('operator')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('operator.login');
    }

    /**
     * Display the password reset request form.
     */
    public function showForgotPasswordForm(): View
    {
        return view('operator.auth.forgot-password');
    }

    /**
     * Send a password reset link to the given operator user.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('tour_operator_users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display the password reset form.
     */
    public function showResetPasswordForm(string $token): View
    {
        return view('operator.auth.reset-password', ['token' => $token]);
    }

    /**
     * Reset the given user's password.
     */
    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::broker('tour_operator_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (TourOperatorUser $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('operator.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}