<section id="pourquoi-nous-choisir" class="why-section py-16 lg:py-24">
    <div class="container-wide">
        <div class="flex flex-wrap justify-between items-end gap-6 mb-10">
            <div>
                <p class="section-eyebrow">Notre approche</p>
                <h2 class="section-heading mt-4">Pourquoi nous choisir ?</h2>
                <p class="text-quiet mt-4 max-w-2xl leading-relaxed">Un choix d'équipements ne se résume pas à un prix : il demande des références claires, des caractéristiques vérifiées et un suivi commercial compréhensible.</p>
            </div>
            <a href="{{ route('about') }}" class="section-link">À propos de Vigilus <x-store-icon name="arrow-right" :size="18"/></a>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
            @foreach([
                ['01', 'Un ancrage régional', 'Une implantation à Conakry dans le réseau Vigilus, portée par les standards et l’expérience du Groupe.'],
                ['02', 'Des références documentées', 'Modèles, caractéristiques et demandes de volumes structurés pour faciliter votre choix.'],
                ['03', 'Une réponse personnalisée', 'Le devis et les disponibilités sont vérifiés selon votre besoin et vos contraintes.'],
                ['04', 'Un parcours transparent', 'Demande, confirmation commerciale puis règlement hors ligne : chaque étape est annoncée clairement.'],
            ] as [$number, $title, $description])
                <article class="why-card">
                    <span class="why-card-index">{{ $number }}</span>
                    <h3 class="text-lg font-extrabold mt-8 leading-snug">{{ $title }}</h3>
                    <p class="mt-4 text-sm text-quiet leading-relaxed">{{ $description }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
