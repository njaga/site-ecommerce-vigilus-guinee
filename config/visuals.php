<?php

// Catégories = illustrations thématiques, pas des photos de fiches produit.
// Source Vigilus : visuels du site institutionnel public, avec validation avant diffusion.
// Source Unsplash : photos gratuites sous licence Unsplash, à valider avant publication.
$own = 'https://www.groupevigilus.com/media/site/group/';

return [
    'category_rules' => [
        [
            'matches' => ['camera', 'cctv', 'surveillance', 'bullet', 'dome', 'ptz'],
            'visual' => [
                'url' => 'https://www.groupevigilus.com/media/services/videosurveillance.jpg',
                'source' => 'Vigilus Groupe SA — site institutionnel /media/services/videosurveillance.jpg',
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
                'url' => 'https://images.unsplash.com/photo-1754494977436-a5c202306fe4?w=900&q=82&fit=crop',
                'source' => 'Unsplash — Jakub Żerdzicki, photo-1754494977436-a5c202306fe4, lecteur de badge',
                'label' => 'Contrôle d’accès professionnel',
            ],
        ],
        [
            'matches' => ['alarme', 'sirene', 'detect', 'intrusion', 'capteur'],
            'visual' => [
                'url' => $own.'electronic.jpg',
                'source' => 'Vigilus Groupe SA — site institutionnel /media/site/group/electronic.jpg',
                'label' => 'Dispositifs d’alarme',
            ],
        ],
        [
            'matches' => ['kit', 'pack', 'solution complete'],
            'visual' => [
                'url' => 'https://www.groupevigilus.com/media/services/videosurveillance.jpg',
                'source' => 'Vigilus Groupe SA — site institutionnel /media/services/videosurveillance.jpg',
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
                'url' => 'https://images.unsplash.com/photo-1754494977436-a5c202306fe4?w=900&q=82&fit=crop',
                'source' => 'Unsplash — Jakub Żerdzicki, photo-1754494977436-a5c202306fe4, lecteur de badge',
                'label' => 'Système de barrière et gestion des accès',
            ],
        ],
    ],
    'default_category' => [
        'url' => 'https://www.groupevigilus.com/media/services/videosurveillance.jpg',
        'source' => 'Vigilus Groupe SA — site institutionnel /media/services/videosurveillance.jpg',
        'label' => 'Équipements professionnels de sécurité électronique',
    ],
];
