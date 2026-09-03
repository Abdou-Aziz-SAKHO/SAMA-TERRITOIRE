<?php

use App\Http\Controllers\AdmiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartographieController;
use App\Http\Controllers\DonneesController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ActualiteController;
use App\Http\Controllers\UtilisateurController;
use App\Http\Controllers\Controller;

Route::get('/', function () {
    return view('welcome');
});
//    route pour les utlisateur simple
Route::get('/cartographie', [CartographieController::class, 'index'])->name('cartographie');
Route::get('/climat', [CartographieController::class, 'climat'])->name('climat');
Route::get('/statistique', [CartographieController::class, 'statistique'])->name('statistique');


// Route pour Les Administrateurs
Route::get('/Dashboard', [AdmiController::class, 'Index'])->name('Dashboard');
Route::get('/Donnees', [DonneesController::class, 'index'])->name('DonneesAdmi');

// Routes de création pour chaque entité
Route::post('/Donnees/Region', [DonneesController::class, 'storeRegion'])->name('donnees.storeRegion');
Route::post('/Donnees/Departement', [DonneesController::class, 'storeDepartement'])->name('donnees.storeDepartement');
Route::post('/Donnees/Commune', [DonneesController::class, 'storeCommune'])->name('donnees.storeCommune');
Route::post('/Donnees/Localite', [DonneesController::class, 'storeLocalite'])->name('donnees.storeLocalite');
Route::post('/Donnees/Secteur', [DonneesController::class, 'storeSecteur'])->name('donnees.storeSecteur');
Route::post('/Donnees/Infrastructure', [DonneesController::class, 'storeInfrastructure'])->name('donnees.storeInfrastructure');
// fin des routes de creation

// Routes de modification pour chaque entite (liaison de modele : {region} = instance Region)
Route::put('/Donnees/Region/{region}', [DonneesController::class, 'updateRegion'])->name('donnees.updateRegion');
Route::put('/Donnees/Departement/{departement}', [DonneesController::class, 'updateDepartement'])->name('donnees.updateDepartement');
Route::put('/Donnees/Commune/{commune}', [DonneesController::class, 'updateCommune'])->name('donnees.updateCommune');
Route::put('/Donnees/Localite/{localite}', [DonneesController::class, 'updateLocalite'])->name('donnees.updateLocalite');
Route::put('/Donnees/Secteur/{secteur}', [DonneesController::class, 'updateSecteur'])->name('donnees.updateSecteur');
Route::put('/Donnees/Infrastructure/{infrastructure}', [DonneesController::class, 'updateInfrastructure'])->name('donnees.updateInfrastructure');

// Routes de suppression pour chaque entite
Route::delete('/Donnees/Region/{region}', [DonneesController::class, 'destroyRegion'])->name('donnees.destroyRegion');
Route::delete('/Donnees/Departement/{departement}', [DonneesController::class, 'destroyDepartement'])->name('donnees.destroyDepartement');
Route::delete('/Donnees/Commune/{commune}', [DonneesController::class, 'destroyCommune'])->name('donnees.destroyCommune');
Route::delete('/Donnees/Localite/{localite}', [DonneesController::class, 'destroyLocalite'])->name('donnees.destroyLocalite');
Route::delete('/Donnees/Secteur/{secteur}', [DonneesController::class, 'destroySecteur'])->name('donnees.destroySecteur');
Route::delete('/Donnees/Infrastructure/{infrastructure}', [DonneesController::class, 'destroyInfrastructure'])->name('donnees.destroyInfrastructure');


// ── Documents (fichiers Excel / Word / PDF) ──
Route::post('/Donnees/Document', [DocumentController::class, 'store'])->name('donnees.storeDocument');
Route::get('/Documents/{document}/apercu', [DocumentController::class, 'preview'])->name('documents.preview');
Route::get('/Documents/{document}/telecharger', [DocumentController::class, 'download'])->name('documents.download');
Route::delete('/Documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

// ── Photos des infrastructures (affichage dans la consultation) ──
Route::get('/Photos/{photo}', [DonneesController::class, 'apercuPhoto'])->name('photos.apercu');

// ── Actualités (Admin) ──
Route::get('/ActualitesAdmi', [ActualiteController::class, 'index'])->name('ActualitesAdmi');
Route::post('/Actualites/Actualite', [ActualiteController::class, 'store'])->name('actualites.store');
Route::put('/Actualites/Actualite/{actualite}', [ActualiteController::class, 'update'])->name('actualites.update');
Route::delete('/Actualites/Actualite/{actualite}', [ActualiteController::class, 'destroy'])->name('actualites.destroy');
Route::get('/Actualites/Impact/{actualite}', [ActualiteController::class, 'impact'])->name('actualites.impact');
Route::put('/Actualites/Commentaire/{commentaire}/lue', [ActualiteController::class, 'marquerLue'])->name('actualites.marquerLue');
Route::delete('/Actualites/Commentaire/{commentaire}', [ActualiteController::class, 'supprimerCommentaire'])->name('actualites.supprimerCommentaire');

// Route d'analyse d'impact AVANT suppression : renvoie en JSON la liste
// de ce qui sera détruit (affiché dans l'écran de confirmation)
Route::get('/Donnees/Impact/{type}/{id}', [DonneesController::class, 'impact'])->name('donnees.impact');

// route pour creer une utlisateurs
Route::get('/Register', [AuthController::class, 'register'])->name('Register');
Route::post('/Register', [AuthController::class, 'registerAdmi'])->name('Register.post');

// ── Gestion des utilisateurs (page Admin) ──
Route::get('/UtilisateursAdmi', [UtilisateurController::class, 'index'])->name('UtilisateursAdmi');
Route::put('/Utilisateurs/{user}', [UtilisateurController::class, 'update'])->name('utilisateurs.update');
Route::put('/Utilisateurs/{user}/statut', [UtilisateurController::class, 'toggleStatut'])->name('utilisateurs.statut');


// Route pour se connecter et se deconnecter
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('login.post');

// Route mot de passe oublié
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password.post');

