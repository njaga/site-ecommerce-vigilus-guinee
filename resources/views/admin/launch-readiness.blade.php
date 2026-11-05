@extends('layouts.admin')
@section('title', 'Préparation du lancement')
@section('content')
<div class="mb-8">
    <p class="kicker">Recette et exploitation</p>
    <h1 class="text-3xl font-extrabold mt-2">Préparation de la mise en ligne</h1>
    <p class="mt-3 text-quiet max-w-3xl">Vérifications automatiques disponibles sur ce serveur. Les vérifications visuelles, juridiques, emails réels et droits produits restent obligatoires.</p>
</div>
<div class="grid sm:grid-cols-3 gap-3 mb-8">
    <div class="bg-white border border-slate-200 rounded-xl p-5">
        <p class="text-xs text-quiet">Contrôles satisfaits</p><strong class="text-3xl">{{ collect($checks)->where('passed', true)->count() }} / {{ count($checks) }}</strong>
    </div>
    <div class="sm:col-span-2 bg-white border border-slate-200 rounded-xl p-5">
        <strong class="text-base">Décision de publication</strong>
        <p class="text-sm text-quiet mt-2">La réussite de ces points ne déclenche aucune publication ni migration. Le responsable doit approuver la recette et la bascule.</p>
    </div>
</div>
<div class="grid md:grid-cols-2 gap-4">
@foreach($checks as $check)
    <article class="bg-white border border-slate-200 rounded-xl p-5">
        <p class="text-xs uppercase tracking-wide font-bold {{ $check['passed'] ? 'text-emerald-700' : 'text-rouge' }}">{{ $check['passed'] ? 'Configuré' : 'À compléter' }}</p>
        <h2 class="font-extrabold mt-2">{{ $check['label'] }}</h2>
        <p class="text-sm text-quiet mt-2">{{ $check['detail'] }}</p>
    </article>
@endforeach
</div>
<div class="bg-cloud border border-slate-200 rounded-xl p-6 mt-8 text-sm text-quiet">
    <h2 class="font-bold text-ink text-lg">Contrôles humains indispensables</h2>
    <p class="mt-3">Tester sur de vrais appareils les menus, les fiches et photos Dahua, les formulaires, l’accessibilité clavier, les emails SMTP, le suivi signé des commandes et les conditions de livraison. Vérifier aussi le domaine historique Guinée avant toute redirection SEO.</p>
    <p class="mt-3 font-bold text-ink">Ne pas ouvrir le site au public tant que les informations légales, les produits et les contacts ne sont pas validés.</p>
</div>
@endsection
