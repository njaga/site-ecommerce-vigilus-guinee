@extends('layouts.store')
@section('title', 'Caméras Dahua et sécurité électronique à Conakry | Vigilus Guinée')
@section('meta_description', 'Découvrez les équipements Dahua proposés par Vigilus Guinée : caméras de surveillance, NVR, contrôle d’accès, alarmes et demandes de devis à Conakry.')
@section('content')
<section class="bg-[#f7f8fa] border-b border-slate-200">
    <div class="container-wide py-20 lg:py-28 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <p class="kicker">Équipements et solutions de sécurité</p>
            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.12] tracking-tight">Votre sécurité commence avec <span class="text-rouge">le bon équipement.</span></h1>
            <p class="text-quiet text-lg leading-relaxed mt-6 max-w-xl">Caméras, enregistreurs, alarmes et contrôle d’accès. Retrouvez les références de notre catalogue et faites-vous accompagner pour votre projet à Conakry.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('catalog') }}" class="btn-red">Explorer la boutique <span aria-hidden="true">→</span></a>
                <a href="{{ route('quote.create') }}" class="btn-light">Demander un devis</a>
            </div>
            <div class="mt-10 flex gap-6 text-sm text-quiet">
                <div><strong class="block text-ink">Références détaillées</strong>Caractéristiques techniques</div>
                <div><strong class="block text-ink">Accompagnement</strong>Choix des références et devis</div>
            </div>
        </div>
        <div class="bg-white rounded-3xl border border-slate-200 p-8 sm:p-10 shadow-sm">
            <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                <p class="font-bold text-xl">De quel équipement avez-vous besoin ?</p>
                <span class="text-rouge font-extrabold">01—04</span>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-6">
                @foreach(['Caméras IP','Enregistreurs NVR','Kits vidéosurveillance','Contrôle d’accès'] as $name)
                    @php $target = $categories->firstWhere('name', $name); @endphp
                    <a href="{{ $target ? route('category.show', $target) : route('catalog') }}" class="min-h-32 p-5 bg-cloud rounded-xl flex flex-col justify-between hover:bg-red-50 hover:text-rouge">
                        <span class="text-2xl text-rouge" aria-hidden="true">◈</span>
                        <strong class="text-sm">{{ $name }} <span aria-hidden="true">↗</span></strong>
                    </a>
                @endforeach
            </div>
            <p class="text-xs text-quiet mt-5">Les tarifs et les disponibilités sont affichés uniquement après validation.</p>
        </div>
    </div>
</section>
<section class="container-wide py-18">
    <div class="flex justify-between items-end gap-4 mb-8">
        <div><p class="kicker">Catalogue</p><h2 class="text-3xl font-extrabold mt-2">Nos catégories d’équipements</h2></div>
        <a href="{{ route('catalog') }}" class="font-bold text-rouge hover:underline text-sm">Tout voir →</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @forelse($categories as $category)
            <a href="{{ route('category.show', $category) }}" class="bg-white border border-slate-200 rounded-xl p-5 min-h-32 flex justify-between items-end hover:border-rouge hover:shadow-sm">
                <strong class="text-sm">{{ $category->name }}</strong><span class="text-rouge">↗</span>
            </a>
        @empty
            <p class="text-quiet col-span-full">Le catalogue est en cours de préparation.</p>
        @endforelse
    </div>
</section>
@if($featured->isNotEmpty())
<section class="bg-cloud py-18">
    <div class="container-wide">
        <div class="flex items-end justify-between gap-4 mb-8">
            <div><p class="kicker">Sélection</p><h2 class="text-3xl font-extrabold mt-2">Produits à découvrir</h2></div>
            <a href="{{ route('catalog') }}" class="font-bold text-rouge hover:underline text-sm">Voir le catalogue →</a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featured as $product)
                @include('store.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif
<section class="container-wide py-18 grid lg:grid-cols-2 gap-5">
    <div class="bg-ink text-white p-9 sm:p-12 rounded-2xl">
        <p class="text-red-300 font-bold text-sm uppercase tracking-widest">Solutions professionnelles</p>
        <h2 class="text-3xl font-extrabold mt-3">Vous équipez un commerce ou plusieurs sites ?</h2>
        <p class="text-slate-300 mt-4 leading-relaxed">Décrivez vos contraintes de sécurité, vos surfaces et votre budget. Notre équipe étudiera les références et solutions de fourniture adaptées.</p>
        <a href="{{ route('quote.create') }}" class="inline-block mt-7 font-bold text-white underline underline-offset-4">Demander une étude →</a>
    </div>
    <div class="bg-[#f8e9ec] p-9 sm:p-12 rounded-2xl">
        <p class="text-rouge font-bold text-sm uppercase tracking-widest">Installateurs et revendeurs</p>
        <h2 class="text-3xl font-extrabold mt-3">Des références, des volumes, une relation commerciale.</h2>
        <p class="text-quiet mt-4 leading-relaxed">Pour vos achats professionnels, communiquez les modèles et les quantités recherchés. Nous vous proposerons une offre personnalisée.</p>
        <a href="{{ route('quote.create') }}" class="inline-block mt-7 font-bold text-rouge underline underline-offset-4">Demander un tarif volume →</a>
    </div>
</section>
@endsection
