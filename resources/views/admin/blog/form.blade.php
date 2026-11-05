@extends('layouts.admin')
@section('title', $post->exists ? 'Modifier un article' : 'Nouvel article')
@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-8">
    <div>
        <a href="{{ route('admin.blog.index') }}" class="text-sm text-quiet hover:text-rouge">← Tous les articles</a>
        <h1 class="text-3xl font-extrabold mt-3">{{ $post->exists ? 'Modifier un article' : 'Nouvel article' }}</h1>
    </div>
    @if($post->exists)
        <a href="{{ route('admin.blog.preview', $post) }}" target="_blank" rel="noopener noreferrer" class="btn-light text-sm">Prévisualiser ↗</a>
    @endif
</div>
<form method="POST" enctype="multipart/form-data"
      action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}"
      class="grid lg:grid-cols-[minmax(0,1fr)_290px] gap-7 items-start">
    @csrf
    @if($post->exists) @method('PUT') @endif
    <div class="space-y-6 min-w-0">
        <div class="bg-white border border-slate-200 p-6 sm:p-8 space-y-5">
            <div>
                <label for="blog-title" class="input-label">Titre *</label>
                <input id="blog-title" name="title" class="field" required minlength="8" maxlength="220"
                    value="{{ old('title', $post->title) }}" placeholder="Un titre clair et précis">
            </div>
            @if($post->exists)
                <p class="text-xs text-quiet">URL : /blog/{{ $post->slug }}</p>
            @endif
            <div>
                <label for="blog-category" class="input-label">Rubrique *</label>
                <input id="blog-category" name="category" class="field" maxlength="70" required
                    value="{{ old('category', $post->category) }}" placeholder="Conseils, actualités, équipements…">
            </div>
            <div>
                <label for="blog-excerpt" class="input-label">Introduction *</label>
                <textarea id="blog-excerpt" name="excerpt" class="field" required minlength="45" maxlength="360"
                    rows="3" placeholder="Deux phrases visibles sur la carte et sous le titre">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            <div>
                <label for="blog-body" class="input-label">Article *</label>
                <div class="blog-editor-toolbar" role="toolbar" aria-label="Mise en forme de l'article">
                    <button type="button" data-blog-tool="heading">Titre</button>
                    <button type="button" data-blog-tool="bold"><strong>Gras</strong></button>
                    <button type="button" data-blog-tool="list">Liste</button>
                    <button type="button" data-blog-tool="link">Lien</button>
                    <button type="button" data-blog-tool="quote">Citation</button>
                </div>
                <textarea id="blog-body" name="body" class="field blog-editor" required minlength="180" rows="20"
                    placeholder="Rédigez votre article…">{{ old('body', $post->body) }}</textarea>
                <p class="text-xs text-quiet mt-2">Sélectionnez du texte puis utilisez la barre d’outils pour le mettre en forme.</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 p-6 sm:p-8 space-y-5">
            <h2 class="text-lg font-extrabold">Image de couverture</h2>
            @if($post->cover_url)
                <img src="{{ $post->cover_url }}" alt="{{ $post->cover_alt }}" class="w-full max-h-72 object-cover" loading="lazy">
            @endif
            <div>
                <label for="blog-cover" class="input-label">Photo (JPG, PNG ou WebP)</label>
                <input type="file" id="blog-cover" name="cover_image" accept="image/jpeg,image/png,image/webp"
                    class="field h-auto text-sm">
            </div>
            <div>
                <label for="blog-cover-alt" class="input-label">Description de la photo *</label>
                <input id="blog-cover-alt" name="cover_alt" class="field" maxlength="180"
                    value="{{ old('cover_alt', $post->cover_alt) }}" placeholder="Description pour l’accessibilité">
            </div>
        </div>
        <details class="bg-white border border-slate-200 p-6 sm:p-8">
            <summary class="font-extrabold cursor-pointer">Référencement SEO</summary>
            <div class="space-y-5 mt-6">
                <div><label class="input-label" for="blog-seo-title">Titre SEO</label>
                    <input id="blog-seo-title" class="field" name="seo_title" maxlength="75"
                           value="{{ old('seo_title', $post->seo_title) }}">
                </div>
                <div><label class="input-label" for="blog-seo-desc">Description SEO</label>
                    <textarea id="blog-seo-desc" name="seo_description" class="field" rows="3" maxlength="165">{{ old('seo_description', $post->seo_description) }}</textarea>
                </div>
            </div>
        </details>
    </div>
    <aside class="bg-white border border-slate-200 p-6 space-y-5 lg:sticky lg:top-5">
        <h2 class="font-extrabold text-xl">Publication</h2>
        <div>
            <label for="blog-status" class="input-label">Statut</label>
            <select id="blog-status" class="field" name="status" required>
                <option value="draft" @selected(old('status', $post->status) === 'draft')>Brouillon</option>
                <option value="published" @selected(old('status', $post->status) === 'published')>Publié</option>
            </select>
        </div>
        <div>
            <label for="blog-date" class="input-label">Date de publication</label>
            <input type="datetime-local" id="blog-date" class="field" name="published_at"
                value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <label class="flex items-start gap-3 text-sm">
            <input type="checkbox" name="is_featured" value="1" class="accent-rouge mt-1"
                @checked(old('is_featured', $post->is_featured))>
            <span>Mettre à la une</span>
        </label>
        <button type="submit" class="btn-red w-full">Enregistrer</button>
        @if($post->exists && $post->status === 'published')
            <p class="text-xs text-quiet">Pour retirer cet article du site, passez-le en brouillon puis enregistrez.</p>
        @endif
    </aside>
</form>
@if($post->exists && $post->status === 'draft' && $post->published_at === null)
    <form method="POST" action="{{ route('admin.blog.destroy', $post) }}"
          onsubmit="return confirm('Supprimer définitivement ce brouillon ?')" class="mt-7">
        @csrf @method('DELETE')
        <button type="submit" class="text-sm font-semibold text-rouge hover:underline">Supprimer ce brouillon</button>
    </form>
@endif
@endsection
