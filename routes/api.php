<?php

use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

// On remet le vigile officiel Sanctum !
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
});