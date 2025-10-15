<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OperatorAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::guard('operator')->check()) {
            return redirect()->route('operator.login');
        }

        $user = Auth::guard('operator')->user();

        if (! $user->is_active) {
            Auth::guard('operator')->logout();

            return redirect()->route('operator.login')
                ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        // Verify tour operator is still active
        if (! $user->tourOperator || ! $user->tourOperator->is_active) {
            Auth::guard('operator')->logout();

            return redirect()->route('operator.login')
                ->withErrors(['email' => 'Votre opérateur touristique a été désactivé.']);
        }

        return $next($request);
    }
}
