@extends('layouts.store')
@section('title', 'Demande reçue | Vigilus Guinée')
@section('robots', 'noindex,nofollow')
@section('content')
<div class="container-wide max-w-2xl py-24">
    <div class="border border-slate-200 rounded-md p-9 sm:p-12 text-center">
        <span class="text-4xl text-emerald-600" aria-hidden="true">✓</span>
        <p class="section-eyebrow mt-5">Demande enregistrée</p>
        <h1 class="text-3xl font-extrabold mt-4">Votre demande est bien enregistrée.</h1>
        <p class="text-quiet mt-5">Notre équipe prendra contact avec vous pour la suite.</p>
        <div class="mt-7 bg-cloud p-5 rounded-lg">
            <div class="text-xs text-quiet">Référence de suivi</div>
            <strong class="font-extrabold text-2xl tracking-wide">{{ $reference }}</strong>
        </div>
        <p class="text-sm text-quiet mt-5">Aucun paiement n’a été effectué.</p>
        <div class="mt-7 flex justify-center gap-3 flex-wrap">
            <a class="btn-red" href="{{ $trackingUrl }}">Suivre ma demande</a>
            <a class="btn-light" href="{{ route('home') }}">Retour à l’accueil</a>
        </div>
        <p class="text-xs text-quiet mt-5">Conservez ce lien privé pour suivre votre demande. Il expire au bout de {{ config('commerce.tracking_link_days') }} jours.</p>
    </div>
</div>
@endsection
