# 🎫 Application de Gestion de Ticketing - Ticketing App

## 🚀 Présentation
Cette application permet à une société de services de centraliser les demandes clients, de suivre le temps passé par les collaborateurs et de gérer la facturation des tickets (inclus vs facturables).

## 🌟 Fonctionnalités

### 👥 Gestion des Rôles & Sécurité
- **Multi-rôles** : Distinction entre `Administrateur` et `Utilisateur`.
- **Protection des données** : Les utilisateurs ne peuvent voir que leurs propres tickets.
- **Sécurité** : Utilisation de Sanctum pour l'API et protection CSRF pour le Web.

### 💰 Gestion Contractuelle & Heures
- **Dashboard** : Affichage en temps réel des heures consommées, du solde d'heures restantes et du montant HT à facturer.
- **Cycle de Validation** : Les tickets hors forfait ("facturables") font l'objet d'une validation manuelle par le client avant leur mise en production.
- **Suivi du temps** : Agrégation automatique des entrées de temps par ticket et répercussion immédiate sur le contrat du projet.

### ⚡ API REST & Expérience Utilisateur (UX)
- **Sécurité API** : Authentification sécurisée des requêtes via Sanctum garantir l'intégrité des échanges.
- **Ajout Rapide** : Utilisation de la méthode `fetch()` pour l'ajout rapide de tickets sans rechargement de page.
- **Affichage Dynamique** : Mise à jour instantanée des données du tableau via le DOM JavaScript.
- **Design Responsive** : Menu latéral rétractable optimisé pour une utilisation sur mobile et tablette.

## 🛠️ Installation
1. Cloner le projet
2. Installer les dépendances : `composer install`
3. Configurer le fichier `.env` (Base de données et `SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000`)
4. Générer la clé : `php artisan key:generate`
5. Lancer les migrations : `php artisan migrate:fresh --seed`
6. Lancer le serveur : `php artisan serve`

## 🧪 Technologies utilisées
- **Backend** : Laravel 11 / PHP
- **Frontend** : Blade, CSS, JavaScript
- **Base de données** : MySQL (Migrations & Eloquent)
- **Authentification** : Laravel Breeze / Sanctum