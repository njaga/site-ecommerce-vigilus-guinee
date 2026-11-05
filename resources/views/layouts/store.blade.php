<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    @if(config('seo.default_indexable'))
    <meta name="robots" content="@yield('robots', 'index,follow')">
    @else
    <meta name="robots" content="noindex,nofollow">
    @endif
    <title>@yield('title', 'Vigilus Guinée | Équipements Dahua et sécurité électronique')</title>
    <meta name="description" content="@yield('meta_description', 'Explorez les caméras Dahua, enregistreurs, systèmes de contrôle d’accès et accessoires disponibles sur demande à Conakry.')">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('jsonld')
</head>
<body class="store-body">
    <a class="skip-link" href="#contenu">Aller au contenu principal</a>
    <div class="store-utility">
        <div class="container-wide flex flex-wrap items-center justify-between gap-x-5 gap-y-1 py-2">
            <span>Vigilus Guinée <span class="mx-2 opacity-40">/</span> Distribution d’équipements professionnels à Conakry</span>
            <span class="hidden sm:inline">Références &amp; volumes sur devis <span aria-hidden="true" class="ml-2">↗</span></span>
        </div>
    </div>
    <header class="store-header">
        <div class="container-wide store-header-inner">
            <a href="{{ route('home') }}" class="store-brand" aria-label="Vigilus Guinée — Accueil">
                <img src="{{ config('site.brand_logo_url') }}" width="126" height="58"
                     alt="Vigilus — logo du groupe" class="store-brand-image">
                <span class="store-brand-region">GUINÉE <span class="block font-normal tracking-normal normal-case text-xs opacity-65">Équipements</span></span>
            </a>
            <form action="{{ route('catalog') }}" method="GET" role="search" class="store-search hidden md:flex">
                <label class="sr-only" for="desktop-search">Rechercher un produit Dahua</label>
                <x-store-icon name="search" :size="19" class="store-search-icon"/>
                <input id="desktop-search" name="q" type="search" value="{{ request('q') }}"
                       placeholder="Rechercher une référence, caméra, NVR…" autocomplete="off">
                <button type="submit">Rechercher</button>
            </form>
            <div class="store-header-actions">
                <a class="store-header-contact hidden sm:inline-flex" href="{{ route('quote.create') }}">Demander un devis</a>
                <a class="store-header-cart" href="{{ route('cart.index') }}" aria-label="Panier — {{ array_sum(session('cart', [])) }} article(s)">
                    <x-store-icon name="cart" :size="22"/>
                    <span class="hidden sm:inline">Panier</span>
                    <span class="store-cart-count">{{ array_sum(session('cart', [])) }}</span>
                </a>
                <details class="mobile-menu md:hidden">
                    <summary aria-label="Ouvrir la navigation"><x-store-icon name="menu" :size="24"/></summary>
                    <div class="mobile-menu-panel">
                        <nav aria-label="Menu mobile">
                            <a href="{{ route('catalog') }}">Tous les produits <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('solution.videosurveillance') }}">Bien choisir sa vidéosurveillance <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('quote.create') }}">Demande de devis <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('cart.index') }}">Mon panier <x-store-icon name="arrow-right" :size="17"/></a>
                        </nav>
                        @if(config('site.phone') || config('site.email'))
                        <div class="mobile-menu-contact">
                            @if(config('site.phone')) <span>{{ config('site.phone') }}</span> @endif
                            @if(config('site.email')) <span>{{ config('site.email') }}</span> @endif
                        </div>
                        @endif
                    </div>
                </details>
            </div>
        </div>
        <div class="store-nav-row hidden md:block">
            <nav class="container-wide store-nav" aria-label="Navigation principale">
                <a href="{{ route('home') }}" @class(['is-current' => request()->routeIs('home')])>Accueil</a>
                <a href="{{ route('catalog') }}" @class(['is-current' => request()->routeIs('catalog', 'category.show', 'product.show')])>Catalogue Dahua</a>
                <a href="{{ route('solution.videosurveillance') }}" @class(['is-current' => request()->routeIs('solution.videosurveillance')])>Guide vidéosurveillance</a>
                <a href="{{ route('quote.create') }}" @class(['is-current' => request()->routeIs('quote.*')])>Devis professionnels</a>
                <span class="store-nav-note">Guinée · GNF</span>
            </nav>
        </div>
        <div class="container-wide md:hidden pb-3">
            <form action="{{ route('catalog') }}" role="search" method="GET" class="store-search store-search-mobile flex">
                <label class="sr-only" for="mobile-search">Rechercher une référence</label>
                <x-store-icon name="search" :size="18" class="store-search-icon"/>
                <input id="mobile-search" name="q" value="{{ request('q') }}" type="search" placeholder="Rechercher un produit Dahua…">
                <button type="submit" aria-label="Lancer la recherche"><x-store-icon name="arrow-right" :size="18"/></button>
            </form>
        </div>
    </header>
    <main id="contenu">@yield('content')</main>
    <footer class="store-footer mt-20">
        <div class="container-wide py-14 lg:py-20">
            <div class="grid sm:grid-cols-2 lg:grid-cols-[1.3fr_.8fr_.8fr_1fr] gap-10 lg:gap-12">
                <div>
                    <div class="store-footer-brand">VIGILUS <span>GUINÉE</span></div>
                    <p class="mt-4 text-sm leading-relaxed text-slate-300 max-w-xs">La sélection et la fourniture d’équipements de sécurité électronique pour vos projets à Conakry et en Guinée.</p>
                    <p class="mt-5 text-xs text-slate-400">Une entité du Groupe Vigilus.</p>
                </div>
                <nav aria-label="Catalogue" class="store-footer-nav"><h2>Catalogue</h2>
                    <a href="{{ route('catalog') }}">Toutes les références</a>
                    <a href="{{ route('solution.videosurveillance') }}">Guide vidéosurveillance</a>
                    <a href="{{ route('quote.create') }}">Demande de devis</a>
                </nav>
                <nav aria-label="Informations" class="store-footer-nav"><h2>Informations</h2>
                    <a href="{{ route('legal.mentions') }}">Mentions légales</a>
                    <a href="{{ route('legal.privacy') }}">Confidentialité</a>
                    <a href="{{ route('legal.sales') }}">Conditions commerciales</a>
                </nav>
                <div class="store-footer-nav">
                    <h2>Parlons de votre projet</h2>
                    <p class="text-slate-300">Conakry, République de Guinée</p>
                    @if(config('site.phone'))<a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>@endif
                    @if(config('site.email'))<a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>@endif
                    <a href="{{ route('quote.create') }}" class="font-bold text-white mt-2">Contacter l’équipe <span aria-hidden="true">↗</span></a>
                </div>
            </div>
            <div class="store-footer-bottom"><span>© {{ date('Y') }} Vigilus Guinée</span>
                <span>Prix, disponibilité et livraison confirmés avant toute transaction. Aucun paiement en ligne.</span>
            </div>
        </div>
    </footer>
</body>
</html>
