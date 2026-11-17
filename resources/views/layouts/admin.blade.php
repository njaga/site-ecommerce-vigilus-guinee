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
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<a href="#contenu" class="skip-link">Aller au contenu principal</a>
<div class="admin-shell">
    <aside class="admin-rail" aria-label="Navigation du back-office">
        <a href="{{ route('admin.home') }}" class="admin-rail-brand">
            <img src="{{ config('site.brand_logo_url') }}" width="83" height="47"
                 alt="Vigilus — logo du groupe">
            <span>Vigilus<span class="admin-brand-caption">Administration</span></span>
        </a>
        <nav class="admin-rail-nav" aria-label="Rubriques">
            @foreach([
                'Catalogue' => [['admin.products.index', 'admin.products.*', 'package', 'Produits'], ['admin.categories.index', 'admin.categories.*', 'filter', 'Catégories'], ['admin.imports.index', 'admin.imports.*', 'file', 'Imports']],
                'Activité' => [['admin.orders.index', 'admin.orders.*', 'cart', 'Commandes'], ['admin.quotes.index', 'admin.quotes.*', 'file', 'Devis'], ['admin.training.index', 'admin.training.*', 'calendar', 'Formations']],
                'Site' => [['admin.blog.index', 'admin.blog.*', 'edit', 'Articles'], ['admin.seo-redirects.index', 'admin.seo-redirects.*', 'link', 'Redirections'], ['admin.launch-readiness', 'admin.launch-readiness', 'shield', 'Mise en ligne']],
            ] as $group => $links)
                <span class="admin-nav-label">{{ $group }}</span>
                @foreach($links as [$route, $pattern, $icon, $label])
                    <a href="{{ route($route) }}" @class(['active' => request()->routeIs($pattern)]) @if(request()->routeIs($pattern)) aria-current="page" @endif><x-store-icon :name="$icon" :size="19"/><span>{{ $label }}</span></a>
                @endforeach
            @endforeach
        </nav>
        <div class="admin-rail-foot">
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="font-bold text-rouge hover:underline">Voir la boutique ↗</a>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="admin-topbar-label"><span class="admin-presence" aria-hidden="true"></span><strong>Espace de gestion</strong></div>
            <div class="flex items-center gap-4 sm:gap-6">
                @unless(app()->environment('production'))<span class="admin-environment">Préproduction</span>@endunless
                <span class="admin-user hidden sm:inline">{{ auth()->user()?->name }}</span>
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
