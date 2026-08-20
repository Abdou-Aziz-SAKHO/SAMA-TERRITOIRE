<?php

use App\Http\Controllers\AdmiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartographieController;
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
Route::get('/Register', [AuthController::class, 'register'])->name('Register');
Route::post('/Register', [AuthController::class, 'registerAdmi'])->name('Register.post');


// Route pour se connecter et se deconnecter
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'auth'])->name('login.post');

// Route mot de passe oublié
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password.post');

