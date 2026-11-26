<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Commentaire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Pagination : vue personnalisée commune à toutes les tables de l'application.
        Paginator::defaultView('vendor.pagination.sama');
        Paginator::defaultSimpleView('vendor.pagination.sama');

        // Badge Commentaires : nombre de retours en attente (partagé à toutes les vues)
        View::composer('layouts.navbarAdmi', function ($view) {
            $view->with('commentairesEnAttente', Commentaire::independant()
                ->where('statut', 'en_attente')
                ->count());
        });
    }
}
