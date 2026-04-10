@extends('layouts.app')
@section('title', 'Détail Projet - Ticketing App')

@section('content')
<div class="page-header-simple">
    <a href="{{ route('projects') }}" class="link-back">← Retour aux projets</a>
    
    <div class="header-flex mt-1">
        <div>
            <h1>{{ $project['nom'] }}</h1>
            <p class="text-muted">
                Client : <strong>{{ $project['client'] }}</strong> 
                @if($project['description']) • {{ $project['description'] }} @endif
            </p>
        </div>
        <div class="header-actions align-center">
            @if(Auth::user()->role === 'Administrateur')
                <a href="{{ route('project.edit', $project['id']) }}" class="btn-modifier">
                    <span>✏️</span> Modifier
                </a>

                <form action="{{ route('project.destroy', $project['id']) }}" method="POST" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-supprimer" onclick="return confirm('Es-tu sûr de vouloir supprimer ce projet ?')">
                        <span>🗑️</span> Supprimer
                    </button>
                </form>
            @endif
            
            <a href="{{ route('ticket.create') }}" class="btn btn-sm d-flex align-center gap-05">
                <span>➕</span> Nouveau Ticket
            </a>
        </div>
    </div>
</div>

{{-- BILAN FINANCIER (Admin) --}}
@if(Auth::user()->role === 'Administrateur')
    <div class="dashboard-cards">
        <div class="card-stat stat-total">
            📊 <strong>Forfait Total</strong><br>
            <span class="stat-value">{{ $project['heures_total'] }}h</span>
        </div>
        
        <div class="card-stat stat-restant">
            ⏳ <strong>Heures Restantes</strong> (Forfait)<br>
            <span class="stat-value">{{ $project['heures_restantes'] }}h</span>
        </div>

        <div class="card-stat stat-facturer">
            💰 <strong>À Facturer</strong> (Évolutions)<br>
            <span class="stat-value">{{ $project['heures_a_facturer'] }}h</span>
            <small class="form-time-help">Soit : {{ $project['heures_a_facturer'] * $project['taux'] }} € HT</small>
        </div>
    </div>
@endif

{{-- CONSOMMATION --}}
@php
    $percent = ($project['heures_total'] > 0) ? ($project['heures_utilisees'] / $project['heures_total']) * 100 : 0;
@endphp

<div class="stats-grid-dynamic">
    <div class="card">
        <h2 class="form-section-title-sm mb-1">Consommation Globale</h2>
        <div class="stat-value-large">
            {{ $project['heures_utilisees'] }}h <span class="stat-value-muted">/ {{ $project['heures_total'] }}h</span>
        </div>
        <div class="progress-container md">
            <div style="width: {{ min($percent, 100) }}%; height: 100%; background: {{ $percent > 100 ? 'var(--danger)' : 'var(--success)' }};"></div>
        </div>
    </div>

    <div class="card">
        <h2 class="form-section-title-sm mb-1">Détail Hors Forfait</h2>
        <div class="stat-value-large text-warning">
            {{ $project['heures_a_facturer'] }}h
        </div>
        <p class="text-muted mt-1">Temps passé sur les nouvelles fonctionnalités.</p>
    </div>
</div>

{{-- FORMULAIRE API --}}
<div class="card card-api-form mb-2">
    <h3 class="form-section-title-sm mb-1">⚡ Ajout rapide</h3>
    
    <form id="quick-ticket-form" class="api-form-container">
        <input type="hidden" id="api-project-id" value="{{ $project['id'] }}">
        
        <div class="form-group-large">
            <input type="text" id="api-titre" placeholder="Sujet du ticket..." required class="form-control">
        </div>

        <div class="form-group-flex">
            <select id="api-type" class="form-control">
                <option value="inclus">Bug</option>
                <option value="facturable">Nouvelle fonctionnalité</option>
            </select>
        </div>

        <div class="form-group-flex">
            <select id="api-priorite" class="form-control">
                <option value="Basse">Basse</option>
                <option value="Moyenne" selected>Moyenne</option>
                <option value="Haute">Haute</option>
                <option value="Critique">Critique</option>
            </select>
        </div>

        <button type="submit" class="btn btn-sm">Ajouter</button>
    </form>
    <div id="api-message" class="api-msg"></div>
</div>

{{-- TABLEAU DES TICKETS --}}
<div class="card card-no-padding">
    <div class="table-header-custom">
        <h2 class="form-section-title-sm m-0">Derniers tickets du projet</h2>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sujet</th>
                    <th>Statut</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project_tickets as $pt)
                <tr>
                    <td>#{{ $pt['id'] }}</td>
                    <td><strong>{{ $pt['titre'] }}</strong></td>
                    <td><span class="badge badge-gray">{{ $pt['statut'] }}</span></td>
                    <td class="text-right">
                        <button onclick="viewTicketDetails({{ $pt['id'] }})" class="btn btn-sm btn-outline mr-1">👀 Aperçu API</button>
                        <a href="{{ route('ticket.show', $pt['id']) }}" class="btn btn-sm btn-light">Voir</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan='4' class="table-empty-state text-muted">Aucun ticket pour ce projet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/script.js') }}"></script>
@endsection