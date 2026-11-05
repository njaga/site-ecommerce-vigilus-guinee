<section id="nos-clients" class="client-proof py-14 lg:py-20">
    <div class="container-wide">
        <div class="grid lg:grid-cols-[.86fr_1.14fr] gap-10 lg:gap-16 items-center">
            <div>
                <p class="section-eyebrow">Ils nous font confiance</p>
                <h2 class="section-heading mt-4">Nos clients, à l’échelle du Groupe.</h2>
                <p class="text-quiet mt-5 leading-relaxed">Le Groupe Vigilus accompagne des organisations de secteurs variés en Afrique. Les références ci-contre sont celles du <strong>Groupe</strong>, tous métiers et marchés confondus, et ne constituent pas une liste de clients de la boutique en Guinée.</p>
                <a class="section-link mt-7" href="https://groupevigilus.com/" target="_blank" rel="noopener noreferrer">Découvrir le Groupe et ses références <x-store-icon name="arrow-right" :size="17"/></a>
            </div>
            <div class="client-wordmarks" aria-label="Exemples de références clients publiées par Vigilus Groupe SA">
                @foreach(['Orange', 'Senelec', 'DP World', 'Marriott', 'Maersk', 'Radisson'] as $name)
                    <span class="client-wordmark">{{ $name }}</span>
                @endforeach
            </div>
        </div>
    </div>
</section>
