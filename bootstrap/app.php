<?php

use App\Services\SeoRedirector;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Uniquement les 404 ; ne jamais masquer un refus d'accès 403,
        // ni détourner une URL administrateur vers une page commerciale.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return app(SeoRedirector::class)->forNotFound($request);
        });
    })->create();
