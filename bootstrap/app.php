<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Si user pas connecté, on le renvoie vers '/'
        $middleware->redirectGuestsTo('/');

        // Dit de vérifier si les requetes api viennent d'un domaine autorisé dans le .env
        $middleware->statefulApi(); // Si oui -> regarde le cookie

        // Si user connecté et tente d'aller sur une page 'guest', on renvoie vers dashboard
        $middleware->redirectUsersTo('/dashboard');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
