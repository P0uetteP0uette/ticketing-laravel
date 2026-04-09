<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\User;
use App\Models\TempsPasse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
    // ==========================================
    // TABLEAU DE BORD & LISTES
    // ==========================================

    public function dashboard()
    {
        $user = Auth::user();
        $prenomUser = $user ? $user->prenom : "Invité";

        if ($user->role === 'Administrateur') {
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
        $projets_bdd = Project::with(['contrat.client', 'tickets.tempsPasses'])->get();
        
        $projects = $projets_bdd->map(function ($p) {
            return [
                'id' => $p->id,
                'nom' => $p->nom,
                'client' => $p->contrat->client->nom_entreprise ?? 'Inconnu',
                'statut' => 'Actif',
                'heures_total' => $p->contrat->heures_incluses ?? 0,
                'heures_utilisees' => $p->tickets->flatMap->tempsPasses->sum('duree_heures'),
            ];
        });

        return view('pages.projects', compact('projects'));
    }

    public function tickets()
    {
        $user = Auth::user();

        if ($user->role === 'Administrateur') {
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

    // ==========================================
    // AFFICHAGE DÉTAILLÉ (SHOW)
    // ==========================================

    public function showProject($id)
    {
        $p = Project::with(['contrat.client', 'tickets.tempsPasses'])->findOrFail($id);
        
        $heures_total_forfait = $p->contrat->heures_incluses ?? 0;
        
        $total_general = $p->tickets->flatMap->tempsPasses->sum('duree_heures');

        $heures_a_facturer = $p->tickets
            ->whereIn('type', ['Nouvelle fonctionnalité', 'Évolution', 'facturable'])
            ->flatMap->tempsPasses
            ->sum('duree_heures');

        $heures_consommees_forfait = $p->tickets
            ->whereNotIn('type', ['Nouvelle fonctionnalité', 'Évolution', 'facturable'])
            ->flatMap->tempsPasses
            ->sum('duree_heures');

        $heures_restantes = max(0, $heures_total_forfait - $heures_consommees_forfait);

        $project = [
            'id' => $p->id,
            'nom' => $p->nom,
            'client' => $p->contrat->client->nom_entreprise ?? 'Inconnu',
            'description' => $p->description,
            'heures_total' => $heures_total_forfait,
            'heures_utilisees' => $total_general,
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
        
        if (Auth::user()->role !== 'Administrateur' && $t->auteur_id !== Auth::id()) {
            abort(403, "Accès refusé : Ce ticket ne vous appartient pas.");
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
            'temps_total_ticket' => $t->tempsPasses->sum('duree_heures')
        ];

        return view('pages.ticket-detail', compact('ticket'));
    }

    // ==========================================
    // CRÉATION & ENREGISTREMENT (CREATE/STORE)
    // ==========================================

    public function createProject()
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }
        $clients = Client::all();
        return view('pages.project-create', compact('clients'));
    }

    public function createTicket()
    {
        $projets = Project::all();
        return view('pages.ticket-create', compact('projets'));
    }

    public function storeProject(Request $request)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        Project::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'contrat_id' => 1 
        ]);

        return redirect()->route('projects')->with('success', 'Projet créé avec succès.');
    }

    public function storeTicket(Request $request)
    {
        Ticket::create([
            'titre' => $request->titre,
            'description' => $request->description,
            'type' => $request->type,
            'priorite' => $request->priorite,
            'projet_id' => $request->projet_id,
            'auteur_id' => Auth::id(),
            'statut' => 'Nouveau'
        ]);

        return redirect()->route('tickets')->with('success', 'Ticket créé avec succès.');
    }

    public function addTime(Request $request, $id)
    {
        TempsPasse::create([
            'duree_heures' => $request->duree,
            'ticket_id' => $id,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('ticket.show', $id)->with('success', 'Temps ajouté avec succès.');
    }

    // ==========================================
    // MODIFICATION (EDIT/UPDATE)
    // ==========================================

    public function editProject($id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        $project = Project::findOrFail($id);
        $clients = Client::all();
        
        return view('pages.project-edit', compact('project', 'clients'));
    }

    public function updateProject(Request $request, $id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        $project = Project::findOrFail($id);
        $project->update($request->only(['nom', 'description']));

        return redirect()->route('project.show', $id)->with('success', 'Projet mis à jour.');
    }

    public function editTicket($id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        $ticket = Ticket::findOrFail($id);
        $projets = Project::all();
        
        return view('pages.ticket-edit', compact('ticket', 'projets'));
    }

    public function updateTicket(Request $request, $id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        $ticket = Ticket::findOrFail($id);
        $ticket->update($request->only(['titre', 'description', 'type', 'priorite', 'statut']));

        return redirect()->route('ticket.show', $id)->with('success', 'Ticket mis à jour.');
    }

    public function validateQuote($id)
    {
        $ticket = Ticket::findOrFail($id);

        if (Auth::user()->role !== 'Administrateur' && $ticket->auteur_id !== Auth::id()) {
            abort(403, "Vous n'avez pas l'autorisation de valider ce devis.");
        }

        $ticket->update(['statut' => 'En cours']);

        return redirect()->back()->with('success', 'Le devis a été accepté, les travaux commencent !');
    }

    // ==========================================
    // SUPPRESSION (DESTROY)
    // ==========================================

    public function destroyProject($id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }
        
        Project::findOrFail($id)->delete();
        return redirect()->route('projects')->with('success', 'Projet supprimé.');
    }

    public function destroyTicket($id)
    {
        if (Auth::user()->role !== 'Administrateur') {
            abort(403, "Accès refusé : Droits d'administration requis.");
        }

        Ticket::findOrFail($id)->delete();
        return redirect()->route('tickets')->with('success', 'Ticket supprimé.');
    }

    // ==========================================
    // AUTHENTIFICATION & UTILISATEUR
    // ==========================================

    public function storeUser(Request $request)
    {
        User::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Utilisateur'
        ]);

        return redirect()->route('login')->with('success', "Compte créé avec succès. Vous pouvez vous connecter.");
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard'); 
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ==========================================
    // PAGES STATIQUES & PROFILS
    // ==========================================

    public function profile() 
    { 
        $user = Auth::user();
        return view('pages.profile', compact('user')); 
    }
    
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('pages.auth.login');
    }

    public function settings() { return view('pages.settings'); }
    public function register() { return view('pages.auth.register'); }
    public function forgotPassword() { return view('pages.auth.forgot-password'); }
}