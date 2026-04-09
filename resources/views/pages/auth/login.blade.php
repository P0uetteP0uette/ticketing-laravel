@extends('layouts.auth')
@section('title', 'Connexion - Ticketing App')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="auth-header">
            <h1 class="text-primary mb-05">Connexion</h1>
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
                    <span class="text-danger text-sm d-block mt-05">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="d-flex justify-between align-center">
                    <label for="password" class="m-0">Mot de passe</label>
                    <a href="{{ route('password.request') }}" tabindex="-1" class="text-sm text-primary">Mot de passe oublié ?</a>
                </div>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-wide w-100 d-block mb-1">Se connecter</button>

            <div class="text-center text-sm">
                Pas encore de compte ? <a href="{{ route('register') }}" tabindex="-1" class="text-primary font-bold">S'inscrire</a>
            </div>
        </form>
    </div>
</div>
@endsection