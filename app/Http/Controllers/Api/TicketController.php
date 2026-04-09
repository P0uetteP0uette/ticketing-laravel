<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Crée un nouveau ticket via l'API (Ajout rapide).
     */
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
            'message' => 'Le ticket a été créé avec succès.',
            'ticket' => $ticket
        ], 201);
    }

    /**
     * Récupère les données brutes d'un ticket spécifique.
     */
    public function show($id)
    {
        $ticket = Ticket::findOrFail($id);
        return response()->json($ticket);
    }
}