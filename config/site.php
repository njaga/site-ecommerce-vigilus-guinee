<?php

return [
    'country' => env('SITE_COUNTRY', 'GN'),
    'currency' => env('SITE_CURRENCY', 'GNF'),
    'city' => env('SITE_CITY', 'Conakry'),
    'phone' => env('SITE_PHONE', '+224613824242'),
    'phone_secondary' => env('SITE_PHONE_SECONDARY', '+224613824243'),
    'address' => env('SITE_ADDRESS', 'Conakry, Guinée'),
    'email' => env('SITE_EMAIL', 'contact@vigilus-guinee.com'),
    'whatsapp' => env('SITE_WHATSAPP', '+224613824242'),
    'legal_name' => env('SITE_LEGAL_NAME', 'Vigilus GN'),
    'legal_address' => env('SITE_LEGAL_ADDRESS', 'Conakry'),
    'legal_registration' => env('SITE_LEGAL_REGISTRATION', ''),
    'legal_form' => env('SITE_LEGAL_FORM', 'SAS'),
    'legal_capital' => env('SITE_LEGAL_CAPITAL', '500000000 GNF'),
    'legal_tax_id' => env('SITE_LEGAL_TAX_ID', ''),
    'publication_director' => env('SITE_PUBLICATION_DIRECTOR', ''),
    'hosting_name' => env('SITE_HOSTING_NAME', 'o2switch'),
    'hosting_address' => env('SITE_HOSTING_ADDRESS', ''),
    'hosting_contact' => env('SITE_HOSTING_CONTACT', ''),
    'privacy_providers_notice' => env('SITE_PRIVACY_PROVIDERS_NOTICE', ''),
    'legal_updated_at' => env('SITE_LEGAL_UPDATED_AT', '2026-11-16'),
    'privacy_contact' => env('SITE_PRIVACY_EMAIL', ''),
    'privacy_approved' => (bool) env('SITE_PRIVACY_APPROVED', false),
    'sales_approved' => (bool) env('SITE_SALES_APPROVED', false),
    'privacy_retention_notice' => env('SITE_PRIVACY_RETENTION_NOTICE', ''),
    'delivery_terms' => env('SITE_DELIVERY_TERMS', ''),
    'returns_terms' => env('SITE_RETURNS_TERMS', ''),
    'warranty_terms' => env('SITE_WARRANTY_TERMS', ''),
    // Logo officiel du site public de Vigilus Groupe SA : à héberger localement
    // après validation de la charte, avant mise en production.
    'brand_logo_url' => env('SITE_BRAND_LOGO_URL')
        ?: 'https://www.groupevigilus.com/media/site/logo.png',
    'hero_illustration_url' => env('SITE_HERO_ILLUSTRATION_URL')
        ?: 'https://www.groupevigilus.com/media/services/videosurveillance.jpg',

];
