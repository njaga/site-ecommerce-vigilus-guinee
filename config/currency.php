<?php

return [
    // Unités de devise pour 1 GNF. Taux indicatifs fournis par Vigilus.
    'rates' => [
        'GNF' => 1,
        'USD' => (float) env('CURRENCY_USD_PER_GNF', 0.00011),
        'EUR' => (float) env('CURRENCY_EUR_PER_GNF', 0.00010),
    ],
    'updated_at' => env('CURRENCY_RATES_DATE', '2026-11-16'),
];
