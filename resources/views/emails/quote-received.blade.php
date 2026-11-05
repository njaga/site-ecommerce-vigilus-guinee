<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>Demande de devis · Vigilus Guinée</title></head>
<body style="font-family:Arial,sans-serif;color:#17212c;line-height:1.65">
<div style="max-width:600px;margin:auto;padding:24px">
    <p style="font-weight:bold;color:#c61d35">VIGILUS GUINÉE</p>
    @if($forTeam)
        <h1 style="font-size:22px">Nouvelle demande de devis à examiner</h1>
        <p>Référence interne : <strong>#{{ $quote->id }}</strong></p>
        <p>Client : <strong>{{ $quote->name }}</strong><br>Téléphone : {{ $quote->phone }}<br>
            @if($quote->email)Email : {{ $quote->email }}<br>@endif
            @if($quote->company)Société : {{ $quote->company }}@endif</p>
        <p>Produit : {{ $quote->product?->sku ?: 'Demande générale' }} · Quantité : {{ $quote->quantity }}</p>
        @if($quote->message)<p>Besoin communiqué : {{ $quote->message }}</p>@endif
        <p><a href="{{ route('admin.quotes.index') }}#devis-{{ $quote->id }}">Consulter dans l'administration</a></p>
    @else
        <h1 style="font-size:22px">Votre demande a bien été reçue.</h1>
        <p>Bonjour {{ $quote->name }},</p>
        <p>Merci pour votre demande de devis #{{ $quote->id }}. Notre équipe commerciale examinera les informations transmises et reviendra vers vous pour confirmer la disponibilité, les tarifs et les conditions applicables.</p>
        <p>Aucun paiement n'est demandé ni collecté sur le site.</p>
    @endif
    <p style="font-size:12px;color:#607082">Vigilus Guinée · Message lié à une demande effectuée via le site.</p>
</div>
</body>
</html>
