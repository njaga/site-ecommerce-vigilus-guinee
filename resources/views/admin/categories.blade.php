@extends('layouts.admin')
@section('title', 'Catégories et SEO')
@section('content')
<div class="mb-7">
    <p class="kicker">Architecture catalogue</p>
    <h1 class="text-3xl font-extrabold mt-1">Catégories et SEO</h1>
    <p class="text-quiet mt-3 max-w-3xl">Chaque catégorie active possède une page indexable et une URL stable. Renseignez des textes spécifiques à son contenu plutôt que des mots-clés répétés.</p>
</div>
<div class="grid lg:grid-cols-[340px_minmax(0,1fr)] gap-6 items-start">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white p-6 border border-slate-200 rounded-xl space-y-4">
        @csrf <h2 class="font-bold text-xl">Nouvelle catégorie</h2>
        <div><label for="new-name" class="input-label">Nom *</label><input id="new-name" name="name" required class="field"></div>
        <div><label for="new-slug" class="input-label">URL (slug) *</label><input id="new-slug" name="slug" required class="field" pattern="[a-zA-Z0-9_-]+" placeholder="cameras-ip"></div>
        <div><label for="new-description" class="input-label">Texte de présentation</label><textarea id="new-description" name="description" class="field" rows="4"></textarea></div>
        <div><label for="new-seo-title" class="input-label">Titre SEO</label><input id="new-seo-title" name="seo_title" maxlength="75" class="field"></div>
        <div><label for="new-seo-description" class="input-label">Méta-description</label><textarea id="new-seo-description" name="seo_description" maxlength="170" class="field" rows="3"></textarea></div>
        <div><label for="new-sort" class="input-label">Ordre</label><input id="new-sort" type="number" min="0" name="sort_order" class="field" value="0" required></div>
        <input type="hidden" name="is_active" value="0">
        <label class="flex gap-2 text-sm items-center"><input type="checkbox" name="is_active" value="1"> Activer immédiatement la catégorie</label>
        <button class="btn-red w-full" type="submit">Créer la catégorie</button>
    </form>
    <div class="space-y-4">
        @foreach($categories as $category)
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white border border-slate-200 rounded-xl p-5">
                @csrf @method('PUT')
                <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
                    <h2 class="font-bold">{{ $category->name }}</h2>
                    <span class="text-xs {{ $category->is_active ? 'text-emerald-700' : 'text-quiet' }}">{{ $category->is_active ? 'Active' : 'Masquée' }}</span>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div><label class="input-label" for="cat-name-{{ $category->id }}">Nom</label><input id="cat-name-{{ $category->id }}" name="name" class="field" value="{{ $category->name }}" required></div>
                    <div><label class="input-label" for="cat-sort-{{ $category->id }}">Ordre</label><input id="cat-sort-{{ $category->id }}" name="sort_order" type="number" class="field" value="{{ $category->sort_order }}" min="0" required></div>
                    <div class="sm:col-span-2"><label class="input-label" for="cat-desc-{{ $category->id }}">Introduction commerciale française</label><textarea id="cat-desc-{{ $category->id }}" name="description" class="field" rows="4">{{ $category->description }}</textarea></div>
                    <div class="sm:col-span-2"><label class="input-label" for="cat-seo-title-{{ $category->id }}">Titre SEO (75 caractères maximum)</label><input id="cat-seo-title-{{ $category->id }}" name="seo_title" maxlength="75" class="field" value="{{ $category->seo_title }}"></div>
                    <div class="sm:col-span-2"><label class="input-label" for="cat-seo-desc-{{ $category->id }}">Méta-description (170 caractères maximum)</label><textarea id="cat-seo-desc-{{ $category->id }}" name="seo_description" maxlength="170" class="field" rows="3">{{ $category->seo_description }}</textarea></div>
                    <div><input type="hidden" name="is_active" value="0"><label class="flex gap-2 items-center text-sm"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Catégorie active</label></div>
                    <button type="submit" class="btn-light">Enregistrer cette catégorie</button>
                </div>
                <p class="mt-4 text-xs text-quiet">/categories/{{ $category->slug }} · slug conservé pour éviter la perte de positionnement SEO.</p>
            </form>
        @endforeach
    </div>
</div>
@endsection
