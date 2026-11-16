@props(['amount'])
@if($amount !== null)
<span {{ $attributes }} data-price-gnf="{{ $amount }}">{{ number_format($amount, 0, ',', ' ') }} GNF</span>
@else
<span {{ $attributes }}>Prix sur devis</span>
@endif
