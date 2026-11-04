@extends('layouts.store')
@section('title', 'Demande reçue | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide max-w-2xl py-24">
    <div class="border border-slate-200 rounded-2xl p-9 sm:p-12 text-center">
        <span class="text-4xl text-emerald-600" aria-hidden="true">✓</span>
        <p class="kicker mt-5">Demande enregistrée</p>
        <h1 class="text-3xl font-extrabold mt-3">Merci pour votre confiance.</h1>
        <p class="text-quiet mt-5">Votre demande de commande a bien été enregistrée. Un conseiller devra confirmer les disponibilités, les frais éventuels et les modalités de règlement.</p>
        <div class="mt-7 bg-cloud p-5 rounded-lg">
            <div class="text-xs text-quiet">Référence de suivi</div>
            <strong class="font-extrabold text-2xl tracking-wide">{{ $reference }}</strong>
        </div>
        <p class="text-sm text-quiet mt-5">Ceci n'est ni une preuve de paiement ni une confirmation de livraison.</p>
        <a class="btn-red mt-7" href="{{ route('home') }}">Retour à l’accueil</a>
    </div>
</div>
@endsection
