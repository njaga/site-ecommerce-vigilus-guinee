@props(['product'])
<span {{ $attributes->class(['equipment-thumbnail']) }}>
    @if($product?->images->first())
        <img src="{{ asset('storage/'.$product->images->first()->path) }}" alt="{{ $product->images->first()->alt ?: $product->name }}" width="64" height="64" loading="lazy">
    @else
        <x-store-icon name="package" :size="25"/>
        <span class="sr-only">Visuel à venir</span>
    @endif
</span>
