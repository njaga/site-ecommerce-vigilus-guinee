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
    @vite(['resources/css/app.css', 'resources/css/storefront.css', 'resources/js/app.js'])
    @stack('jsonld')
</head>
<body class="store-body" data-currency-rates="{{ json_encode(config('currency.rates')) }}">
    <a class="skip-link" href="#contenu">Aller au contenu principal</a>
    <header class="store-header">
        <div class="container-wide store-header-inner">
            <a href="{{ route('home') }}" class="store-brand" aria-label="Vigilus Groupe SA — boutique Guinée">
                <img src="{{ config('site.brand_logo_url') }}" width="126" height="58"
                     alt="Vigilus Groupe SA — logo officiel" class="store-brand-image">
            </a>
            <button type="button" data-open-search data-fallback-url="{{ route('catalog') }}" class="store-search-launch hidden md:flex" aria-haspopup="dialog"
                    aria-label="Rechercher un produit Dahua">
                <x-store-icon name="search" :size="20" class="text-slate-500"/>
                <span class="flex-1 text-left">Un équipement, une référence…</span>
                <kbd class="store-search-shortcut">Ctrl K</kbd>
            </button>
            <div class="store-header-actions">
                @include('store.partials.currency-picker')
                <a class="store-header-contact btn-red hidden sm:inline-flex" href="{{ route('quote.create') }}">Demander un devis</a>
                <a class="store-header-cart" href="{{ route('cart.index') }}" aria-label="Panier — {{ array_sum(session('cart', [])) }} article(s)">
                    <x-store-icon name="cart" :size="22"/>
                    <span class="hidden sm:inline">Panier</span>
                    <span class="store-cart-count">{{ array_sum(session('cart', [])) }}</span>
                </a>
                <details class="mobile-menu md:hidden">
                    <summary aria-label="Ouvrir la navigation"><x-store-icon name="menu" :size="24"/></summary>
                    <div class="mobile-menu-panel">
                        <nav aria-label="Menu mobile">
                            <a href="{{ route('catalog') }}">Notre catalogue <x-store-icon name="arrow-right" :size="17"/></a>
                            @foreach($navCategories->take(4) as $navCategory)
                                @php $navVisual = app(\App\Support\CategoryVisuals::class)->for($navCategory); @endphp
                                <a href="{{ route('category.show', $navCategory) }}" class="store-category-link">
                                    <img class="store-category-thumbnail" src="{{ $navVisual['url'] }}" alt="" width="48" height="48" loading="lazy">
                                    <span>{{ $navCategory->name }}</span><x-store-icon name="arrow-right" :size="14"/>
                                </a>
                            @endforeach
                            <a href="{{ route('about') }}">À propos de Vigilus Guinée <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('contact') }}">Contact <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('training.index') }}">Formations <x-store-icon name="arrow-right" :size="17"/></a>
                            <a href="{{ route('solution.videosurveillance') }}">Journal & conseils <x-store-icon name="arrow-right" :size="17"/></a>
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
                <details class="store-nav-catalog">
                    <summary @class(['is-current' => request()->routeIs('catalog', 'category.show', 'product.show')])>
                        Notre catalogue <x-store-icon name="chevron-down" :size="15"/>
                    </summary>
                    <div class="store-mega-menu">
                        <p class="text-xs tracking-widest uppercase font-extrabold text-quiet">Explorer par famille d’équipements</p>
                        <div class="grid grid-cols-2 gap-x-6 mt-3">
                            @foreach($navCategories as $navCategory)
                                @php $navVisual = app(\App\Support\CategoryVisuals::class)->for($navCategory); @endphp
                                <a href="{{ route('category.show', $navCategory) }}" class="store-category-link">
                                    <img class="store-category-thumbnail" src="{{ $navVisual['url'] }}" alt="" width="48" height="48" loading="lazy">
                                    <span>{{ $navCategory->name }}</span>
                                    <x-store-icon name="arrow-right" :size="15" class="ml-auto text-rouge"/>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('catalog') }}" class="font-extrabold text-rouge mt-3">Tout le catalogue <x-store-icon name="arrow-right" :size="16"/></a>
                    </div>
                </details>
                <a href="{{ route('about') }}" @class(['is-current' => request()->routeIs('about')])>À propos</a>
                <a href="{{ route('training.index') }}" @class(['is-current' => request()->routeIs('training.*')])>Formations</a>
                <a href="{{ route('solution.videosurveillance') }}" @class(['is-current' => request()->routeIs('solution.videosurveillance')])>Journal & conseils</a>
                <a href="{{ route('contact') }}" @class(['is-current' => request()->routeIs('contact')])>Contact</a>
                <span class="store-nav-note">Conakry, Guinée</span>
            </nav>
        </div>
        <div class="container-wide md:hidden pb-3">
            <button type="button" data-open-search data-fallback-url="{{ route('catalog') }}" class="store-search-launch store-search-launch-mobile w-full" aria-haspopup="dialog">
                <x-store-icon name="search" :size="18"/><span>Rechercher un équipement…</span>
                <x-store-icon name="arrow-right" :size="16" class="ml-auto"/>
            </button>
        </div>
    </header>
    <div class="currency-notice" data-currency-notice hidden role="status">Prix approximatifs · Taux fixes du {{ \Carbon\Carbon::parse(config('currency.updated_at'))->format('d/m/Y') }} · Le montant de référence reste en GNF.</div>
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
                <span>Recherchez un modèle, une catégorie ou un équipement.</span>
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
    <footer class="store-footer">
        <div class="container-wide py-14 lg:py-18">
            <div class="grid sm:grid-cols-2 lg:grid-cols-[1.35fr_.85fr_.85fr_1.1fr] gap-9 lg:gap-14">
                <div>
                    <a href="{{ route('home') }}" class="store-footer-logo-wrap" aria-label="Vigilus Groupe SA — accueil">
                        <img src="{{ config('site.brand_logo_url') }}" width="144" height="70" alt="Vigilus Groupe SA — logo officiel" loading="lazy">
                    </a>
                    <p class="mt-5 text-sm leading-relaxed max-w-xs">Vigilus Guinée accompagne vos projets de sécurité : équipements Dahua, conseil technique et formations pour les installateurs et les entreprises.</p>
                    <p class="footer-dahua-statement">{{ config('group.dahua_statement') }}</p>
                    <a href="https://groupevigilus.com/" target="_blank" rel="noopener noreferrer" class="inline-flex gap-2 items-center font-bold text-sm text-[#bde8f2] mt-5 hover:text-white">Découvrir le Groupe Vigilus <x-store-icon name="arrow-right" :size="16"/></a>
                </div>
                <nav class="store-footer-nav" aria-label="Navigation de la boutique">
                    <h2>Explorer</h2>
                    <a href="{{ route('home') }}">Accueil</a>
                    <a href="{{ route('catalog') }}">Notre catalogue</a>
                    <a href="{{ route('solution.videosurveillance') }}">Journal & conseils</a>
                    <a href="{{ route('about') }}">À propos</a>
                    <a href="{{ route('training.index') }}">Formations</a>
                    <a href="{{ route('contact') }}">Contact</a>
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
                    @include('store.partials.contact-details')
                    <a href="{{ route('contact') }}" class="font-extrabold text-[#bde8f2] mt-2 hover:text-white">Échanger avec l'équipe <span aria-hidden="true">↗</span></a>
                </div>
            </div>
            <div class="store-footer-bottom">
                <span>© {{ date('Y') }} Vigilus Groupe SA · Guinée</span>
                <span>Équipements de sécurité électronique · Conakry</span>
            </div>
        </div>
    </footer>
    @include('store.partials.whatsapp')
</body>
</html>
