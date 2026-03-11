@extends('layouts.auth')
@section('title', 'Connexion - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Connexion</h1>
            <p class="text-muted">Accédez à votre espace de gestion.</p>
        </div>

        <form>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="admin@ticketing.app" required>
            </div>
            <div class="form-group">
                <div class="d-flex" style="justify-content: space-between; align-items: center;">
                    <label for="password" style="margin:0;">Mot de passe</label>
                    <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--primary-color);">Mot de passe oublié ?</a>
                </div>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <a href="{{ route('dashboard') }}" class="btn mb-1 text-center" style="display: block;">Se connecter</a>

            <div style="text-align: center; font-size: 0.9rem;">
                Pas encore de compte ? <a href="{{ route('register') }}" class="text-primary" style="font-weight: bold;">S'inscrire</a>
            </div>
        </form>
    </div>
</div>
@endsection