@extends('layouts.auth')
@section('title', 'Mot de passe oublié - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Mot de passe oublié ?</h1>
            <p class="text-muted">Entrez votre email, nous vous enverrons un lien de réinitialisation.</p>
        </div>

        <form> 
            <div class="form-group">
                <label for="email">Email associé au compte</label>
                <input type="email" id="email" placeholder="nom@entreprise.com" required>
            </div>

            <a href="{{ route('login') }}" class="btn mb-1 text-center" style="display: block;">Envoyer le lien</a>
        </form>

        <div style="text-align: center; font-size: 0.9rem; margin-top: 10px;">
            <a href="{{ route('login') }}" class="text-muted">← Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection