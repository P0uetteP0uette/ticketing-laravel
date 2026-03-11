@extends('layouts.app')
@section('title', 'Paramètres - Ticketing App')

@section('content')
<div class="container-narrow">
    <header class="page-header-simple">
        <h1>Paramètres de l'application</h1>
        <p class="text-muted">Personnalisez votre expérience.</p>
    </header>

    <form>
        <div class="card">
            <h2 class="form-section-title">Notifications</h2>
            <p class="text-muted mb-1">Choisissez quand vous souhaitez être alerté.</p>
            
            <div class="form-group">
                <label class="checkbox-pill mb-1">
                    <input type="checkbox" checked> M'avertir quand un ticket m'est assigné
                </label>
                <label class="checkbox-pill mb-1">
                    <input type="checkbox" checked> M'avertir lors d'une nouvelle réponse
                </label>
                <label class="checkbox-pill mb-1">
                    <input type="checkbox"> Recevoir le résumé hebdomadaire par email
                </label>
            </div>
        </div>

        <div class="card">
            <h2 class="form-section-title">Préférences générales</h2>
            <div class="form-group">
                <label>Langue de l'interface</label>
                <select>
                    <option value="fr" selected>Français</option>
                    <option value="en">English</option>
                </select>
            </div>
            <div class="text-right mt-1">
                <button type="button" class="btn">Sauvegarder les préférences</button>
            </div>
        </div>
    </form>
</div>
@endsection