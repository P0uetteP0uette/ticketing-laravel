<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// On importe nos modèles pour pouvoir s'en servir !
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\User;
use App\Models\TempsPasse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        // Le "?" remplace le if/else, si user connecté alors user->prenom, sinon "invité"
        $prenomUser = $user ? $user->prenom : "Invité";

        if($user->role === 'Administrateur'){
            $stats = [
                'tickets_totaux' => Ticket::count(),
                'urgences' => Ticket::whereIn('priorite', ['Haute', 'Critique'])->count(),
                'projets_actifs' => Project::count()
            ];
        } else {
            $stats = [
                'tickets_totaux' => Ticket::where('auteur_id', $user->id)->count(),
                'urgences' => Ticket::where('auteur_id', $user->id)->whereIn('priorite', ['Haute', 'Critique'])->count(),
                'projets_actifs' => Project::count()
            ];
        }

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
        $user = Auth::user();

        if($user->role === 'Administrateur'){
            $tickets_bdd = Ticket::with(['auteur'])->get();
        } else {
            $tickets_bdd = Ticket::where('auteur_id', $user->id)->with(['auteur'])->get();
        }
        
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
        
        $heures_total_forfait = $p->contrat->heures_incluses ?? 0;
        
        // 🎯 CALCUL 1 : Total de toutes les heures pointées (l'ancienne clé qui manquait)
        $total_general = $p->tickets->flatMap->tempsPasses->sum('duree_heures');

        // 🎯 CALCUL 2 : Heures à facturer (Uniquement Nouvelles fonctionnalités / Évolutions)
        $heures_a_facturer = $p->tickets
            ->whereIn('type', ['Nouvelle fonctionnalité', 'Évolution'])
            ->flatMap->tempsPasses
            ->sum('duree_heures');

        // 🎯 CALCUL 3 : Heures consommées sur le forfait (Bugs / Maintenance)
        $heures_consommees_forfait = $p->tickets
            ->whereNotIn('type', ['Nouvelle fonctionnalité', 'Évolution'])
            ->flatMap->tempsPasses
            ->sum('duree_heures');

        // 🎯 CALCUL 4 : Heures restantes sur le forfait
        $heures_restantes = max(0, $heures_total_forfait - $heures_consommees_forfait);

        $project = [
            'id' => $p->id,
            'nom' => $p->nom,
            'client' => $p->contrat->client->nom_entreprise ?? 'Inconnu',
            'description' => $p->description,
            'heures_total' => $heures_total_forfait,
            'heures_utilisees' => $total_general, // ✅ On remet cette clé pour corriger l'erreur !
            'heures_restantes' => $heures_restantes,
            'heures_a_facturer' => $heures_a_facturer,
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
        
        if(Auth::user()->role !== 'Administrateur' && $t->auteur_id !== Auth::id()){
            abort(403, "Accès refusé : Ce ticket est privé et ne vous appartient pas");
        }

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
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, "Accès refusé : vous n'êtes pas admin");
        }
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
        // C'est l'utilisateur connecté qui devient l'auteur du ticket
        $user = Auth::user();

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
        // C'est l'utilisateur connecté qui pointe ses heures
        $user = Auth::user();

        // On enregistre le temps passé
        TempsPasse::create([
            'duree_heures' => $request->duree,
            'ticket_id' => $id,
            'user_id' => $user->id
        ]);

        // On recharge la page du ticket avec un message de succès
        return redirect()->route('ticket.show', $id);
    }

    // --- MODIFIER UN PROJET ---
    public function editProject($id)
    {
        if (Auth::user()->role !== 'Administrateur'){
            abort(403, 'Accès refusé : Seul un administrateur peut modifier un projet');
        }

        $project = Project::findOrFail($id); // On récupère le projet à modifier
        $clients = Client::all(); // On a besoin des clients pour le menu déroulant
        return view('pages.project-edit', compact('project', 'clients'));
    }

    public function updateProject(Request $request, $id)
    {
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, ('Accès refusé'));
        }

        $project = Project::findOrFail($id);
        
        // La magie d'Eloquent : on met à jour uniquement ce qui a changé
        $project->update([
            'nom' => $request->nom,
            'description' => $request->description,
        ]);

        // On renvoie l'utilisateur sur la page de détail du projet
        return redirect()->route('project.show', $id);
    }

    // --- MODIFIER UN TICKET ---
    public function editTicket($id)
    {
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, 'Accès refusé : Seul un admin peut modifier un ticket');
        }

        $ticket = Ticket::findOrFail($id);
        $projets = Project::all();
        return view('pages.ticket-edit', compact('ticket', 'projets'));
    }

    public function updateTicket(Request $request, $id)
    {
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, 'Accès refusé');
        }

        $ticket = Ticket::findOrFail($id);
        
        $ticket->update([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => $request->type,
            'priorite' => $request->priorite,
            'statut' => $request->statut // C'est ici qu'on peut enfin changer le statut !
        ]);

        return redirect()->route('ticket.show', $id);
    }

    // --- SUPPRIMER UN PROJET ---
    public function destroyProject($id)
    {
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, "Accès refusé : vous n'êtes pas administrateur");
        }
        $project = Project::findOrFail($id);
        $project->delete(); // La magie d'Eloquent : ça supprime aussi les tickets liés si tu as mis 'cascade' dans tes migrations !

        return redirect()->route('projects');
    }

    // --- SUPPRIMER UN TICKET ---
    public function destroyTicket($id)
    {
        if(Auth::user()->role !== 'Administrateur'){
            abort(403, "Accès refusé : vous n'êtes pas administrateur");
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('tickets');
    }

    public function storeUser(Request $request)
    {
        // On crée le nouvel utilisateur dans la BDD
        $user = User::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Utilisateur'
        ]);

        // On le redirige vers la page login
        return redirect()->route('login')->with('success', "Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.");
    }

    public function authenticate(Request $request)
    {
        // 1. On récupère l'email et le mot de passe tapés par l'utilisateur
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Auth::attempt va faire le sale boulot : chercher l'email en BDD et comparer le mot de passe
        if (Auth::attempt($credentials)) {
            // Succès ! On regénère la session (sécurité Laravel)
            $request->session()->regenerate();
            
            // On le redirige vers son tableau de bord
            return redirect()->route('dashboard'); 
        }

        // 3. Échec (mauvais mot de passe ou email inconnu) : on le renvoie en arrière avec un message d'erreur
        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email'); // onlyInput permet de garder l'email tapé dans la case pour ne pas tout retaper
    }

    public function logout(Request $request)
    {
        Auth::logout(); // On déconnecte l'utilisateur
        
        // On nettoie la session par sécurité
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // On le renvoie vers la page de connexion
        return redirect()->route('login');
    }

    public function profile() 
    { 
        // On récupère l'utilisateur connecté
        $user = Auth::user();
        return view('pages.profile', compact('user')); 
    }
    
    public function login()
    {
        // Si l'utilisateur est déjà connecté, on le dégage vers le dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('pages.auth.login'); // Vérifie bien le chemin de ton fichier .blade.php
    }

    public function validateQuote($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Sécurité : Seul l'auteur du ticket (le client) ou un admin peut valider
        if (Auth::user()->role !== 'Administrateur' && $ticket->auteur_id !== Auth::id()) {
            abort(403, "Vous n'avez pas l'autorisation de valider ce devis.");
        }

        // On change le statut du ticket
        $ticket->update([
            'statut' => 'En cours'
        ]);

        return redirect()->back()->with('success', 'Le devis a été accepté, les travaux commencent !');
    }

    public function settings() { return view('pages.settings'); }
    public function register() { return view('pages.auth.register'); }
    public function forgotPassword() { return view('pages.auth.forgot-password'); }
}