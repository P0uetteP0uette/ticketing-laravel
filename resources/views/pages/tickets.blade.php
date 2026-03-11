@extends('layouts.app')

@section('title', 'Liste des Tickets - Ticketing App')

@section('content')
    <header class="header-flex mb-2">
        <div>
            <h1>Liste des Tickets</h1>
            <p class="text-muted">Gérez les demandes et suivez l'avancement.</p>
        </div>
        <a href="#" class="btn btn-sm btn-create">➕ Nouveau Ticket</a>
    </header>

    <div class="table-container">
        <table class="w-100">
            <thead>
                <tr><th>ID</th><th>Sujet</th><th>Auteur</th><th>Statut</th><th>Priorité</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach ($tickets as $ticket)
                    @php
                        $statusClass = match($ticket['statut']) {
                            'Nouveau' => 'badge-blue',
                            'En cours' => 'badge-yellow',
                            'Terminé' => 'badge-green',
                            default => 'badge-gray'
                        };

                        $priorityClass = match($ticket['priorite']) {
                            'Haute', 'Critique' => 'badge-red',
                            'Moyenne' => 'badge-yellow',
                            default => 'badge-gray'
                        };
                    @endphp

                    <tr>
                        <td data-label="ID">#{{ $ticket['id'] }}</td>
                        <td data-label="Sujet">
                            <strong>{{ $ticket['titre'] }}</strong><br>
                            <span class="badge {{ $ticket['type'] === 'facturable' ? 'badge-red' : 'badge-gray' }}">{{ $ticket['type'] }}</span>
                        </td>
                        <td data-label="Auteur">{{ $ticket['auteur'] }}</td>
                        <td data-label="Statut"><span class="badge {{ $statusClass }}">{{ $ticket['statut'] }}</span></td>
                        <td data-label="Priorité"><span class="badge {{ $priorityClass }}">{{ $ticket['priorite'] }}</span></td>
                        <td data-label="Actions"><a href="#" class="btn btn-sm btn-light">Voir</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection