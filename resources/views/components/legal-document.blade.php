@props(['title', 'intro', 'sections', 'approved' => false])
<div class="container-wide legal-page">
    <header class="legal-heading">
        <p class="section-eyebrow">Informations & engagements</p>
        <h1>{{ $title }}</h1>
        <p>{{ $intro }}</p>
        <span class="legal-version">Version du {{ \Carbon\Carbon::parse(config('site.legal_updated_at'))->format('d/m/Y') }}</span>
    </header>
    @unless($approved)
        <p class="legal-draft"><strong>Version en cours de validation.</strong> Certaines informations sur l’exploitant et les modalités commerciales restent à confirmer avant la mise en ligne commerciale.</p>
    @endunless
    <div class="legal-layout">
        <aside class="legal-sidebar">
            <nav aria-label="Sommaire de {{ $title }}"><p>Sur cette page</p>
                @foreach($sections as $id => $label)<a href="#{{ $id }}"><span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>{{ $label }}</a>@endforeach
            </nav>
            <a class="legal-help" href="{{ route('contact') }}">Une question ?<strong>Contactez notre équipe <x-store-icon name="arrow-right" :size="16"/></strong></a>
        </aside>
        <article class="legal-content">{{ $slot }}</article>
    </div>
    <nav class="legal-related" aria-label="Autres informations légales">
        @foreach(['legal.mentions' => 'Mentions légales', 'legal.privacy' => 'Politique de confidentialité', 'legal.sales' => 'Conditions commerciales'] as $route => $label)
            <a href="{{ route($route) }}" @if(request()->routeIs($route)) aria-current="page" @endif>{{ $label }} <x-store-icon name="arrow-right" :size="16"/></a>
        @endforeach
    </nav>
</div>
