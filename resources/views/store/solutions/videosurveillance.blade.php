@extends('layouts.store')
@section('title', 'Installation vidéosurveillance à Conakry : étude et équipements | Vigilus Guinée')
@section('meta_description', 'Projet de vidéosurveillance à Conakry ? Découvrez comment choisir les caméras, l’enregistrement et l’implantation, puis demandez une étude personnalisée à Vigilus Guinée.')
@section('canonical', route('solution.videosurveillance'))
@push('jsonld')
    @php
        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Accueil', 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Vidéosurveillance à Conakry', 'item' => route('solution.videosurveillance')],
            ],
        ];
    @endphp
    <script type="application/ld+json">@json($breadcrumbs)</script>
@endpush
@section('content')
<section class="bg-cloud border-b border-slate-200">
    <div class="container-wide py-16 lg:py-24 grid lg:grid-cols-[1.3fr_.7fr] gap-10 items-center">
        <div>
            <nav aria-label="Fil d'Ariane" class="text-xs text-quiet mb-7"><a href="{{ route('home') }}">Accueil</a> / Vidéosurveillance à Conakry</nav>
            <p class="kicker">Conakry · Réseaux de surveillance</p>
            <h1 class="font-extrabold tracking-tight text-4xl lg:text-5xl leading-tight mt-4">Vidéosurveillance à Conakry : <span class="text-rouge">un système pensé pour votre site.</span></h1>
            <p class="text-lg text-quiet leading-relaxed mt-6">Commerce, entrepôt, résidence ou bureau : le choix d’une caméra ne se résume pas à sa résolution. L'emplacement, l'éclairage, l’enregistrement et les accès aux images comptent tout autant.</p>
            <div class="flex gap-3 flex-wrap mt-8"><a class="btn-red" href="{{ route('quote.create') }}">Décrire mon projet</a><a class="btn-light" href="{{ route('catalog') }}">Voir les équipements</a></div>
        </div>
        <aside class="bg-white border border-slate-200 rounded-2xl p-8">
            <p class="kicker">Avant de choisir</p>
            <h2 class="text-xl font-extrabold mt-3">Les points à clarifier</h2>
            <ul class="mt-5 space-y-4 text-sm text-quiet">
                <li><strong class="text-ink block">Zones à couvrir</strong>Entrées, comptoirs, circulation, zones de stockage</li>
                <li><strong class="text-ink block">Conditions techniques</strong>Éclairage, alimentation, réseau, environnement</li>
                <li><strong class="text-ink block">Exploitation</strong>Durée d’archivage, accès aux images, maintenance</li>
            </ul>
        </aside>
    </div>
</section>
<div class="container-wide py-16">
    <p class="kicker">Définir la bonne architecture</p>
    <h2 class="text-3xl font-extrabold mt-2">Les composants d’une installation cohérente</h2>
    <div class="grid md:grid-cols-3 gap-5 mt-8">
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">1. Caméras et objectifs</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Fixes ou motorisées, intérieures ou extérieures, les références doivent être retenues selon les scènes et les détails utiles à observer.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('catalog') }}">Explorer les caméras →</a>
        </article>
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">2. Enregistrement et stockage</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Le nombre de flux, leur qualité et la durée de conservation déterminent la capacité des enregistreurs et des disques.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('catalog') }}">Voir le catalogue NVR / XVR →</a>
        </article>
        <article class="border border-slate-200 p-7 rounded-xl">
            <h3 class="text-lg font-extrabold">3. Réseau et sécurité des accès</h3>
            <p class="text-sm text-quiet mt-4 leading-relaxed">Connectivité, protection des identifiants, accès aux images et suivi technique doivent être prévus dès la conception.</p>
            <a class="text-sm font-bold text-rouge inline-block mt-5" href="{{ route('quote.create') }}">Demander un conseil →</a>
        </article>
    </div>
    <section class="bg-ink text-white rounded-2xl p-8 sm:p-12 mt-14 grid lg:grid-cols-[1fr_auto] gap-6 items-center">
        <div><h2 class="text-2xl font-extrabold">Votre installation mérite une étude adaptée.</h2>
        <p class="text-slate-300 mt-3 text-sm max-w-2xl leading-relaxed">Indiquez le type de site, les zones à surveiller et les contraintes de votre projet. Une proposition commerciale sera établie selon les informations confirmées.</p></div>
        <a href="{{ route('quote.create') }}" class="btn-red">Demander un devis →</a>
    </section>
    <p class="text-xs text-quiet mt-6">Tout projet doit respecter les règles applicables à la protection des personnes et des données. Les caractéristiques des équipements et les prestations sont confirmées dans une proposition commerciale.</p>
</div>
@endsection
