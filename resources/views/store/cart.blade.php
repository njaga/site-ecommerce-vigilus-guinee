@extends('layouts.store')
@section('title', 'Mon panier | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide py-12">
    <p class="section-eyebrow">Sélection d’équipements</p>
    <h1 class="text-3xl sm:text-4xl font-extrabold mt-5">Votre sélection d’équipements.</h1>
    <p class="text-quiet mt-4 max-w-2xl leading-relaxed">Vérifiez les références et ajustez les quantités avant d'envoyer votre demande. Aucun règlement ne sera demandé à cette étape.</p>
    @if($errors->any())
        <div role="alert" class="mt-6 p-4 bg-red-50 text-red-900 rounded-lg">{{ $errors->first() }}</div>
    @endif
    @if(session('success'))
        <div role="status" class="mt-6 p-4 bg-emerald-50 text-emerald-900 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($lines->isEmpty())
        <div class="border border-slate-200 rounded-md mt-9 p-14 bg-cloud text-center">
            <p class="text-xl font-bold">Votre panier est vide.</p>
            <a class="btn-red mt-6" href="{{ route('catalog') }}">Explorer le catalogue</a>
        </div>
    @else
        <div class="grid lg:grid-cols-[minmax(0,1fr)_340px] gap-7 mt-9 items-start">
            <div class="space-y-4">
                @foreach($lines as $line)
                    <article class="bg-white rounded-md border border-slate-200 p-5 flex flex-wrap sm:flex-nowrap gap-5 items-start">
                        <div class="w-24 h-24 shrink-0 bg-cloud border border-slate-100 rounded-lg overflow-hidden">
                            @if($line['product']?->images->first())
                                <img class="product-photo" alt="{{ $line['product']->name }}" src="{{ asset('storage/'.$line['product']->images->first()->path) }}" width="120" height="120">
                            @else <div class="h-full grid place-items-center text-xs text-quiet text-center px-2">Sans image</div> @endif
                        </div>
                        <div class="flex-1 min-w-40">
                            @if($line['product'])
                                <a class="font-bold hover:text-rouge" href="{{ route('product.show', $line['product']->slug) }}">{{ $line['product']->name }}</a>
                                <p class="text-xs text-quiet mt-1">{{ $line['product']->sku }}</p>
                            @else <strong>Référence indisponible</strong> @endif
                            @if(! $line['valid'])
                                <p class="text-sm text-rouge mt-3">Ce produit ou cette quantité n'est plus disponible. Modifiez ou supprimez cette ligne.</p>
                            @else
                                <p class="text-sm text-ink font-bold mt-3">{{ number_format($line['subtotal'], 0, ',', ' ') }} GNF</p>
                            @endif
                        </div>
                        <form action="{{ route('cart.update') }}" method="POST" class="flex gap-2 items-end">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $line['id'] }}">
                            <div><label class="input-label" for="qty-{{ $line['id'] }}">Quantité</label>
                                <input class="field w-20" id="qty-{{ $line['id'] }}" name="quantity" type="number" min="0" max="100" value="{{ $line['quantity'] }}" required>
                            </div>
                            <button class="btn-light text-xs" type="submit">Mettre à jour</button>
                        </form>
                        <form action="{{ route('cart.update') }}" method="POST" class="self-end">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $line['id'] }}">
                            <input type="hidden" name="quantity" value="0">
                            <button type="submit" class="text-sm font-bold text-rouge hover:underline px-2 py-3"
                                    aria-label="Retirer {{ $line['product']?->name ?: 'la référence indisponible' }} du panier">Retirer</button>
                        </form>
                    </article>
                @endforeach
            </div>
            <aside class="bg-cloud rounded-md p-6 border border-slate-200">
                <h2 class="font-extrabold text-xl">Votre récapitulatif</h2>
                <div class="mt-6 flex justify-between font-bold">
                    <span>Sous-total matériel</span><span>{{ number_format($subtotal, 0, ',', ' ') }} GNF</span>
                </div>
                <div class="flex justify-between text-sm text-quiet mt-4"><span>Livraison</span><span>À confirmer</span></div>
                <div class="border-t border-slate-200 mt-6 pt-5 text-sm text-quiet">
                    Les frais d'expédition et le montant final sont confirmés par l'équipe avant tout règlement hors site.
                </div>
                @if($lines->every(fn ($line) => $line['valid']))
                    <a href="{{ route('checkout.create') }}" class="btn-red w-full mt-6 text-center">Continuer →</a>
                @else
                    <p class="mt-5 text-sm font-bold text-rouge">Corrigez les lignes indisponibles pour continuer.</p>
                @endif
                <a href="{{ route('catalog') }}" class="block text-center text-sm font-semibold text-rouge mt-5">Ajouter d’autres produits</a>
            </aside>
        </div>
    @endif
</div>
@endsection
