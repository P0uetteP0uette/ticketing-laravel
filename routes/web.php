<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// =============================================================
// ROUTES PUBLIQUES (Accessibles par tout le monde)
// =============================================================
Route::middleware(['guest'])->group(function () {

    // Accueil / Connexion
    Route::get('/', [PageController::class, 'login'])->name('login');
    Route::post('/connexion', [PageController::class, 'authenticate'])->name('login.authenticate');
    Route::get('/login', [PageController::class,'login']);

    // Inscription
    Route::get('/register', [PageController::class, 'register'])->name('register');
    Route::post('/inscription', [PageController::class, 'storeUser'])->name('register.store');

    // Mot de passe oublié
    Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('password.request');
});

// =============================================================
// ROUTES PRIVÉES (Uniquement pour les utilisateurs connectés)
// =============================================================
Route::middleware(['auth'])->group(function () {

    // Dashboard & Profil
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', [PageController::class, 'profile'])->name('profile');
    Route::get('/parametres', [PageController::class, 'settings'])->name('settings');
    Route::post('/deconnexion', [PageController::class, 'logout'])->name('logout');

    // --- GESTION DES PROJETS ---
    Route::prefix('projets')->group(function () {
        Route::get('/', [PageController::class, 'projects'])->name('projects');
        Route::get('/nouveau', [PageController::class, 'createProject'])->name('project.create');
        Route::post('/nouveau', [PageController::class, 'storeProject'])->name('project.store');
        Route::get('/{id}', [PageController::class, 'showProject'])->name('project.show');
        Route::get('/{id}/editer', [PageController::class, 'editProject'])->name('project.edit');
        Route::put('/{id}', [PageController::class, 'updateProject'])->name('project.update');
        Route::delete('/{id}', [PageController::class, 'destroyProject'])->name('project.destroy');
    });

    // --- GESTION DES TICKETS ---
    Route::prefix('tickets')->group(function () {
        Route::get('/', [PageController::class, 'tickets'])->name('tickets');
        Route::get('/nouveau', [PageController::class, 'createTicket'])->name('ticket.create');
        Route::post('/nouveau', [PageController::class, 'storeTicket'])->name('ticket.store');
        Route::get('/{id}', [PageController::class, 'showTicket'])->name('ticket.show');
        Route::get('/{id}/editer', [PageController::class, 'editTicket'])->name('ticket.edit');
        Route::put('/{id}', [PageController::class, 'updateTicket'])->name('ticket.update');
        Route::delete('/{id}', [PageController::class, 'destroyTicket'])->name('ticket.destroy');
        Route::post('/{id}/temps', [PageController::class, 'addTime'])->name('ticket.addTime');
        Route::post('/{id}/validate', [PageController::class, 'validateQuote'])->name('ticket.validate');
    });

});