<article class="group bg-white border border-slate-200 rounded-xl overflow-hidden hover:border-slate-300 hover:shadow-lg hover:shadow-slate-100 transition-shadow">
    <a href="{{ route('product.show', $product->slug) }}" class="block aspect-[4/3] bg-cloud overflow-hidden">
        @if($product->images->first())
            <img class="product-photo group-hover:scale-[1.02] transition-transform" loading="lazy" width="400" height="300"
                 src="{{ asset('storage/'.$product->images->first()->path) }}"
                 alt="{{ $product->images->first()->alt ?: $product->name }}">
        @else
            <div class="flex h-full items-center justify-center text-slate-400 text-xs text-center px-6">Visuel produit à renseigner</div>
        @endif
    </a>
    <div class="p-5">
        <div class="text-[11px] text-quiet font-semibold uppercase tracking-wider">{{ $product->brand }} · {{ $product->sku }}</div>
        <h3 class="mt-2 font-bold text-ink leading-snug min-h-12 group-hover:text-rouge">
            <a href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a>
        </h3>
        <p class="text-sm text-quiet mt-2 line-clamp-2">{{ $product->short_description }}</p>
        <div class="mt-5 flex items-end justify-between gap-2">
            <div>
                @if($product->price_gnf !== null)
                    <strong class="block text-lg text-ink">{{ number_format($product->price_gnf, 0, ',', ' ') }} <span class="text-xs">GNF</span></strong>
                @else
                    <strong class="block text-sm text-rouge">Prix sur devis</strong>
                @endif
                <span class="text-xs {{ $product->stock_qty > 0 ? 'text-emerald-700' : 'text-quiet' }}">{{ $product->stock_qty > 0 ? 'En stock' : 'Disponibilité à confirmer' }}</span>
            </div>
            <a class="text-sm font-bold text-rouge hover:underline shrink-0" href="{{ route('product.show', $product->slug) }}">Détails →</a>
        </div>
    </div>
</article>
