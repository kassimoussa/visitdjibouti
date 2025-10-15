<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOperatorIsActive
{
    /**
     * Handle an incoming request.
     * Vérifie que l'utilisateur opérateur est actif.
     * Note: Le système de permissions granulaires a été supprimé.
     * Tous les utilisateurs opérateurs actifs ont maintenant un accès complet.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('operator')->user();

        if (! $user) {
            return redirect()->route('operator.login');
        }

        // Vérifier que le compte est actif
        if (! $user->is_active) {
            Auth::guard('operator')->logout();

            return redirect()->route('operator.login')
                ->withErrors(['error' => 'Votre compte a été désactivé. Veuillez contacter votre administrateur.']);
        }

        return $next($request);
    }
}
