<section id="notre-reseau" class="group-network storefront-section">
    <div class="container-wide">
        <div class="section-topline"><div><p class="section-eyebrow">Vigilus Groupe SA</p><h2 class="section-heading">Un Groupe, plusieurs horizons.</h2></div><a class="section-link" href="{{ route('about') }}#nos-filiales">Découvrir nos filiales <x-store-icon name="arrow-right" :size="16"/></a></div>
        <div class="group-locations">
            @foreach(config('group.locations') as $location)
                <div class="group-location"><img src="{{ asset('images/flags/'.$location['code'].'.svg') }}" alt="" width="42" height="28" loading="lazy"><strong>{{ $location['name'] }}</strong>@if(isset($location['url']))<a href="{{ $location['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Site de Vigilus — {{ $location['name'] }} (nouvel onglet)">Voir le site ↗</a>@endif</div>
            @endforeach
        </div>
    </div>
</section>
