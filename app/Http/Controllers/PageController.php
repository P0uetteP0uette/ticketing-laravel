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
        // On compte les vraies lignes dans la BDD !
        $stats = [
            'tickets_totaux' => Ticket::count(),
            'urgences' => Ticket::whereIn('priorite', ['Haute', 'Critique'])->count(),
            'projets_actifs' => Project::count()
        ];
        
        // On récupère l'utilisateur connecté !
        $user = Auth::user();
        $prenomUser = $user ? $user->prenom : "Invité";

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
        $project = Project::findOrFail($id); // On récupère le projet à modifier
        $clients = Client::all(); // On a besoin des clients pour le menu déroulant
        return view('pages.project-edit', compact('project', 'clients'));
    }

    public function updateProject(Request $request, $id)
    {
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
        $ticket = Ticket::findOrFail($id);
        $projets = Project::all();
        return view('pages.ticket-edit', compact('ticket', 'projets'));
    }

    public function updateTicket(Request $request, $id)
    {
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
        $project = Project::findOrFail($id);
        $project->delete(); // La magie d'Eloquent : ça supprime aussi les tickets liés si tu as mis 'cascade' dans tes migrations !

        return redirect()->route('projects');
    }

    // --- SUPPRIMER UN TICKET ---
    public function destroyTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('tickets');
    }

    public function storeUser(Request $request)
    {
        // 1. On crée le nouvel utilisateur dans la BDD
        $user = User::create([
            'prenom' => $request->prenom, // Attention: ton input dans le HTML doit bien avoir name="prenom"
            'nom' => $request->nom,       // name="nom"
            'email' => $request->email,   // name="email"
            'password' => Hash::make($request->password), // On crypte le mot de passe par sécurité !
            'role' => 'Utilisateur'       // Rôle par défaut
        ]);

        // 2. On connecte l'utilisateur automatiquement après son inscription
        Auth::login($user);

        // 3. On le redirige vers le tableau de bord
        return redirect()->route('dashboard'); // Change 'dashboard' si ta route s'appelle autrement
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
    
    public function settings() { return view('pages.settings'); }
    public function login() { return view('pages.auth.login'); }
    public function register() { return view('pages.auth.register'); }
    public function forgotPassword() { return view('pages.auth.forgot-password'); }
}