@extends('layouts.app')
@section('title', 'Détail Ticket - Ticketing App')

@section('content')
<div class="page-header-simple">
    <a href="{{ route('tickets') }}" class="link-back">← Retour à la liste</a>
    <div class="header-flex mt-1">
    <h1>#{{ $ticket['id'] }} - {{ $ticket['titre'] }}</h1>
    <div class="d-flex align-center gap-1">
        <a href="{{ route('ticket.edit', $ticket['id']) }}" class="btn btn-sm btn-light">✏️ Modifier</a>
        
        <form action="{{ route('ticket.destroy', $ticket['id']) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket définitivement ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm" style="background-color: var(--danger); color: white; border: none; cursor: pointer;">🗑️ Supprimer</button>
        </form>
        
        @if($ticket['type'] === 'facturable')
            <span class="badge badge-red">Facturable</span>
        @else
            <span class="badge badge-gray">Inclus</span>
        @endif
    </div>
</div>
</div>

<div class="grid-2-1">
    <section class="col-main">
        <div class="card">
            <h2>Description de la demande</h2>
            <div class="ticket-description">
                <p>{!! nl2br(e($ticket['description'])) !!}</p>
            </div>
        </div>
    </section>

    <aside class="col-sidebar">
        @if ($ticket['type'] === 'facturable')
        <div class="card card-alert-orange">
            <h2>⚠️ Action requise</h2>
            <p class="mb-1">Ce ticket est hors forfait. Veuillez valider le devis.</p>
            <button class="btn bg-green mb-1 w-100">✅ Accepter le devis</button>
        </div>
        @endif

        <div class="card">
            <h2>Informations</h2>
            <ul class="info-list">
                <li><strong>Statut</strong> <span class="badge badge-yellow">{{ $ticket['statut'] }}</span></li>
                <li><strong>Priorité</strong> <span>{{ $ticket['priorite'] }}</span></li>
                <li><strong>Client</strong> <span>{{ $ticket['client_nom'] }}</span></li>
                <li><strong>Projet</strong> <span>{{ $ticket['projet_nom'] }}</span></li>
                <li><strong>Créé le</strong> <span>{{ date('d/m/Y', strtotime($ticket['date_creation'])) }}</span></li>
            </ul>
        </div>

        <div class="card">
            <h2>Suivi du temps</h2>
            
            <ul class="info-list">
                <li><strong>Temps total passé</strong> <span class="text-primary" style="font-weight: bold; font-size: 1.1rem;">{{ $ticket['temps_total_ticket'] }} h</span></li>
            </ul>

            <form action="{{ route('ticket.addTime', $ticket['id']) }}" method="POST" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                @csrf
                <label for="duree" style="font-size: 0.85rem; font-weight: bold; display: block; margin-bottom: 5px;">Pointer des heures :</label>
                <div class="d-flex gap-1">
                    <input type="number" step="0.5" min="0.5" id="duree" name="duree" placeholder="Ex: 1.5" required style="flex: 1; padding: 5px;">
                    <button type="submit" class="btn btn-sm">Ajouter</button>
                </div>
                <small class="text-muted" style="display: block; margin-top: 5px;">(Par tranches de 0.5h)</small>
            </form>
        </div>
    </aside>
</div>
@endsection