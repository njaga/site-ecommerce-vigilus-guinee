<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Demande de commande Vigilus</title></head>
<body style="font-family:Arial,sans-serif;color:#17212c;line-height:1.6">
    <div style="max-width:600px;margin:auto;padding:24px">
        <p style="font-weight:800;color:#c61d35">VIGILUS GUINÉE</p>
        <h1 style="font-size:24px">{{ $forTeam ? 'Nouvelle demande à traiter' : 'Votre demande de commande' }}</h1>
        <p>Référence : <strong>{{ $order->reference }}</strong></p>
        <p>Statut : <strong>{{ $order->statusLabel() }}</strong></p>
        @if($forTeam)
            <p>Client : {{ $order->customer_name }} — {{ $order->customer_phone }}<br>
            Ville : {{ $order->city }}</p>
        @endif
        <ul>
            @foreach($order->items as $item)
                <li>{{ $item->quantity }} × {{ $item->name_snapshot }} ({{ $item->sku_snapshot }})</li>
            @endforeach
        </ul>
        <p>Sous-total matériel : <strong>{{ number_format($order->subtotal_gnf, 0, ',', ' ') }} GNF</strong></p>
        @if($order->status === \App\Models\Order::CONFIRMED && $order->total_gnf !== null)
            <p>Livraison validée : {{ number_format($order->shipping_gnf, 0, ',', ' ') }} GNF<br>
            Total matériel + livraison : <strong>{{ number_format($order->total_gnf, 0, ',', ' ') }} GNF</strong></p>
        @else
            <p>Les frais de livraison et le montant final seront confirmés par notre équipe.</p>
        @endif
        @if($order->status === \App\Models\Order::CANCELLED)
            <p>La demande a été annulée. Contactez notre équipe si besoin.</p>
        @else
            <p>Il ne s'agit pas d'une confirmation de paiement. Le règlement se fera exclusivement en dehors du site, selon les modalités convenues avec Vigilus.</p>
        @endif
        <p><a href="{{ $trackingUrl }}" style="display:inline-block;background:#c61d35;color:#fff;padding:12px 16px;text-decoration:none;border-radius:6px">{{ $forTeam ? 'Ouvrir dans l’administration' : 'Consulter le suivi sécurisé' }}</a></p>
        @unless($forTeam)<p style="font-size:12px;color:#64748b">Ce lien privé expire après {{ config('commerce.tracking_link_days') }} jours. Ne le partagez pas.</p>@endunless
    </div>
</body>
</html>
