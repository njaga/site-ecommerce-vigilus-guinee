@extends('layouts.store')
@section('title', 'Politique de confidentialité | Vigilus Guinée')
@section('robots', config('site.privacy_approved') ? 'index,follow' : 'noindex,follow')
@section('content')
<section class="container-wide max-w-3xl py-14">
    <p class="kicker">Données personnelles</p><h1 class="text-4xl font-extrabold mt-3">Politique de confidentialité</h1>
    @unless(config('site.privacy_approved'))<p class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm">Texte de travail : à faire valider et compléter par l'entité exploitante avant toute mise en ligne commerciale.</p>@endunless
    <div class="mt-8 space-y-8 text-quiet leading-relaxed">
        <section><h2 class="text-xl font-extrabold text-ink">Responsable du traitement</h2>
            <p class="mt-3">{{ config('site.legal_name') ?: 'Entité juridique à confirmer' }} — {{ config('site.legal_address') ?: 'Adresse à confirmer' }}.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">Données transmises</h2>
            <p class="mt-3">Lorsque vous demandez un devis ou soumettez une demande de commande, le site recueille les données renseignées dans le formulaire : nom, téléphone, email facultatif, entreprise facultative, adresse et informations utiles à la demande. Les demandes de commande contiennent les références choisies et leurs quantités.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">Utilisation des données</h2>
            <p class="mt-3">Les informations servent à répondre aux demandes commerciales, vérifier les disponibilités, préparer une proposition, organiser le suivi de la commande et communiquer avec le demandeur. Elles sont accessibles aux membres habilités de l'équipe et peuvent être transmises via la messagerie transactionnelle configurée par l'exploitant.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">Durée et modalités de conservation</h2>
            <p class="mt-3">{{ config('site.privacy_retention_notice') ?: 'Les durées de conservation, règles d’archivage et d’effacement doivent être précisées par l’exploitant dans la politique définitive avant lancement.' }}</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">Contact confidentialité</h2>
            <p class="mt-3">Pour toute question relative à vos informations ou demande les concernant, écrivez à {{ config('site.privacy_contact') ?: (config('site.email') ?: 'l’adresse à renseigner avant publication') }}. Les modalités de traitement de ces demandes doivent être confirmées avant ouverture.</p></section>
        <section><h2 class="text-xl font-extrabold text-ink">Paiement</h2>
            <p class="mt-3">Le site ne collecte aucune information de carte bancaire et ne propose pas de paiement en ligne. Les modalités de règlement font l'objet d'une confirmation commerciale séparée.</p></section>
    </div>
</section>
@endsection
