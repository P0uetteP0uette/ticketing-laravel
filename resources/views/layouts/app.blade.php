<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ticketing App')</title>
    
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<button id="mobile-menu-btn" class="menu-btn"><span>&#8942;</span></button>

<div class="app-layout">
    
    <nav class="sidebar">
        <h2>Ticketing App</h2>
        <ul>
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">📊 Tableau de bord</a></li>
            <li><a href="{{ route('projects') }}" class="{{ request()->routeIs('projects') ? 'active' : '' }}">📁 Projets</a></li>
            <li><a href="{{ route('tickets') }}" class="{{ request()->routeIs('tickets') ? 'active' : '' }}">🎫 Tickets</a></li>
            <li><a href="#">👤 Mon Profil</a></li>
            <li><a href="#">⚙️ Paramètres</a></li>
        </ul>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

</div>

</body>
</html>