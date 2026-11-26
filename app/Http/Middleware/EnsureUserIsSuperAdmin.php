<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSuperAdmin
{
    /**
     * Réservé au SUPER_ADMINISTRATEUR.
     * Disponible pour de futures actions strictement réservées (ex. création d'un Super Admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isSuperAdmin()) {
            abort(403, 'Accès réservé au Super Administrateur.');
        }

        return $next($request);
    }
}
