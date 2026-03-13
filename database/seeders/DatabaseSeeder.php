<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Models\Contrat;
use App\Models\Project;
use App\Models\Ticket;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. On crée notre Admin (pour pouvoir se connecter plus tard)
        $admin = User::create([
            'prenom' => 'Admin',
            'nom' => 'Super',
            'email' => 'admin@ticketing.app',
            'password' => Hash::make('secret'), // On hache le mot de passe comme tu le faisais !
            'role' => 'Administrateur',
        ]);

        // 2. On crée un Client
        $client = Client::create([
            'nom_entreprise' => 'Acme Corp',
        ]);

        // 3. On lui crée un Contrat
        $contrat = Contrat::create([
            'client_id' => $client->id,
            'heures_incluses' => 50,
            'taux_horaire' => 80.00,
        ]);

        // 4. On crée le Projet lié au contrat
        $projet = Project::create([
            'nom' => 'Refonte Site Web',
            'description' => 'Refonte complète du site vitrine et espace client.',
            'contrat_id' => $contrat->id,
        ]);

        // 5. On crée un Ticket lié à ce projet
        Ticket::create([
            'titre' => 'Bug affichage page accueil',
            'description' => 'Le bouton de contact ne fonctionne plus sur la version mobile.',
            'type' => 'inclus',
            'statut' => 'Nouveau',
            'priorite' => 'Haute',
            'projet_id' => $projet->id,
            'auteur_id' => $admin->id,
        ]);
    }
}