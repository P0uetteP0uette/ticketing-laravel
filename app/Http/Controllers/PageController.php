<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// On importe nos modèles pour pouvoir s'en servir !
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\User;
use App\Models\TempsPasse;

class PageController extends Controller
{
    public function dashboard()
    {
        // On compte les vraies lignes dans la BDD !
        $stats = [
            'tickets_totaux' => Ticket::count(),
            'urgences' => Ticket::whereIn('priorite', ['Haute', 'Critique'])->count(),
            'projets_actifs' => Project::count()
        ];
        
        // On récupère le premier utilisateur (notre Admin créé par le Seeder)
        $user = User::first();
        $prenomUser = $user ? $user->prenom : "Admin";

        return view('pages.dashboard', compact('stats', 'prenomUser'));
    }

    public function projects()
    {
        // La magie d'Eloquent : On récupère les projets AVEC leurs contrats, clients, tickets et temps passés
        $projets_bdd = Project::with(['contrat.client', 'tickets.tempsPasses'])->get();
        
        // On reformate pour notre vue Blade
        $projects = $projets_bdd->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'client' => $p->contrat->client->nom_entreprise ?? 'Inconnu',
                'statut' => 'Actif',
                'heures_total' => $p->contrat->heures_incluses ?? 0,
                // On additionne automatiquement tout le temps passé sur tous les tickets de ce projet !
                'heures_utilisees' => $p->tickets->flatMap->tempsPasses->sum('duree_heures'),
            ];
        });

        return view('pages.projects', compact('projects'));
    }

    public function tickets()
    {
        // On récupère tous les tickets AVEC le nom du projet et de l'auteur
        $tickets_bdd = Ticket::with(['projet', 'auteur'])->get();
        
        $tickets = $tickets_bdd->map(function ($t) {
            return [
                'id' => $t->id,
                'titre' => $t->titre,
                'type' => $t->type,
                'auteur' => $t->auteur->prenom . ' ' . $t->auteur->nom,
                'statut' => $t->statut,
                'priorite' => $t->priorite,
            ];
        });

        return view('pages.tickets', compact('tickets'));
    }

    public function showProject($id)
    {
        $p = Project::with(['contrat.client', 'tickets.tempsPasses'])->findOrFail($id);
        
        $project = [
            'id' => $p->id,
            'nom' => $p->nom,
            'client' => $p->contrat->client->nom_entreprise ?? 'Inconnu',
            'description' => $p->description,
            'heures_total' => $p->contrat->heures_incluses ?? 0,
            'heures_utilisees' => $p->tickets->flatMap->tempsPasses->sum('duree_heures'),
            'taux' => $p->contrat->taux_horaire ?? 0
        ];

        $project_tickets = $p->tickets->map(function ($t) {
            return [
                'id' => $t->id,
                'titre' => $t->titre,
                'statut' => $t->statut,
            ];
        });

        return view('pages.project-detail', compact('project', 'project_tickets'));
    }

    public function showTicket($id)
    {
        $t = Ticket::with(['projet.contrat.client', 'auteur', 'tempsPasses'])->findOrFail($id);
        
        $ticket = [
            'id' => $t->id,
            'titre' => $t->titre,
            'description' => $t->description,
            'type' => $t->type,
            'statut' => $t->statut,
            'priorite' => $t->priorite,
            'client_nom' => $t->projet->contrat->client->nom_entreprise ?? 'Inconnu',
            'projet_nom' => $t->projet->nom ?? 'Inconnu',
            'date_creation' => $t->created_at->format('Y-m-d'),
            // On calcule le total des heures pointées sur ce ticket
            'temps_total_ticket' => $t->tempsPasses->sum('duree_heures')
        ];

        return view('pages.ticket-detail', compact('ticket'));
    }

    public function createProject()
    {
        $clients = Client::all()->toArray();
        return view('pages.project-create', compact('clients'));
    }

    public function createTicket()
    {
        $projets = Project::all()->toArray();
        return view('pages.ticket-create', compact('projets'));
    }

    public function storeProject(Request $request)
    {
        // 1. On crée le projet avec les données du formulaire
        Project::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'contrat_id' => 1 // On force le contrat 1 pour l'instant (car on n'a pas encore fait de formulaire de création de contrat)
        ]);

        // 2. On redirige vers la liste des projets
        return redirect()->route('projects');
    }

    public function storeTicket(Request $request)
    {
        // On récupère notre Admin de test pour simuler l'auteur
        $user = User::first();

        Ticket::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => $request->type,
            'priorite' => $request->priorite,
            'projet_id' => $request->projet_id,
            'auteur_id' => $user->id,
            'statut' => 'Nouveau'
        ]);

        return redirect()->route('tickets');
    }

    public function addTime(Request $request, $id)
    {
        $user = User::first();

        // On enregistre le temps passé
        TempsPasse::create([
            'duree_heures' => $request->duree,
            'ticket_id' => $id,
            'user_id' => $user->id
        ]);

        // On recharge la page du ticket avec un message de succès
        return redirect()->route('ticket.show', $id);
    }

    // Les vues qui n'ont pas besoin de la BDD pour s'afficher
    public function profile() { return view('pages.profile', ['user' => ['prenom'=>'Admin', 'nom'=>'Super', 'email'=>'admin@ticketing.app', 'role'=>'Administrateur']]); }
    public function settings() { return view('pages.settings'); }
    public function login() { return view('pages.auth.login'); }
    public function register() { return view('pages.auth.register'); }
    public function forgotPassword() { return view('pages.auth.forgot-password'); }
}