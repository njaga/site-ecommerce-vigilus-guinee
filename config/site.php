<?php

return [
    'country' => env('SITE_COUNTRY', 'GN'),
    'currency' => env('SITE_CURRENCY', 'GNF'),
    'city' => env('SITE_CITY', 'Conakry'),
    'phone' => env('SITE_PHONE', ''),
    'address' => env('SITE_ADDRESS', ''),
    'email' => env('SITE_EMAIL', ''),
    'whatsapp' => env('SITE_WHATSAPP', ''),
    'legal_name' => env('SITE_LEGAL_NAME', ''),
    'legal_address' => env('SITE_LEGAL_ADDRESS', ''),
    'legal_registration' => env('SITE_LEGAL_REGISTRATION', ''),
    'privacy_contact' => env('SITE_PRIVACY_EMAIL', ''),
    'privacy_approved' => (bool) env('SITE_PRIVACY_APPROVED', false),
    'sales_approved' => (bool) env('SITE_SALES_APPROVED', false),
    'privacy_retention_notice' => env('SITE_PRIVACY_RETENTION_NOTICE', ''),
    'delivery_terms' => env('SITE_DELIVERY_TERMS', ''),
    'returns_terms' => env('SITE_RETURNS_TERMS', ''),
    'warranty_terms' => env('SITE_WARRANTY_TERMS', ''),
    // Identité temporairement servie par le dépôt officiel Vigilus, à empaqueter
    // sur le domaine local avant mise en production.
    'brand_logo_url' => env('SITE_BRAND_LOGO_URL')
        ?: 'https://www.groupevigilus.com/media/site/logo.png',
    'hero_illustration_url' => env('SITE_HERO_ILLUSTRATION_URL')
        ?: 'https://www.groupevigilus.com/media/realisations/covers/Videosurveillance.jpg',

];
