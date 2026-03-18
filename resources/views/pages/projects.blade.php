@extends('layouts.app')

@section('title', 'Mes Projets - Ticketing App')

@section('content')
    <header class="header-flex mb-2">
        <div>
            <h1>Liste des Projets</h1>
            <p class="text-muted">Suivi des contrats et des enveloppes d'heures.</p>
        </div>
        @if(Auth::user()->role === 'Administrateur')
            <a href="{{ route('project.create') }}" class="btn btn-sm btn-create">➕ Nouveau Projet</a>
        @endif
        </header>

    <div class="table-container">
        <table class="w-100">
            <thead>
                <tr>
                    <th>Nom du Projet</th>
                    <th>Client</th>
                    <th>Contrat (Heures)</th>
                    <th>Consommation</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $projet)
                    @php
                        // On calcule le pourcentage direct dans Blade
                        $percent = ($projet['heures_total'] > 0) ? ($projet['heures_utilisees'] / $projet['heures_total']) * 100 : 0;
                        $barColor = $percent >= 100 ? 'bg-red' : ($percent > 70 ? 'bg-orange' : 'bg-green');
                        $badgeClass = $projet['statut'] === 'Actif' ? 'badge-green' : 'badge-red';
                    @endphp

                    <tr>
                        <td data-label="Nom du Projet"><strong>{{ $projet['nom'] }}</strong></td>
                        <td data-label="Client">{{ $projet['client'] }}</td>
                        <td data-label="Contrat">{{ $projet['heures_total'] }}h / an</td>
                        <td data-label="Consommation" class="td-progress">
                            <div class="progress-info">
                                <strong>{{ $projet['heures_utilisees'] }}h</strong> utilisées ({{ round($percent) }}%)
                            </div>
                            <div class="progress-container sm">
                                <div class="progress-bar {{ $barColor }}" style="width: {{ min($percent, 100) }}%;"></div>
                            </div>
                        </td>
                        <td data-label="Statut"><span class="badge {{ $badgeClass }}">{{ $projet['statut'] }}</span></td>
                        <td data-label="Actions"><a href="{{ route('project.show', $projet['id']) }}" class="btn btn-sm btn-light">Détails</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection