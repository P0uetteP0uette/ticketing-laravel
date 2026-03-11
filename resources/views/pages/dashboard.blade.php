@extends('layouts.app')

@section('title', 'Tableau de bord - Ticketing App')

@section('content')
    <header class="page-header">
        <h1>Bonjour, {{ $prenomUser }} 👋</h1>
        <p>Voici un aperçu de l'activité.</p>
    </header>

    <section class="d-flex gap-1">
        <div class="stat-card">
            <h2>Tickets Totaux</h2>
            <p class="stat-value text-primary">{{ $stats['tickets_totaux'] }}</p>
        </div>

        <div class="stat-card">
            <h2>Urgences</h2>
            <p class="stat-value text-warning">{{ $stats['urgences'] }}</p>
        </div>

        <div class="stat-card">
            <h2>Projets actifs</h2>
            <p class="stat-value text-success">{{ $stats['projets_actifs'] }}</p>
        </div>
    </section>
@endsection