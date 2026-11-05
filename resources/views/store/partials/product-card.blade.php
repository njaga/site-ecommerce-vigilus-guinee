<article class="product-card group">
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-media" aria-label="Découvrir {{ $product->name }}">
        @if($product->images->first())
            <img src="{{ asset('storage/'.$product->images->first()->path) }}"
                 alt="{{ $product->images->first()->alt ?: $product->name }}"
                 width="460" height="410" loading="lazy">
        @else
            @php $visual = app(\App\Support\CategoryVisuals::class)->for($product->category); @endphp
            <img src="{{ $visual['url'] }}" class="preview-category-image" width="460" height="410"
                alt="Illustration : {{ $visual['label'] }}" loading="lazy">
            <span class="absolute bottom-3 left-3 text-[10px] uppercase tracking-wide font-bold text-slate-600 bg-white/95 px-2.5 py-1.5">Illustration</span>
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
            @if($product->display_price_gnf !== null)
                <strong class="product-card-price">{{ number_format($product->display_price_gnf, 0, ',', ' ') }} <span class="text-xs font-bold">GNF</span></strong>
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
