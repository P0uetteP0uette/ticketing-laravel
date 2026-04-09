<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // --- 1. LE POST (Créer) ---
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'priorite' => 'required|string',
            'projet_id' => 'required|exists:projects,id',
        ]);

        $ticket = Ticket::create([
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? '',
            'type' => $validated['type'],
            'priorite' => $validated['priorite'],
            'projet_id' => $validated['projet_id'],
            'auteur_id' => Auth::id(),
            'statut' => 'Nouveau'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket créé sans rechargement !',
            'ticket' => $ticket
        ], 201);
    }

    // --- 2. LE GET (Lire) ---
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        // On renvoie juste les données brutes du ticket
        return response()->json($ticket);
    }
}