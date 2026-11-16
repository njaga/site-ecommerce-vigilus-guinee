@extends('layouts.store')
@section('title', 'Formations Dahua pour installateurs et entreprises | Vigilus Guinée')
@section('meta_description', 'Découvrez les sessions de formation Dahua de Vigilus Guinée et demandez une formation adaptée aux besoins de votre entreprise.')
@section('canonical', route('training.index'))
@section('robots', request()->has('session') ? 'noindex,follow' : 'index,follow')
@section('content')
<section class="training-page-hero container-wide">
    <div><p class="section-eyebrow">Formations · Vigilus Guinée</p><h1>Le savoir-faire,<br>au service de vos projets.</h1><p>Installateurs, équipes techniques et entreprises : développez vos compétences autour des solutions Dahua.</p><div class="flex flex-wrap gap-3 mt-7"><a class="btn-red" href="#sessions">Voir les sessions</a><a class="btn-light" href="#demande">Organiser une session</a></div></div>
    <div class="training-hero-brand"><img src="{{ asset(config('group.dahua_logo')) }}" alt="Dahua Technology" width="280" height="90"><p>{{ config('group.dahua_statement') }}</p><span>Des sessions pour vos équipes et vos projets.</span></div>
</section>
<section id="sessions" class="container-wide storefront-section">
    <div class="section-topline"><div><p class="section-eyebrow">Le calendrier</p><h2 class="section-heading">Nos prochaines sessions.</h2></div><span class="text-sm text-quiet">Horaires de Conakry</span></div>
    @if($sessions->isEmpty())
        <div class="training-empty"><x-store-icon name="file" :size="28"/><div><h3>Le prochain calendrier se prépare.</h3><p>Vous pouvez déjà demander une session pour votre entreprise ou nous faire part de votre intérêt.</p></div><a class="section-link" href="#demande">Nous contacter <x-store-icon name="arrow-right" :size="16"/></a></div>
    @else
        <div class="training-sessions">
            @foreach($sessions as $session)
                <article class="training-session-card"><div class="training-session-date"><strong>{{ $session->starts_at->format('d') }}</strong><span>{{ $session->starts_at->locale('fr')->translatedFormat('M Y') }}</span></div><div><span class="training-session-audience">{{ $session->audience }}</span><h3>{{ $session->title }}</h3><p class="training-session-description">{{ $session->description }}</p><p class="training-session-meta">{{ $session->starts_at->format('H:i') }} · {{ $session->location }}</p><a class="section-link" href="{{ route('training.index', ['session' => $session->id]) }}#demande">Demander à participer <x-store-icon name="arrow-right" :size="16"/></a></div></article>
            @endforeach
        </div>
    @endif
</section>
<section id="demande" class="container-wide storefront-section training-request-layout">
    <div><p class="section-eyebrow">Construisons votre session</p><h2 class="section-heading mt-4">Une formation adaptée<br>à votre équipe.</h2><p class="training-request-intro">Précisez le nombre de participants, vos objectifs et la période souhaitée. Notre équipe vous recontactera pour définir le programme et les modalités.</p><p class="text-sm text-quiet mt-5">Votre demande ne confirme pas une inscription. Dates, disponibilité et conditions seront convenues avec vous.</p></div>
    <div>
        @if(session('success'))<div role="status" class="bg-emerald-50 text-emerald-900 p-5 rounded-lg mb-5">{{ session('success') }}</div>@endif
        @if($errors->any())<div role="alert" class="bg-red-50 text-red-900 p-5 rounded-lg mb-5"><strong>Vérifiez les informations suivantes :</strong><ul class="list-disc pl-5 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form action="{{ route('training.store') }}" method="POST" class="training-request-form" data-training-form>
            @csrf
            <div class="hidden" aria-hidden="true"><label>Site web<input name="website" type="text" tabindex="-1" autocomplete="off"></label></div>
            <div class="sm:col-span-2"><label for="request_type" class="input-label">Votre demande *</label><select name="request_type" id="request_type" class="field" required><option value="entreprise" @selected(old('request_type', $selectedSession ? 'session' : 'entreprise') === 'entreprise')>Organiser une session pour mon entreprise</option>@if($sessions->isNotEmpty())<option value="session" @selected(old('request_type', $selectedSession ? 'session' : 'entreprise') === 'session')>Participer à une session programmée</option>@endif<option value="information" @selected(old('request_type') === 'information')>Être informé des prochaines sessions</option></select></div>
            @if($sessions->isNotEmpty())<div class="sm:col-span-2" data-session-field><label for="training_session_id" class="input-label">Session (pour une participation)</label><select name="training_session_id" id="training_session_id" class="field"><option value="">Choisir une session</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected((string) old('training_session_id', $selectedSession?->id) === (string) $session->id)>{{ $session->starts_at->format('d/m/Y') }} · {{ $session->title }}</option>@endforeach</select></div>@endif
            <div><label for="training-name" class="input-label">Nom complet *</label><input id="training-name" name="name" class="field" required maxlength="150" autocomplete="name" value="{{ old('name') }}"></div>
            <div><label for="training-company" class="input-label">Entreprise <span data-company-required>*</span></label><input id="training-company" name="company" class="field" maxlength="150" autocomplete="organization" value="{{ old('company') }}" aria-describedby="company-help"><small id="company-help" class="text-xs text-quiet">Obligatoire pour une session en entreprise.</small></div>
            <div><label for="training-email" class="input-label">Email *</label><input id="training-email" name="email" type="email" class="field" required maxlength="255" autocomplete="email" value="{{ old('email') }}"></div>
            <div><label for="training-phone" class="input-label">Téléphone *</label><input id="training-phone" name="phone" type="tel" class="field" required maxlength="40" autocomplete="tel" value="{{ old('phone') }}"></div>
            <div><label for="participants" class="input-label">Participants *</label><input id="participants" name="participants" type="number" min="1" max="1000" class="field" required value="{{ old('participants', 1) }}"></div>
            <div><label for="preferred_period" class="input-label">Période souhaitée</label><input id="preferred_period" name="preferred_period" class="field" maxlength="150" placeholder="Ex. : janvier, en semaine" value="{{ old('preferred_period') }}"></div>
            <div class="sm:col-span-2"><label for="training-message" class="input-label">Vos objectifs</label><textarea id="training-message" name="message" rows="4" maxlength="3000" class="field" placeholder="Équipements concernés, niveau de votre équipe, besoins particuliers…">{{ old('message') }}</textarea></div>
            <label class="sm:col-span-2 flex items-start gap-3 text-sm text-quiet"><input name="consent" type="checkbox" value="1" required class="mt-1" @checked(old('consent'))><span>J’accepte le traitement de mes coordonnées pour cette demande selon la <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" class="underline">politique de confidentialité</a>. *</span></label>
            <button type="submit" class="btn-red sm:col-span-2">Envoyer ma demande <x-store-icon name="arrow-right" :size="17"/></button>
        </form>
    </div>
</section>
@endsection
