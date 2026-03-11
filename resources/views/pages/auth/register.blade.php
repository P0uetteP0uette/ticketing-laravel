@extends('layouts.auth')
@section('title', 'Inscription - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Créer un compte</h1>
            <p class="text-muted">Rejoignez l'équipe pour gérer vos tickets.</p>
        </div>

        <form>
            <div class="d-flex gap-1 mobile-col">
                <div class="form-group flex-1">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" required>
                </div>
                <div class="form-group flex-1">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email professionnel</label>
                <input type="email" id="email" placeholder="nom@entreprise.com" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" placeholder="••••••••" required>
            </div>

            <a href="{{ route('login') }}" class="btn mb-1 text-center" style="display: block;">S'inscrire</a>
            
            <div style="text-align: center; font-size: 0.9rem;">
                Déjà un compte ? <a href="{{ route('login') }}" class="text-primary" style="font-weight: bold;">Se connecter</a>
            </div>
        </form>
    </div>
</div>
@endsection