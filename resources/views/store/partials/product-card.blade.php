<article class="product-card group">
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-media" aria-label="Découvrir {{ $product->name }}">
        @if($product->images->first())
            <img src="{{ asset('storage/'.$product->images->first()->path) }}"
                 alt="{{ $product->images->first()->alt ?: $product->name }}"
                 width="460" height="410" loading="lazy">
        @else
            <div class="h-full grid place-items-center text-xs text-slate-400 text-center p-8">Photographie fabricant à confirmer</div>
        @endif
        <span class="absolute left-4 top-4 text-[10px] font-bold tracking-wider uppercase text-slate-600 bg-white/95 border border-slate-200 px-2.5 py-1.5">{{ $product->brand }}</span>
    </a>
    <div class="p-4 sm:p-5 flex flex-col flex-1">
        <p class="product-card-meta">{{ $product->category?->name ?? 'Sécurité électronique' }} · {{ $product->sku }}</p>
        <h3 class="product-card-name mt-3">
            <a class="hover:text-rouge" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <p class="text-xs sm:text-sm leading-relaxed text-quiet mt-2 line-clamp-2">{{ $product->short_description }}</p>
        <div class="mt-auto pt-5">
            @if($product->price_gnf !== null && ! $product->price_on_request)
                <strong class="product-card-price">{{ number_format($product->price_gnf, 0, ',', ' ') }} <span class="text-xs font-bold">GNF</span></strong>
            @else
                <strong class="text-rouge font-extrabold text-sm">Prix sur devis</strong>
            @endif
            <p class="product-card-availability mt-2 {{ $product->can_order ? 'is-available' : '' }}">
                {{ $product->can_order ? 'Disponible à la demande' : 'Disponibilité à confirmer' }}
            </p>
            <a class="product-card-cta mt-5" href="{{ route('product.show', $product->slug) }}">
                <span>Voir la fiche</span><x-store-icon name="arrow-right" :size="17"/>
            </a>
        </div>
    </div>
</article>
