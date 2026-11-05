<?php

// Catégories = illustrations thématiques, pas des photos de fiches produit.
// Source Vigilus : dépôt institutionnel réutilisable sous contrôle éditorial.
// Source Unsplash : photos gratuites sous licence Unsplash, à valider avant publication.
$own = 'https://raw.githubusercontent.com/njaga/site-vigilus-securite/main/public/images/';

return [
    'category_rules' => [
        [
            'matches' => ['camera', 'cctv', 'surveillance', 'bullet', 'dome', 'ptz'],
            'visual' => [
                'url' => $own.'news/Cameras.jpg',
                'source' => 'Vigilus — bibliothèque groupe (public/images/news/Cameras.jpg)',
                'label' => 'Caméras de surveillance',
            ],
        ],
        [
            'matches' => ['nvr', 'dvr', 'enregistreur', 'stockage', 'disque', 'record'],
            'visual' => [
                'url' => 'https://images.unsplash.com/photo-1695668548342-c0c1ad479aee?w=900&q=82&fit=crop',
                'source' => 'Unsplash — Kevin Ache, photo-1695668548342-c0c1ad479aee',
                'label' => 'Infrastructure d’enregistrement et réseau',
            ],
        ],
        [
            'matches' => ['acces', 'badge', 'biometr', 'interph', 'porte', 'portier'],
            'visual' => [
                'url' => $own.'shared/electronique/controle-acces-badge.jpg',
                'source' => 'Vigilus — bibliothèque groupe (shared/electronique/controle-acces-badge.jpg)',
                'label' => 'Contrôle d’accès professionnel',
            ],
        ],
        [
            'matches' => ['alarme', 'sirene', 'detect', 'intrusion', 'capteur'],
            'visual' => [
                'url' => $own.'Alarme-securite-electronique-protection-domicile-et-service.webp',
                'source' => 'Vigilus — bibliothèque groupe (alarme sécurité électronique)',
                'label' => 'Dispositifs d’alarme',
            ],
        ],
        [
            'matches' => ['kit', 'pack', 'solution complete'],
            'visual' => [
                'url' => $own.'services/videosurveillance-equipements.jpg',
                'source' => 'Vigilus — bibliothèque groupe (videosurveillance-equipements.jpg)',
                'label' => 'Ensemble d’équipements de vidéosurveillance',
            ],
        ],
        [
            'matches' => ['reseau', 'switch', 'informatique', 'poe', 'cable', 'routeur'],
            'visual' => [
                'url' => 'https://images.unsplash.com/photo-1695668548342-c0c1ad479aee?w=900&q=82&fit=crop',
                'source' => 'Unsplash — Kevin Ache, infrastructure réseau',
                'label' => 'Équipements de réseau',
            ],
        ],
        [
            'matches' => ['barriere', 'vehicule', 'parking'],
            'visual' => [
                'url' => $own.'shared/electronique/barriere-automatique.webp',
                'source' => 'Vigilus — bibliothèque groupe (barriere-automatique.webp)',
                'label' => 'Système de barrière et gestion des accès',
            ],
        ],
    ],
    'default_category' => [
        'url' => $own.'services/videosurveillance-equipements.jpg',
        'source' => 'Vigilus — bibliothèque groupe (videosurveillance-equipements.jpg)',
        'label' => 'Équipements professionnels de sécurité électronique',
    ],
];
