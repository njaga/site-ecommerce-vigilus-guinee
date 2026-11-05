<?php

return [
    // L'URL canonique de destination est APP_URL ; les domaines historiques
    // doivent être explicitement approuvés, sans jamais les deviner.
    'approved_legacy_hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', strtolower((string) env('SEO_APPROVED_LEGACY_HOSTS', ''))),
    ))),
    // Ne jamais activer un ancien domaine ivoirien pour la boutique de Guinée
    // sans une décision explicite et une cartographie inter-pays vérifiée.
    'default_indexable' => (bool) env('SEO_INDEXING_ENABLED', false),
];
