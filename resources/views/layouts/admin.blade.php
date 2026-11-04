<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Administration') · Vigilus Guinée</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cloud">
    <header class="bg-ink text-white py-4">
        <div class="container-wide flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('admin.home') }}" class="font-extrabold tracking-tight text-xl">VIGILUS <span class="text-red-300">ADMIN</span></a>
            <nav aria-label="Administration" class="flex flex-wrap items-center gap-5 text-sm font-semibold">
                <a href="{{ route('admin.products.index') }}" class="hover:text-red-300">Produits</a>
                <a href="{{ route('admin.categories.index') }}" class="hover:text-red-300">Catégories</a>
                <a href="{{ route('admin.imports.index') }}" class="hover:text-red-300">Import BOQ</a>
                <a href="{{ route('admin.quotes.index') }}" class="hover:text-red-300">Devis</a>
                <a href="{{ route('admin.orders.index') }}" class="hover:text-red-300">Commandes</a>
                <a href="{{ route('home') }}" target="_blank" rel="noopener" class="hover:text-red-300">Voir la boutique ↗</a>
                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="hover:text-red-300">Déconnexion</button></form>
            </nav>
        </div>
    </header>
    <main class="container-wide py-10">
        @if(session('success'))<div role="status" class="mb-5 bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-emerald-900">{{ session('success') }}</div>@endif
        @if($errors->any())<div role="alert" class="mb-5 bg-red-50 border border-red-200 rounded-lg p-4 text-red-900">
            <strong>Veuillez corriger les champs :</strong><ul class="mt-2 list-disc ml-5">@foreach($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
        </div>@endif
        @yield('content')
    </main>
</body>
</html>
