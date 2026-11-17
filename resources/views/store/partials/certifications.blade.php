<section id="certifications" class="container-wide storefront-section">
    <div class="section-topline"><div><p class="section-eyebrow">Les engagements du Groupe</p><h2 class="section-heading">Des exigences qui nous guident.</h2></div></div>
    <p class="certifications-intro">Les certifications de Vigilus Groupe SA encadrent ses systèmes de management. Elles soutiennent une démarche d’amélioration continue de la qualité, de la sécurité au travail et de la performance environnementale.</p>
    <div class="certifications-grid">
        @foreach(config('group.certifications') as $certification)
            <article class="certification-card"><img src="{{ asset('images/certifications/'.$certification['image']) }}" alt="Badge de certification {{ $certification['name'] }} de Vigilus Groupe SA" width="160" height="190" loading="lazy"><div><p class="section-eyebrow">{{ $certification['name'] }}</p><h3>{{ $certification['title'] }}</h3><p>{{ $certification['description'] }}</p><a href="{{ $certification['source'] }}" target="_blank" rel="noopener noreferrer">Comprendre la norme sur ISO.org ↗</a></div></article>
        @endforeach
    </div>
</section>
