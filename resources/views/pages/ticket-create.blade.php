@extends('layouts.app')
@section('title', 'Créer un Ticket - Ticketing App')

@section('content')
<div class="container-narrow">
    <div class="page-header-simple">
        <a href="{{ route('tickets') }}" class="link-back">← Annuler et retour</a>
        <h1>Ouvrir un nouveau ticket</h1>
    </div>

    <div class="card">
        <form>
            <div class="d-flex gap-1 mb-1 mobile-col">
                <div class="form-group flex-1">
                    <label for="project">Projet concerné *</label>
                    <select id="project" name="projet_id" required>
                        <option value="" disabled selected>Choisir un projet...</option>
                        @foreach($projets as $p)
                            <option value="{{ $p['id'] }}">{{ $p['nom'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group flex-1">
                    <label for="priority">Priorité</label>
                    <select id="priority" name="priorite">
                        <option value="Basse">🟢 Basse</option>
                        <option value="Moyenne" selected>🟡 Normale</option>
                        <option value="Haute">🔴 Haute</option>
                        <option value="Critique">🔥 Critique</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Sujet de la demande *</label>
                <input type="text" id="title" name="titre" required>
            </div>

            <div class="form-group">
                <label for="description">Description détaillée *</label>
                <textarea id="description" name="description" rows="6" required></textarea>
            </div>

            <div class="form-group">
                <label for="type">Type de demande</label>
                <select id="type" name="type">
                    <option value="inclus">Correction de Bug (Inclus)</option>
                    <option value="facturable">Nouvelle fonctionnalité (Facturable)</option>
                </select>
            </div>

            <div class="text-right mt-2">
                <button type="button" class="btn btn-wide">Créer le ticket</button>
            </div>
        </form>
    </div>
</div>
@endsection