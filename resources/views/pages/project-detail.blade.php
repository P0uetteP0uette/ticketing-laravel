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
            <div class="header-actions" style="display: flex; align-items: center; gap: 10px;">
                @if(Auth::user()->role === 'Administrateur')
                    <a href="{{ route('project.edit', $project['id']) }}" class="btn-modifier">
                        <span>✏️</span> Modifier
                    </a>

                    <form action="{{ route('project.destroy', $project['id']) }}" method="POST" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-supprimer" onclick="return confirm('Es-tu sûr de vouloir supprimer ce projet ?')">
                            <span>🗑️</span> Supprimer
                        </button>
                    </form>
                @endif
                
                <a href="{{ route('ticket.create') }}" class="btn btn-sm" style="display: inline-flex; align-items: center; gap: 5px; background-color: #2563eb;">
                    <span>➕</span> Nouveau Ticket
                </a>
            </div>
    </div>
</div>

{{-- 1. SECTION ADMIN : BILAN FINANCIER --}}
@if(Auth::user()->role === 'Administrateur')
    <div class="financial-dashboard" style="display: flex; gap: 20px; margin: 20px 0; flex-wrap: wrap;">
        <div style="background: #e2e8f0; padding: 15px; border-radius: 8px; flex: 1; min-width: 200px; border-left: 5px solid #64748b;">
            📊 <strong>Forfait Total</strong><br>
            <span style="font-size: 1.2rem;">{{ $project['heures_total'] }}h</span>
        </div>
        
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; flex: 1; min-width: 200px; border-left: 5px solid #28a745;">
            ⏳ <strong>Heures Restantes</strong> (Forfait)<br>
            <span style="font-size: 1.2rem;">{{ $project['heures_restantes'] }}h</span>
        </div>

        <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; flex: 1; min-width: 200px; border-left: 5px solid #ffc107;">
            💰 <strong>À Facturer</strong> (Évolutions)<br>
            <span style="font-size: 1.2rem;">{{ $project['heures_a_facturer'] }}h</span>
            <small style="display: block; margin-top: 5px;">Soit : {{ $project['heures_a_facturer'] * $project['taux'] }} € HT</small>
        </div>
    </div>
@endif

{{-- 2. SECTION GRAPHIQUE : CONSOMMATION --}}
@php
    $percent = ($project['heures_total'] > 0) ? ($project['heures_utilisees'] / $project['heures_total']) * 100 : 0;
@endphp

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.1rem; margin-bottom: 15px;">Consommation Globale</h2>
        <div style="font-size: 2rem; font-weight: bold; margin-bottom: 10px;">
            {{ $project['heures_utilisees'] }}h <span style="font-size: 1rem; color: #666;">/ {{ $project['heures_total'] }}h</span>
        </div>
        <div style="background: #eee; height: 10px; border-radius: 5px; overflow: hidden;">
            <div style="width: {{ min($percent, 100) }}%; height: 100%; background: {{ $percent > 100 ? '#dc3545' : '#28a745' }};"></div>
        </div>
    </div>

    <div class="card" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.1rem; margin-bottom: 15px;">Détail Hors Forfait</h2>
        <div style="font-size: 2rem; font-weight: bold; color: #f39c12;">
            {{ $project['heures_a_facturer'] }}h
        </div>
        <p style="color: #666; margin-top: 5px;">Temps passé sur les nouvelles fonctionnalités.</p>
    </div>
</div>

{{-- 3. TABLEAU DES TICKETS --}}
<div class="card card-no-padding" style="background: white; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
    <div style="padding: 15px 20px; border-bottom: 1px solid #eee; background: #f8f9fa;">
        <h2 style="font-size: 1.1rem;">Derniers tickets du projet</h2>
    </div>
    <div class="table-container">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f1f5f9; text-align: left;">
                    <th style="padding: 12px;">ID</th>
                    <th style="padding: 12px;">Sujet</th>
                    <th style="padding: 12px;">Statut</th>
                    <th style="padding: 12px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($project_tickets as $pt)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#{{ $pt['id'] }}</td>
                    <td style="padding: 12px;"><strong>{{ $pt['titre'] }}</strong></td>
                    <td style="padding: 12px;"><span class="badge" style="background: #e2e8f0; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">{{ $pt['statut'] }}</span></td>
                    <td style="padding: 12px;"><a href="{{ route('ticket.show', $pt['id']) }}" class="btn-voir" style="text-decoration: none; color: #007bff;">Voir</a></td>
                </tr>
                @empty
                <tr><td colspan='4' style="padding: 20px; text-align: center;">Aucun ticket pour ce projet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection