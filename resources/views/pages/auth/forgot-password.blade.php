@extends('layouts.auth')
@section('title', 'Mot de passe oublié - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="auth-header">
            <h1 class="mb-05">Mot de passe oublié ?</h1>
            <p class="text-muted">Entrez votre email, nous vous enverrons un lien de réinitialisation.</p>
        </div>

        <form> 
            <div class="form-group">
                <label for="email">Email associé au compte</label>
                <input type="email" id="email" placeholder="nom@entreprise.com" required>
            </div>

            <a href="{{ route('login') }}" class="btn mb-1 text-center d-block w-100">Envoyer le lien</a>
        </form>

        <div class="text-center text-sm mt-1">
            <a href="{{ route('login') }}" class="text-muted">← Retour à la connexion</a>
        </div>
    </div>
</div>
@endsection