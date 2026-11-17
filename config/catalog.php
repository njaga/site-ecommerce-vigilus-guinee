<?php

return [
    // Catalogue complet local pour tester les grilles et fiches, sans vente réelle.
    // Désactivé hors de l'environnement local/de test et de localhost.
    'preview_enabled' => (bool) env('CATALOG_PREVIEW_ENABLED',
        env('APP_ENV', 'production') === 'local'),
];
