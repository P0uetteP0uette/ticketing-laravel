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