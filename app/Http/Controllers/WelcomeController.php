<?php

namespace App\Http\Controllers;

use App\Models\Actualite;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Page d'accueil publique : présente SAMA TERRITOIRE, les modules de
     * navigation et les 6 dernières actualités (publiées les 6 derniers mois).
     */
    public function index()
    {
        $actualites = Actualite::with(['photos', 'infrastructure'])
            ->withCount('commentaires')
            ->where('date_publication', '>=', now()->subMonths(6))
            ->orderByDesc('date_publication')
            ->take(4)
            ->get();

        return view('welcome', compact('actualites'));
    }

    /**
     * Page publique listant toutes les actualités publiées (avec pagination).
     */
    public function toutesLesActualites()
    {
        $actualites = Actualite::with(['photos', 'infrastructure'])
            ->withCount('commentaires')
            ->whereNotNull('date_publication')
            ->orderByDesc('date_publication')
            ->paginate(9)
            ->withQueryString();

        return view('PageUser.actualites', compact('actualites'));
    }

    /**
     * Page « À propos » : présentation du projet SAMA TERRITOIRE.
     */
    public function apropos()
    {
        return view('PageUser.apropos');
    }
}