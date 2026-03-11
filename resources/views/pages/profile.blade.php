@extends('layouts.app')
@section('title', 'Mon Profil - Ticketing App')

@section('content')
<div class="container-narrow">
    <header class="page-header-simple">
        <h1>Mon Profil</h1>
        <p class="text-muted">Gérez vos informations personnelles.</p>
    </header>

    <div class="card d-flex align-center gap-1 mobile-col">
        <div class="avatar avatar-blue" style="width: 80px; height: 80px; font-size: 2rem;">
            {{ substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1) }}
        </div>
        <div>
            <h2 class="card-title-simple" style="margin-bottom: 0;">
                {{ $user['prenom'] }} {{ $user['nom'] }}
            </h2>
            <p class="text-muted">{{ $user['role'] }}</p>
        </div>
    </div>

    <div class="card">
        <h2 class="form-section-title">Informations de contact</h2>
        <form>
            <div class="d-flex gap-1 mobile-col">
                <div class="form-group flex-1">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="{{ $user['prenom'] }}" required>
                </div>
                <div class="form-group flex-1">
                    <label>Nom</label>
                    <input type="text" name="nom" value="{{ $user['nom'] }}" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ $user['email'] }}" required>
            </div>
            <div class="text-right">
                <button type="button" class="btn">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
@endsection