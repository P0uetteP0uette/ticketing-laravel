@extends('layouts.auth')
@section('title', 'Connexion - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="color: var(--primary-color); margin-bottom: 0.5rem;">Connexion</h1>
            <p class="text-muted">Accédez à votre espace de gestion.</p>
        </div>

        @if(session('success'))
            <div class="alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.authenticate') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>

                @error('email')
                    <span style="color: var(--danger, red); font-size: 0.85rem; margin-top: 5px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="d-flex" style="justify-content: space-between; align-items: center;">
                    <label for="password" style="margin:0;">Mot de passe</label>
                    <a href="{{ route('password.request') }}" tabindex="-1" style="font-size: 0.85rem; color: var(--primary-color);">Mot de passe oublié ?</a>
                </div>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-wide mb-1" style="width: 100%; display: block; margin-bottom: 1rem;">Se connecter</button>

            <div style="text-align: center; font-size: 0.9rem;">
                Pas encore de compte ? <a href="{{ route('register') }}" tabindex="-1" class="text-primary" style="font-weight: bold;">S'inscrire</a>
            </div>
        </form>
    </div>
</div>
@endsection