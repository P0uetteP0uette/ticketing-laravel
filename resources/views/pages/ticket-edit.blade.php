@extends('layouts.app')
@section('title', 'Modifier le Ticket - Ticketing App')

@section('content')
<div class="container-narrow">
    <div class="page-header-simple">
        <a href="{{ route('ticket.show', $ticket->id) }}" class="link-back">← Annuler</a>
        <h1>Modifier le ticket #{{ $ticket->id }}</h1>
    </div>

    <div class="card">
        <form action="{{ route('ticket.update', $ticket->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="d-flex gap-1 mb-1 mobile-col">
                <div class="form-group flex-1">
                    <label for="statut">Statut du ticket</label>
                    <select id="statut" name="statut">
                        <option value="Nouveau" {{ $ticket->statut == 'Nouveau' ? 'selected' : '' }}>Nouveau</option>
                        <option value="En cours" {{ $ticket->statut == 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminé" {{ $ticket->statut == 'Terminé' ? 'selected' : '' }}>Terminé</option>
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="priorite">Priorité</label>
                    <select id="priorite" name="priorite">
                        <option value="Basse" {{ $ticket->priorite == 'Basse' ? 'selected' : '' }}>🟢 Basse</option>
                        <option value="Moyenne" {{ $ticket->priorite == 'Moyenne' ? 'selected' : '' }}>🟡 Normale</option>
                        <option value="Haute" {{ $ticket->priorite == 'Haute' ? 'selected' : '' }}>🔴 Haute</option>
                        <option value="Critique" {{ $ticket->priorite == 'Critique' ? 'selected' : '' }}>🔥 Critique</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Sujet de la demande *</label>
                <input type="text" id="title" name="titre" value="{{ $ticket->titre }}" required>
            </div>

            <div class="form-group">
                <label for="description">Description détaillée *</label>
                <textarea id="description" name="description" rows="6" required>{{ $ticket->description }}</textarea>
            </div>

            <div class="form-group">
                <label for="type">Type de demande</label>
                <select id="type" name="type">
                    <option value="inclus" {{ $ticket->type == 'inclus' ? 'selected' : '' }}>Correction de Bug (Inclus)</option>
                    <option value="facturable" {{ $ticket->type == 'facturable' ? 'selected' : '' }}>Nouvelle fonctionnalité (Facturable)</option>
                </select>
            </div>

            <div class="text-right mt-2">
                <button type="submit" class="btn btn-wide">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
@endsection