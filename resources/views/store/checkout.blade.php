@extends('layouts.store')
@section('title', 'Demande de commande | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide py-12">
    <p class="section-eyebrow">Votre commande</p>
    <h1 class="text-3xl sm:text-4xl font-extrabold mt-5">Votre demande de commande.</h1>
    <p class="text-quiet mt-4 max-w-3xl leading-relaxed">Transmettez votre demande. Les disponibilités et conditions de règlement seront confirmées par notre équipe.</p>
    @if($errors->any())
        <div role="alert" class="mt-6 p-4 bg-red-50 text-red-900 rounded-lg"><strong>Impossible d'enregistrer :</strong><ul class="ml-5 list-disc mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="grid lg:grid-cols-[1fr_340px] gap-7 mt-9 items-start">
        <form method="POST" action="{{ route('checkout.store') }}" class="bg-white border border-slate-200 rounded-md p-6 sm:p-9 grid sm:grid-cols-2 gap-5">
            @csrf
            <input type="hidden" name="checkout_token" value="{{ $checkoutToken }}">
            <h2 class="sm:col-span-2 text-xl font-extrabold border-b border-slate-200 pb-4">01 · Vos coordonnées</h2>
            <div><label for="customer_name" class="input-label">Nom complet *</label><input id="customer_name" name="customer_name" class="field" required autocomplete="name" value="{{ old('customer_name') }}"></div>
            <div><label for="customer_phone" class="input-label">Téléphone *</label><input id="customer_phone" name="customer_phone" class="field" required type="tel" autocomplete="tel" value="{{ old('customer_phone') }}"></div>
            <div class="sm:col-span-2"><label for="customer_email" class="input-label">Adresse email</label><input id="customer_email" name="customer_email" type="email" autocomplete="email" class="field" value="{{ old('customer_email') }}"></div>
            <h2 class="sm:col-span-2 text-xl font-extrabold mt-4 border-b border-slate-200 pb-4">02 · Livraison ou retrait souhaité</h2>
            <div><label for="city" class="input-label">Ville *</label><input id="city" name="city" required class="field" value="{{ old('city', config('site.city')) }}"></div>
            <div><label for="district" class="input-label">Commune / quartier</label><input id="district" name="district" class="field" value="{{ old('district') }}"></div>
            <div class="sm:col-span-2"><label for="address" class="input-label">Adresse / repère *</label><textarea id="address" name="address" required class="field" rows="3">{{ old('address') }}</textarea></div>
            <div class="sm:col-span-2"><label for="notes" class="input-label">Consignes particulières</label><textarea id="notes" name="notes" class="field" rows="3">{{ old('notes') }}</textarea></div>
            <label class="sm:col-span-2 flex gap-3 items-start text-sm text-quiet">
                <input type="checkbox" name="consent" value="1" required class="mt-1" @checked(old('consent'))>
                <span>J'accepte le traitement de mes coordonnées pour cette demande selon la <a class="underline hover:text-rouge" target="_blank" rel="noopener" href="{{ route('legal.privacy') }}">politique de confidentialité</a>. Aucun paiement ni achat définitif n'est réalisé en ligne. *</span>
            </label>
            <div class="sm:col-span-2"><button type="submit" class="btn-red w-full">Envoyer ma demande de commande</button></div>
        </form>
        <aside class="bg-cloud border border-slate-200 rounded-md p-6">
            <h2 class="font-extrabold text-xl">Équipements sélectionnés</h2>
            <ul class="divide-y divide-slate-200 mt-4">
                @foreach($lines as $line)
                    <li class="py-3 text-sm">
                        <div class="font-semibold">{{ $line['product']->name }}</div>
                        <div class="flex justify-between text-quiet mt-1">
                            <span>{{ $line['quantity'] }} × {{ number_format($line['product']->price_gnf, 0, ',', ' ') }}</span>
                            <span>{{ number_format($line['subtotal'], 0, ',', ' ') }} GNF</span>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="mt-5 pt-5 border-t border-slate-200 flex justify-between font-extrabold">
                <span>Sous-total matériel</span><span>{{ number_format($subtotal, 0, ',', ' ') }} GNF</span>
            </div>
            <p class="text-quiet text-sm mt-4">Livraison et montant final confirmés par notre équipe.</p>
            <a class="block mt-5 text-sm text-rouge font-bold" href="{{ route('cart.index') }}">← Modifier le panier</a>
        </aside>
    </div>
</div>
@endsection
