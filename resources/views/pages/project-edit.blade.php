@extends('layouts.app')
@section('title', 'Modifier le Projet - Ticketing App')

@section('content')
<div class="container-narrow">
    <div class="page-header-simple">
        <a href="{{ route('project.show', $project->id) }}" class="link-back">← Annuler</a>
        <h1>Modifier le projet : {{ $project->nom }}</h1>
    </div>

    <div class="card">
        <form action="{{ route('project.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="form-group mb-1">
                <label for="name">Nom du projet *</label>
                <input type="text" id="name" name="nom" value="{{ $project->nom }}" required>
            </div>

            <div class="form-group">
                <label for="description">Contexte / Description</label>
                <textarea id="description" name="description" rows="3">{{ $project->description }}</textarea>
            </div>

            <div class="form-actions text-right mt-2">
                <button type="submit" class="btn btn-wide">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
@endsection