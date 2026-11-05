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
            <span>Vigilus Groupe SA <span class="mx-2 opacity-40">/</span> Distribution d’équipements à Conakry</span>
            <a href="{{ route('quote.create') }}" class="hover:text-white font-semibold">Votre projet, notre accompagnement <span aria-hidden="true" class="ml-2">↗</span></a>
        </div>
    </div>
    <header class="store-header">
        <div class="container-wide store-header-inner">
            <a href="{{ route('home') }}" class="store-brand" aria-label="Vigilus Groupe SA — boutique Guinée">
                <img src="{{ config('site.brand_logo_url') }}" width="126" height="58"
                     alt="Vigilus Groupe SA — logo officiel" class="store-brand-image">
                <span class="store-brand-region">GUINÉE <span class="block font-normal tracking-normal normal-case text-xs opacity-65">Équipements</span></span>
            </a>
            <button type="button" data-open-search data-fallback-url="{{ route('catalog') }}" class="store-search-launch hidden md:flex" aria-haspopup="dialog"
                    aria-label="Rechercher un produit Dahua">
                <x-store-icon name="search" :size="20" class="text-slate-500"/>
                <span class="flex-1 text-left">Rechercher un équipement ou une référence Dahua…</span>
                <kbd class="store-search-shortcut">⌘ / Ctrl + K</kbd>
            </button>
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
                            <a href="{{ route('about') }}">À propos de Vigilus Guinée <x-store-icon name="arrow-right" :size="17"/></a>
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
                <a href="{{ route('about') }}" @class(['is-current' => request()->routeIs('about')])>À propos</a>
                <a href="{{ route('solution.videosurveillance') }}" @class(['is-current' => request()->routeIs('solution.videosurveillance')])>Guide vidéosurveillance</a>
                <a href="{{ route('quote.create') }}" @class(['is-current' => request()->routeIs('quote.*')])>Devis professionnels</a>
                <span class="store-nav-note">Guinée · GNF</span>
            </nav>
        </div>
        <div class="container-wide md:hidden pb-3">
            <button type="button" data-open-search data-fallback-url="{{ route('catalog') }}" class="store-search-launch store-search-launch-mobile w-full" aria-haspopup="dialog">
                <x-store-icon name="search" :size="18"/><span>Rechercher un équipement…</span>
                <x-store-icon name="arrow-right" :size="16" class="ml-auto"/>
            </button>
        </div>
    </header>
    <dialog id="store-search-dialog" data-search-dialog
            data-suggestions-url="{{ route('search.suggestions') }}"
            data-catalog-url="{{ route('catalog') }}"
            aria-labelledby="store-search-title" aria-describedby="store-search-help"
            class="store-search-dialog">
        <div class="store-search-dialog-panel">
            <div class="flex items-center justify-between gap-4 mb-5">
                <div>
                    <p class="text-xs tracking-[.17em] uppercase text-rouge font-extrabold">Recherche rapide</p>
                    <h2 id="store-search-title" class="text-xl sm:text-2xl font-extrabold mt-1">Quel équipement recherchez-vous ?</h2>
                </div>
                <button type="button" data-close-search class="store-search-close" aria-label="Fermer la recherche">
                    <x-store-icon name="close" :size="23"/>
                </button>
            </div>
            <form action="{{ route('catalog') }}" method="GET" role="search" class="store-search-dialog-form">
                <label for="store-search-input" class="sr-only">Rechercher une référence ou un équipement</label>
                <x-store-icon name="search" :size="22"/>
                <input id="store-search-input" data-search-input name="q" type="search" autocomplete="off" spellcheck="false"
                       placeholder="Caméra IP, enregistreur NVR, référence Dahua…"
                       aria-controls="store-search-results" aria-autocomplete="list">
                <button type="submit" class="store-search-submit" aria-label="Voir tous les résultats">
                    <x-store-icon name="arrow-right" :size="19"/>
                </button>
            </form>
            <div id="store-search-help" class="flex flex-wrap justify-between gap-3 text-xs text-quiet mt-4">
                <span>Suggestions dès 2 caractères · Produits publiés uniquement</span>
                <span class="hidden sm:inline">↑ ↓ sélectionner · ↵ ouvrir · Échap fermer</span>
            </div>
            <div id="store-search-results" data-search-results aria-live="polite" aria-atomic="false" class="store-search-results mt-7">
                <div data-search-intro>
                    <p class="text-xs uppercase tracking-widest font-extrabold text-quiet mb-4">Recherches fréquentes</p>
                    <div class="flex flex-wrap gap-2">
                        <a class="store-search-chip" href="{{ route('catalog', ['q' => 'Caméra IP']) }}">Caméra IP</a>
                        <a class="store-search-chip" href="{{ route('catalog', ['q' => 'NVR']) }}">Enregistreur NVR</a>
                        <a class="store-search-chip" href="{{ route('catalog', ['q' => 'Contrôle accès']) }}">Contrôle d’accès</a>
                        <a class="store-search-chip" href="{{ route('catalog', ['q' => 'Alarme']) }}">Alarme</a>
                    </div>
                </div>
            </div>
            <div class="store-search-dialog-footer mt-6">
                <a href="{{ route('catalog') }}" class="font-bold text-sm hover:text-rouge">Explorer tout le catalogue</a>
                <span class="text-xs text-quiet">Vigilus Guinée</span>
            </div>
        </div>
    </dialog>
    <main id="contenu">@yield('content')</main>
    <footer class="store-footer mt-24">
        <div class="container-wide py-14 lg:py-18">
            <div class="store-footer-cta">
                <div>
                    <p class="text-xs uppercase tracking-[.18em] font-extrabold text-[#9dcbdb]">Votre projet en Guinée</p>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold mt-3 leading-snug text-white">Une liste de références ? <span class="text-[#bde8f2]">Parlons équipements.</span></h2>
                    <p class="text-slate-300 mt-3 max-w-xl text-sm leading-relaxed">Transmettez vos besoins, modèles et quantités. Notre équipe confirme les disponibilités et conditions commerciales.</p>
                </div>
                <a href="{{ route('quote.create') }}" class="store-footer-cta-button">Demander un devis <x-store-icon name="arrow-right" :size="18"/></a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-[1.35fr_.85fr_.85fr_1.1fr] gap-9 lg:gap-14 mt-16">
                <div>
                    <a href="{{ route('home') }}" class="store-footer-logo-wrap" aria-label="Vigilus Groupe SA — accueil">
                        <img src="{{ config('site.brand_logo_url') }}" width="144" height="70" alt="Vigilus Groupe SA — logo officiel" loading="lazy">
                    </a>
                    <p class="mt-5 text-sm leading-relaxed text-slate-300 max-w-xs">Sélection et fourniture d’équipements électroniques de sécurité. Une implantation du Groupe Vigilus à Conakry.</p>
                    <a href="https://groupevigilus.com/" target="_blank" rel="noopener noreferrer" class="inline-flex gap-2 items-center font-bold text-sm text-[#bde8f2] mt-5 hover:text-white">Découvrir le Groupe Vigilus <x-store-icon name="arrow-right" :size="16"/></a>
                </div>
                <nav class="store-footer-nav" aria-label="Navigation de la boutique">
                    <h2>Explorer</h2>
                    <a href="{{ route('home') }}">Accueil</a>
                    <a href="{{ route('catalog') }}">Catalogue Dahua</a>
                    <a href="{{ route('solution.videosurveillance') }}">Guide vidéosurveillance</a>
                    <a href="{{ route('about') }}">À propos</a>
                    <a href="{{ route('quote.create') }}">Demande de devis</a>
                </nav>
                <nav class="store-footer-nav" aria-label="Liens utiles">
                    <h2>Informations</h2>
                    <a href="{{ route('legal.mentions') }}">Mentions légales</a>
                    <a href="{{ route('legal.privacy') }}">Politique de confidentialité</a>
                    <a href="{{ route('legal.sales') }}">Conditions commerciales</a>
                    <a href="{{ route('cart.index') }}">Mon panier</a>
                </nav>
                <div class="store-footer-nav">
                    <h2>Vigilus Guinée</h2>
                    <p class="text-slate-300 text-sm">Conakry, République de Guinée</p>
                    @if(config('site.phone'))
                        <a href="tel:{{ preg_replace('/[^+0-9]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a>
                    @endif
                    @if(config('site.email'))
                        <a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a>
                    @endif
                    <p class="text-xs text-slate-400 leading-relaxed">Pour toute demande de livraison, de volume ou de référence, notre équipe valide les conditions avant règlement.</p>
                    <a href="{{ route('quote.create') }}" class="font-extrabold text-[#bde8f2] mt-2 hover:text-white">Échanger avec l'équipe <span aria-hidden="true">↗</span></a>
                </div>
            </div>
            <div class="store-footer-bottom">
                <span>© {{ date('Y') }} Vigilus Groupe SA · Guinée</span>
                <span>Distribution d’équipements · Paiement hors du site · Prix et stocks à confirmer</span>
            </div>
        </div>
    </footer>
</body>
</html>
