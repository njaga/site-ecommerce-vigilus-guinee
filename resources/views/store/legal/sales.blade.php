@extends('layouts.store')
@section('title', 'Conditions commerciales | Vigilus Guinée')
@section('robots', config('site.sales_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<section class="container-wide max-w-3xl py-14">
    <p class="kicker">Commande sans paiement en ligne</p><h1 class="text-4xl font-extrabold mt-3">Conditions commerciales</h1>
    @unless(config('site.sales_approved'))<p class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm">Projet de texte non validé : les conditions de vente, garanties, retours, fiscalité et livraison doivent être approuvés par l'exploitant avant lancement.</p>@endunless
    <div class="mt-8 space-y-8 text-quiet leading-relaxed">
        <section><h2 class="text-xl font-extrabold text-ink">1. Catalogue</h2>
            <p class="mt-3">Les références, prix publics et disponibilités présentés sur le site sont fournis à titre informatif et doivent être confirmés par l'équipe commerciale. Les produits « sur devis » ne donnent pas accès à une commande en ligne.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">2. Demande de commande</h2>
            <p class="mt-3">La validation d'un panier enregistre une demande. Elle ne déclenche aucun paiement, ne constitue pas une facture et n'engage pas une livraison avant confirmation commerciale.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">3. Livraison et règlement</h2>
            <p class="mt-3">{{ config('site.delivery_terms') ?: 'Les frais de livraison, la disponibilité, les délais et les modalités de règlement sont communiqués et approuvés séparément avant toute finalisation de commande.' }}</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">4. Garanties, retours et autres conditions</h2>
            <p class="mt-3"><strong class="text-ink">Retours :</strong> {{ config('site.returns_terms') ?: 'Conditions de retour à valider avant commercialisation.' }}
            <span class="block mt-3"><strong class="text-ink">Garanties :</strong> {{ config('site.warranty_terms') ?: 'Conditions de garantie à valider avant commercialisation.' }}</span></p></section>
        <section><h2 class="text-xl font-extrabold text-ink">5. Contact commercial</h2>
            <p class="mt-3">Pour une question ou une demande de conditions détaillées, contactez {{ config('site.email') ?: 'le contact à renseigner avant publication' }}.</p></section>
    </div>
</section>
@endsection
