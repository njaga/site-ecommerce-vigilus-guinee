<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Administration') · Vigilus Guinée</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<a href="#contenu" class="skip-link">Aller au contenu principal</a>
<div class="admin-shell">
    <aside class="admin-rail" aria-label="Navigation du back-office">
        <a href="{{ route('admin.home') }}" class="admin-rail-brand">
            <img src="{{ config('site.brand_logo_url') }}" width="83" height="47"
                 alt="Vigilus — logo du groupe">
            <span>GUINÉE<br><span class="font-normal text-quiet tracking-normal">Administration</span></span>
        </a>
        <nav class="admin-rail-nav" aria-label="Rubriques">
            <span class="admin-nav-label">Produits &amp; catalogue</span>
            <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])>Produits Dahua</a>
            <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Catégories</a>
            <a href="{{ route('admin.imports.index') }}" @class(['active' => request()->routeIs('admin.imports.*')])>Imports techniques</a>
            <span class="admin-nav-label">Suivi commercial</span>
            <a href="{{ route('admin.quotes.index') }}" @class(['active' => request()->routeIs('admin.quotes.*')])>Demandes de devis</a>
            <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Commandes</a>
            <span class="admin-nav-label">Préparation du site</span>
            <a href="{{ route('admin.seo-redirects.index') }}" @class(['active' => request()->routeIs('admin.seo-redirects.*')])>Migration SEO</a>
            <a href="{{ route('admin.launch-readiness') }}" @class(['active' => request()->routeIs('admin.launch-readiness')])>Recette préproduction</a>
        </nav>
        <div class="admin-rail-foot">
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="font-bold text-rouge hover:underline">Voir la boutique ↗</a>
            <p class="mt-3 text-xs text-quiet">Données commerciales réservées aux utilisateurs autorisés.</p>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div><strong>Espace de gestion</strong><small>Vigilus Guinée · Équipements Dahua</small></div>
            <div class="flex items-center gap-4 sm:gap-6">
                <span class="text-xs text-quiet hidden sm:inline">{{ app()->environment('production') ? 'Environnement de production' : 'Environnement de préparation' }}</span>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="text-sm font-bold text-rouge hover:underline" type="submit">Déconnexion</button>
                </form>
            </div>
        </header>
        <main id="contenu" class="admin-content">
            @if(session('success'))
                <div role="status" class="mb-6 bg-emerald-50 border-l-4 border-emerald-600 p-4 text-emerald-900 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div role="alert" class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 text-red-900 text-sm">
                    <strong>Veuillez vérifier les informations :</strong>
                    <ul class="mt-2 list-disc pl-5">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
