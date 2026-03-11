<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function dashboard()
    {
        // Nos fausses données en attendant l'étape 6
        $stats = [
            'tickets_totaux' => 24,
            'urgences' => 3,
            'projets_actifs' => 5
        ];
        $prenomUser = "Admin";

        // On appelle la vue et on lui envoie les variables
        return view('pages.dashboard', compact('stats', 'prenomUser'));
    }

    public function projects()
    {
        return view('pages.projects');
    }

    public function tickets()
    {
        return view('pages.tickets');
    }
}