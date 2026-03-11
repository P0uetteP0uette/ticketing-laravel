<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

// L'URL '/' appelle la méthode 'dashboard' du PageController
Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');

// L'URL '/projets' appelle la méthode 'projects'
Route::get('/projets', [PageController::class, 'projects'])->name('projects');

// L'URL '/tickets' appelle la méthode 'tickets'
Route::get('/tickets', [PageController::class, 'tickets'])->name('tickets');