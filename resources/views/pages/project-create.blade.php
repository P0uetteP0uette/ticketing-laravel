@extends('layouts.app')
@section('title', 'Créer un Projet - Ticketing App')

@section('content')
<div class="container-narrow">
    <div class="page-header-simple">
        <a href="{{ route('projects') }}" class="link-back">← Annuler et retour</a>
        <h1>Créer un nouveau projet</h1>
        <p>Définissez le cadre contractuel et l'équipe assignée.</p>
    </div>

    <div class="card">
        <form action="{{ route('project.store') }}" method="POST">
        @csrf
            <h2 class="form-section-title">1. Identité du projet</h2>
            
            <div class="d-flex gap-1 mb-1 mobile-col">
                <div class="form-group flex-2">
                    <label for="name">Nom du projet *</label>
                    <input type="text" id="name" name="nom" placeholder="Ex: Refonte Site" required>
                </div>
                
                <div class="form-group flex-1">
                    <label for="client">Client *</label>
                    <select id="client" name="client_id" required>
                        <option value="" disabled selected>Choisir...</option>
                        @foreach($clients as $c)
                            <option value="{{ $c['id'] }}">{{ $c['nom_entreprise'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Contexte / Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>

            <hr class="divider">
            <h2 class="form-section-title">2. Contrat & Heures</h2>

            <div class="d-flex gap-1 mb-1 mobile-col">
                <div class="form-group flex-1">
                    <label for="hours">Volume d'heures inclus *</label>
                    <input type="number" id="hours" name="heures" placeholder="Ex: 50" min="0" required>
                </div>
                <div class="form-group flex-1">
                    <label for="rate">Taux horaire suppl. (€) *</label>
                    <input type="number" id="rate" name="taux" placeholder="Ex: 80" min="0" required>
                </div>
            </div>

            <div class="form-actions text-right mt-2">
                <button type="submit" class="btn btn-wide">Valider le projet</button>
            </div>
        </form>
    </div>
</div>
@endsection