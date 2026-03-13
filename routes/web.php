<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// L'URL '/' appelle la méthode 'dashboard' du PageController
Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');

// L'URL '/projets' appelle la méthode 'projects'
Route::get('/projets', [PageController::class, 'projects'])->name('projects');

// L'URL '/tickets' appelle la méthode 'tickets'
Route::get('/tickets', [PageController::class, 'tickets'])->name('tickets');

Route::get('/profil', [PageController::class,'profile'])->name('profile');
Route::get('/parametres', [PageController::class,'settings'])->name('settings');

// Pages de création
Route::get('/projets/nouveau', [PageController::class,'createProject'])->name('project.create');
Route::get('/tickets/nouveau', [PageController::class,'createTicket'])->name('ticket.create');

// Les pages de détails (le {id} permet de capturer le numéro dans l'URL)
Route::get('/projets/{id}', [PageController::class, 'showProject'])->name('project.show');
Route::get('/tickets/{id}', [PageController::class, 'showTicket'])->name('ticket.show');

// Les pages d'authentification
Route::get('/login', [PageController::class, 'login'])->name('login');
Route::get('/register', [PageController::class, 'register'])->name('register');
Route::get('/forgot-password', [PageController::class, 'forgotPassword'])->name('password.request');

// Enregistrement des formulaires en BDD
Route::post('/projets/nouveau', [PageController::class, 'storeProject'])->name('project.store');
Route::post('/tickets/nouveau', [PageController::class, 'storeTicket'])->name('ticket.store');
Route::post('/tickets/{id}/temps', [PageController::class, 'addTime'])->name('ticket.addTime');

// --- MODIFICATION ---
// 1. Afficher les formulaires pré-remplis
Route::get('/projets/{id}/editer', [PageController::class, 'editProject'])->name('project.edit');
Route::get('/tickets/{id}/editer', [PageController::class, 'editTicket'])->name('ticket.edit');

// 2. Sauvegarder les modifications en BDD (Méthode PUT)
Route::put('/projets/{id}', [PageController::class, 'updateProject'])->name('project.update');
Route::put('/tickets/{id}', [PageController::class, 'updateTicket'])->name('ticket.update');

// --- SUPPRESSION ---
Route::delete('/projets/{id}', [PageController::class, 'destroyProject'])->name('project.destroy');
Route::delete('/tickets/{id}', [PageController::class, 'destroyTicket'])->name('ticket.destroy');