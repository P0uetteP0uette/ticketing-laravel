@extends('layouts.app')
@section('title', 'Détail Projet - Ticketing App')

@section('content')
@php
    // Petits calculs directement dans Blade (comme tu faisais en PHP)
    $percent = ($project['heures_total'] > 0) ? ($project['heures_utilisees'] / $project['heures_total']) * 100 : 0;
    $heures_restantes = $project['heures_total'] - $project['heures_utilisees'];
    $hors_forfait = ($heures_restantes < 0) ? abs($heures_restantes) : 0;
@endphp

<div class="page-header-simple">
    <a href="{{ route('projects') }}" class="link-back">← Retour aux projets</a>
    <div class="header-flex mt-1">
        <div>
            <h1>{{ $project['nom'] }}</h1>
            <p class="text-muted">Client : <strong>{{ $project['client'] }}</strong> • {{ $project['description'] }}</p>
        </div>
        <div class="header-actions">
            @if(Auth::user()->role === 'Administrateur')
    
                <a href="{{ route('project.edit', $project['id']) }}" class="btn-modifier">✏️ Modifier</a>

                <form action="{{ route('project.destroy', $project['id']) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-supprimer">🗑️ Supprimer</button>
                </form>

            @endif

            <a href="{{ route('ticket.create') }}" class="btn btn-sm">➕ Nouveau Ticket</a>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="card">
        <h2 class="card-title-simple">Consommation Heures</h2>
        <div class="stat-value">
            {{ $project['heures_utilisees'] }}h <span class="stat-sub">/ {{ $project['heures_total'] }}h</span>
        </div>
        <div class="progress-container">
            <div class="progress-bar {{ $percent > 100 ? 'bg-red' : 'bg-green' }}" style="width: {{ min($percent, 100) }}%;"></div>
        </div>
        <p class="stat-label">
            @if ($heures_restantes >= 0)
                Il reste <strong>{{ $heures_restantes }}h</strong> dans le forfait.
            @else
                <span class="text-danger">Forfait dépassé de {{ abs($heures_restantes) }}h !</span>
            @endif
        </p>
    </div>

    <div class="card">
        <h2 class="card-title-simple">Hors Forfait (Facturable)</h2>
        <div class="stat-value text-warning">{{ $hors_forfait }}h</div>
        <p class="stat-label">Tickets validés en supplément.</p>
        <small class="text-muted">Taux horaire : {{ $project['taux'] }}€ / h</small>
    </div>
</div>

<div class="card card-no-padding">
    <div class="card-header-border"><h2>Derniers tickets du projet</h2></div>
    <div class="table-container">
        <table class="w-100">
            <thead><tr><th>ID</th><th>Sujet</th><th>Statut</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($project_tickets as $pt)
                <tr>
                    <td>#{{ $pt['id'] }}</td>
                    <td><strong>{{ $pt['titre'] }}</strong></td>
                    <td><span class="badge badge-gray">{{ $pt['statut'] }}</span></td>
                    <td><a href="{{ route('ticket.show', $pt['id']) }}" class="btn btn-sm btn-light">Voir</a></td>
                </tr>
                @empty
                <tr><td colspan='4'>Aucun ticket pour ce projet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection