@extends('layouts.admin')
@section('title', $trainingSession->exists ? 'Modifier la session' : 'Créer une session')
@section('content')
<a class="text-sm text-rouge" href="{{ route('admin.training.index') }}">← Formations</a><h1 class="text-3xl font-extrabold mt-5 mb-7">{{ $trainingSession->exists ? 'Modifier la session' : 'Créer une session' }}</h1>
<form class="bg-white border border-slate-200 rounded-xl p-6 max-w-3xl grid sm:grid-cols-2 gap-5" action="{{ $trainingSession->exists ? route('admin.training.update', $trainingSession) : route('admin.training.store') }}" method="POST">
    @csrf @if($trainingSession->exists) @method('PUT') @endif
    <div class="sm:col-span-2"><label for="title" class="input-label">Intitulé *</label><input id="title" name="title" class="field" required maxlength="160" value="{{ old('title', $trainingSession->title) }}"></div>
    <div class="sm:col-span-2"><label for="description" class="input-label">Programme et modalités *</label><textarea id="description" name="description" class="field" required rows="6" maxlength="5000">{{ old('description', $trainingSession->description) }}</textarea></div>
    <div><label for="starts_at" class="input-label">Date et heure (Conakry) *</label><input id="starts_at" name="starts_at" type="datetime-local" class="field" required value="{{ old('starts_at', $trainingSession->starts_at?->format('Y-m-d\TH:i')) }}"></div>
    <div><label for="location" class="input-label">Lieu ou format à distance *</label><input id="location" name="location" class="field" required maxlength="200" value="{{ old('location', $trainingSession->location) }}"></div>
    <div class="sm:col-span-2"><label for="audience" class="input-label">Public concerné *</label><input id="audience" name="audience" class="field" required maxlength="160" value="{{ old('audience', $trainingSession->audience) }}" placeholder="Installateurs, équipes techniques…"></div>
    <div class="sm:col-span-2"><input type="hidden" name="is_published" value="0"><label class="flex gap-3 items-center"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $trainingSession->is_published))> Publier sur la page Formations</label><p class="text-xs text-quiet mt-2">Seules les sessions publiées dont la date est à venir apparaissent sur le site. Décochez pour retirer une session du calendrier.</p></div>
    <button class="btn-red sm:col-span-2" type="submit">Enregistrer la session</button>
</form>
@endsection
