<?php

return [
    // Informations institutionnelles communiquées par Vigilus.
    'dahua_statement' => 'Le représentant officiel exclusif de Dahua en Guinée.',
    'dahua_logo' => '/images/Dahua_Technology_logo.svg.png',
    'certifications' => [
        ['name' => 'ISO 9001', 'image' => 'iso-9001.png', 'title' => 'La qualité au quotidien', 'description' => 'Structurer les méthodes de travail, améliorer les processus et suivre la satisfaction des clients pour des prestations plus régulières.', 'source' => 'https://www.iso.org/standards/popular/iso-9000-family'],
        ['name' => 'ISO 14001', 'image' => 'iso-14001.png', 'title' => 'Maîtriser notre impact', 'description' => 'Identifier les impacts environnementaux, mieux gérer les ressources et les déchets, et progresser dans la prévention de la pollution.', 'source' => 'https://www.iso.org/home/insights-news/resources/standards-explained.html'],
        ['name' => 'ISO 45001', 'image' => 'iso-45001.png', 'title' => 'Protéger les équipes', 'description' => 'Prévenir les accidents et les atteintes à la santé au travail grâce à l’identification des dangers et à la maîtrise des risques professionnels.', 'source' => 'https://www.iso.org/home/insights-news/resources/standards-explained.html'],
        ['name' => 'ISO 29001', 'image' => 'iso-29001.png', 'title' => 'Répondre aux exigences du secteur énergétique', 'description' => 'Renforcer le management de la qualité des fournisseurs de produits et de services destinés aux industries pétrolière, pétrochimique et du gaz naturel.', 'source' => 'https://www.iso.org/standard/67773.html'],
    ],
    'locations' => [
        ['code' => 'gn', 'name' => 'Guinée'],
        ['code' => 'sn', 'name' => 'Sénégal', 'url' => 'https://vigilus-facilities.com'],
        ['code' => 'ci', 'name' => 'Côte d’Ivoire', 'url' => 'https://groupevigilus.ci'],
        ['code' => 'sl', 'name' => 'Sierra Leone', 'url' => 'https://vigilus-facilities.sl'],
        ['code' => 'ae', 'name' => 'Dubaï', 'url' => 'https://vigilus-int.com'],
        ['code' => 'ml', 'name' => 'Mali'],
        ['code' => 'bj', 'name' => 'Bénin'],
    ],
    'subsidiaries' => [
        ['name' => 'Vigilus Properties', 'logo' => env('GROUP_PROPERTIES_LOGO_URL', ''), 'url' => 'https://vigilus-properties.sn'],
        ['name' => 'Vigilus Mobility', 'logo' => env('GROUP_MOBILITY_LOGO_URL', ''), 'url' => 'https://locationvoituredakar.com'],
        ['name' => 'Vigilus Facilities', 'logo' => env('GROUP_FACILITIES_LOGO_URL', ''), 'url' => 'https://vigilus-facilities.com'],
        ['name' => 'Spear', 'detail' => 'Confection de tenues', 'logo' => env('GROUP_SPEAR_LOGO_URL', '')],
    ],
];
