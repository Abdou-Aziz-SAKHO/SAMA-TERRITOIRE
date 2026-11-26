<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Autorise l'accès au back-office.
     * Les deux rôles (ADMINISTRATEUR et SUPER_ADMINISTRATEUR) voient les mêmes pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        if ($user->isBloque()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Votre compte a été bloqué.',
            ]);
        }

        return $next($request);
    }
}
