<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <meta name="robots" content="@yield('robots', 'index,follow')">
    <title>@yield('title', 'Vigilus Guinée | Caméras, vidéosurveillance et sécurité électronique')</title>
    <meta name="description" content="@yield('meta_description', 'Explorez le catalogue de sécurité électronique Vigilus en Guinée : caméras, enregistreurs, contrôle d’accès, accessoires et devis à Conakry.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('jsonld')
</head>
<body>
    <div class="bg-ink text-white text-xs sm:text-sm py-2">
        <div class="container-wide flex items-center justify-between gap-3">
            <span>Équipements de sécurité électronique · Guinée</span>
            <a href="{{ route('quote.create') }}" class="font-semibold hover:underline">Demander un devis professionnel</a>
        </div>
    </div>
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="container-wide flex items-center gap-4 lg:gap-7 py-4">
            <a href="{{ route('home') }}" class="shrink-0 flex items-center gap-2" aria-label="Vigilus Guinée — Accueil">
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-rouge text-white font-extrabold text-xl">V</span>
                <span class="font-extrabold tracking-tight text-xl text-ink">VIGILUS<span class="block text-[10px] font-semibold tracking-widest text-quiet">GUINÉE</span></span>
            </a>
            <form action="{{ route('catalog') }}" method="GET" role="search" class="hidden sm:flex flex-1 max-w-2xl">
                <label for="header-search" class="sr-only">Rechercher une caméra ou une référence</label>
                <input id="header-search" name="q" value="{{ request('q') }}" class="field rounded-r-none" placeholder="Caméra, enregistreur, référence Dahua…">
                <button class="btn-red rounded-l-none" type="submit">Rechercher</button>
            </form>
            <nav aria-label="Accès rapide" class="ml-auto flex items-center gap-4 text-sm font-semibold">
                <a class="hidden md:inline hover:text-rouge" href="{{ route('catalog') }}">Boutique</a>
                <a class="hidden lg:inline hover:text-rouge" href="{{ route('solution.videosurveillance') }}">Choisir son équipement</a>
                <a class="text-rouge hover:underline" href="{{ route('quote.create') }}">Contact commercial</a>
                <a class="whitespace-nowrap text-ink hover:text-rouge" href="{{ route('cart.index') }}" aria-label="Voir mon panier">Panier ({{ array_sum(session('cart', [])) }})</a>
            </nav>
        </div>
        <div class="sm:hidden container-wide pb-3">
            <form action="{{ route('catalog') }}" method="GET" role="search" class="flex">
                <label for="mobile-search" class="sr-only">Rechercher un produit</label>
                <input id="mobile-search" name="q" class="field rounded-r-none" placeholder="Rechercher un équipement…">
                <button class="btn-red rounded-l-none" type="submit" aria-label="Lancer la recherche">OK</button>
            </form>
        </div>
    </header>
    <main id="contenu">@yield('content')</main>
    <footer class="bg-ink text-white mt-20">
        <div class="container-wide grid sm:grid-cols-3 gap-10 py-14">
            <div>
                <div class="text-2xl font-extrabold">VIGILUS <span class="text-rouge">GUINÉE</span></div>
                <p class="mt-3 text-slate-300 text-sm leading-relaxed">Équipements, conseils et solutions de sécurité électronique pour particuliers et professionnels.</p>
            </div>
            <div class="text-sm">
                <strong class="block mb-3">Explorer</strong>
                <a class="block py-1 text-slate-300 hover:text-white" href="{{ route('catalog') }}">Tous les produits</a>
                <a class="block py-1 text-slate-300 hover:text-white" href="{{ route('solution.videosurveillance') }}">Choisir sa vidéosurveillance</a>
                <a class="block py-1 text-slate-300 hover:text-white" href="{{ route('quote.create') }}">Devis personnalisé</a>
            </div>
            <div class="text-sm">
                <strong class="block mb-3">Contact</strong>
                <span class="text-slate-300 block">Conakry, République de Guinée</span>
                @if(config('site.phone')) <a class="block py-1" href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a> @endif
                @if(config('site.email')) <a class="block py-1" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a> @endif
            </div>
        </div>
        <div class="border-t border-white/10 py-5"><div class="container-wide text-xs text-slate-400">© {{ date('Y') }} Vigilus Guinée. Prix et disponibilité à confirmer selon les produits.</div></div>
    </footer>
</body>
</html>
