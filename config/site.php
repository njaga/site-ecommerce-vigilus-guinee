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
];
