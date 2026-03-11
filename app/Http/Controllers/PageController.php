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
        // Fausse liste de projets pour tester la vue
        $projects = [
            ['id' => 1, 'nom' => 'Refonte Site Web', 'client' => 'Acme Corp', 'statut' => 'Actif', 'heures_total' => 50, 'heures_utilisees' => 35],
            ['id' => 2, 'nom' => 'Application Mobile', 'client' => 'Globex', 'statut' => 'Actif', 'heures_total' => 100, 'heures_utilisees' => 10],
            ['id' => 3, 'nom' => 'Audit Sécurité', 'client' => 'Initech', 'statut' => 'Épuisé', 'heures_total' => 20, 'heures_utilisees' => 20],
        ];

        return view('pages.projects', compact('projects'));
    }

    public function tickets()
    {
        // Fausse liste de tickets
        $tickets = [
            ['id' => 101, 'titre' => 'Bug affichage page accueil', 'type' => 'inclus', 'auteur' => 'Alice', 'statut' => 'Nouveau', 'priorite' => 'Haute'],
            ['id' => 102, 'titre' => 'Ajout bouton paiement', 'type' => 'facturable', 'auteur' => 'Bob', 'statut' => 'En cours', 'priorite' => 'Moyenne'],
            ['id' => 103, 'titre' => 'Mise à jour serveur', 'type' => 'inclus', 'auteur' => 'Charlie', 'statut' => 'Terminé', 'priorite' => 'Basse'],
        ];

        return view('pages.tickets', compact('tickets'));
    }

    public function profile()
    {
        // On simule un utilisateur connecté (au lieu du $_SESSION)
        $user = [
            'prenom' => 'Admin',
            'nom' => 'Super',
            'email' => 'admin@ticketing.fr',
            'role' => 'Administrateur'
        ];
        return view('pages.profile', compact('user'));
    }

    public function settings()
    {
        return view('pages.settings');
    }

    public function createProject()
    {
        // Fausse liste de clients pour le menu déroulant du formulaire
        $clients = [
            ['id' => 1, 'nom_entreprise' => 'Acme Corp'],
            ['id' => 2, 'nom_entreprise' => 'Globex']
        ];
        return view('pages.project-create', compact('clients'));
    }

    public function createTicket()
    {
        // Fausse liste de projets pour le menu déroulant
        $projets = [
            ['id' => 1, 'nom' => 'Refonte Site Web'],
            ['id' => 2, 'nom' => 'Application Mobile']
        ];
        return view('pages.ticket-create', compact('projets'));
    }

    public function login()
    {
        return view('pages.auth.login');
    }

    public function register()
    {
        return view('pages.auth.register');
    }

    public function forgotPassword()
    {
        return view('pages.auth.forgot-password');
    }
}