@extends('layouts.store')
@section('title', 'Mentions légales | Vigilus Guinée')
@section('robots', config('site.privacy_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<section class="container-wide max-w-3xl py-14">
    <p class="kicker">Informations de l'exploitant</p><h1 class="text-4xl font-extrabold mt-3">Mentions légales</h1>
    @unless(config('site.privacy_approved'))<p class="mt-5 p-4 border border-amber-200 bg-amber-50 rounded-lg text-sm">Document en cours de validation par l'exploitant. Les informations obligatoires doivent être renseignées avant lancement.</p>@endunless
    <dl class="mt-8 space-y-6">
        <div><dt class="font-bold">Entité exploitante</dt><dd class="text-quiet mt-1">{{ config('site.legal_name') ?: 'À renseigner avant publication' }}</dd></div>
        <div><dt class="font-bold">Adresse légale</dt><dd class="text-quiet mt-1">{{ config('site.legal_address') ?: 'À renseigner avant publication' }}</dd></div>
        <div><dt class="font-bold">Immatriculation</dt><dd class="text-quiet mt-1">{{ config('site.legal_registration') ?: 'À renseigner avant publication' }}</dd></div>
        <div><dt class="font-bold">Contact</dt><dd class="text-quiet mt-1">{{ config('site.email') ?: 'À renseigner avant publication' }}</dd></div>
    </dl>
    <p class="text-sm text-quiet mt-9">Les informations institutionnelles doivent être vérifiées auprès des personnes habilitées avant la mise en production.</p>
</section>
@endsection
