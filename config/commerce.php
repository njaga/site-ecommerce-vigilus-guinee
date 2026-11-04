<?php

return [
    // Notifications commerciales : renseigner uniquement une boîte de confiance locale.
    'order_alert_email' => env('ORDER_ALERT_EMAIL', ''),
    'tracking_link_days' => 30,
    // Le site ne collecte aucun paiement en ligne.
    'online_payments_enabled' => false,
];
