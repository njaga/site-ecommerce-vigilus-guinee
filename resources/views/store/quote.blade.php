@extends('layouts.store')
@section('title', "Demander un devis d'équipements | Vigilus Guinée")
@section('robots', 'noindex,follow')
@section('content')
<section class="bg-cloud border-b border-slate-200">
    <div class="container-wide py-12">
        <p class="section-eyebrow">Votre projet</p>
        <h1 class="text-3xl sm:text-4xl font-extrabold mt-5">Décrivez votre besoin, nous préparons la suite.</h1>
        <p class="text-quiet mt-4 max-w-2xl leading-relaxed">Une référence Dahua, un besoin en quantité ou un projet d’équipement à Conakry ? Envoyez-nous vos spécifications pour recevoir une proposition commerciale.</p>
    </div>
</section>
<div class="container-wide max-w-3xl py-12">
    @if(session('success')) <div role="status" class="bg-emerald-50 text-emerald-900 rounded-lg p-4 mb-5">{{ session('success') }}</div> @endif
    @if($errors->any())
        <div role="alert" class="bg-red-50 text-red-900 rounded-lg p-4 mb-5"><p class="font-bold">Merci de vérifier les informations :</p>
            <ul class="list-disc ml-6 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    @if($product)
        <div class="border border-slate-200 p-5 mb-5 rounded-md">
            <div class="text-xs text-quiet mb-1">Produit concerné</div>
            <strong>{{ $product->name }}</strong> — <span class="text-sm">{{ $product->sku }}</span>
        </div>
    @endif
    <form method="POST" action="{{ route('quote.store') }}" class="grid sm:grid-cols-2 gap-5 bg-white border border-slate-200 rounded-md p-6 sm:p-9">
        @csrf
        @if($product)<input type="hidden" name="product_id" value="{{ $product->id }}">@endif
        <div class="hidden" aria-hidden="true"><label>Site web<input type="text" tabindex="-1" autocomplete="off" name="website"></label></div>
        <div><label for="name" class="input-label">Nom et prénom *</label><input id="name" name="name" required class="field" value="{{ old('name') }}" autocomplete="name"></div>
        <div><label for="phone" class="input-label">Téléphone *</label><input id="phone" name="phone" type="tel" required class="field" value="{{ old('phone') }}" autocomplete="tel"></div>
        <div><label for="email" class="input-label">Email</label><input id="email" name="email" type="email" class="field" value="{{ old('email') }}" autocomplete="email"></div>
        <div><label for="company" class="input-label">Société (facultatif)</label><input id="company" name="company" class="field" value="{{ old('company') }}"></div>
        <div><label for="quantity" class="input-label">Quantité souhaitée *</label><input id="quantity" name="quantity" type="number" min="1" max="100000" required class="field" value="{{ old('quantity', 1) }}"></div>
        <div class="sm:col-span-2"><label for="message" class="input-label">Votre besoin</label><textarea id="message" name="message" rows="5" class="field" placeholder="Références, quantités, type de site, commune, délai de livraison souhaité…">{{ old('message') }}</textarea></div>
        <div class="sm:col-span-2">
            <label class="flex gap-3 items-start text-sm text-quiet"><input type="checkbox" name="consent" value="1" required class="mt-1"> <span>J'accepte que Vigilus traite mes coordonnées pour répondre à cette demande, conformément à la <a class="underline hover:text-rouge" target="_blank" rel="noopener" href="{{ route('legal.privacy') }}">politique de confidentialité</a>. *</span></label>
        </div>
        <div class="sm:col-span-2"><button type="submit" class="btn-red w-full sm:w-auto">Envoyer ma demande →</button></div>
    </form>
</div>
@endsection
