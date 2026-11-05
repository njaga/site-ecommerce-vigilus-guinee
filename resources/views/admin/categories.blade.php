@extends('layouts.admin')
@section('title', 'Catégories')
@section('content')
<h1 class="text-3xl font-extrabold mb-7">Catégories du catalogue</h1>
<div class="grid lg:grid-cols-[340px_1fr] gap-6 items-start">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white p-6 border border-slate-200 rounded-xl space-y-4">
        @csrf <h2 class="font-bold text-xl">Ajouter une catégorie</h2>
        <div><label for="new-name" class="input-label">Nom *</label><input id="new-name" name="name" required class="field"></div>
        <div><label for="new-slug" class="input-label">Slug *</label><input id="new-slug" name="slug" required class="field" pattern="[a-zA-Z0-9_-]+" placeholder="cameras-ip"></div>
        <div><label for="new-description" class="input-label">Description</label><textarea id="new-description" name="description" class="field" rows="3"></textarea></div>
        <div><label for="new-sort" class="input-label">Ordre</label><input id="new-sort" type="number" min="0" name="sort_order" class="field" value="0" required></div>
        <input type="hidden" name="is_active" value="1">
        <button class="btn-red w-full" type="submit">Créer</button>
    </form>
    <div class="space-y-3">
        @foreach($categories as $category)
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white border border-slate-200 rounded-xl p-5">
                @csrf @method('PUT')
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                    <div><label class="input-label" for="cat-name-{{ $category->id }}">Nom</label><input id="cat-name-{{ $category->id }}" name="name" class="field" value="{{ $category->name }}" required></div>
                    <div><label class="input-label" for="cat-desc-{{ $category->id }}">Description</label><input id="cat-desc-{{ $category->id }}" name="description" class="field" value="{{ $category->description }}"></div>
                    <div><label class="input-label" for="cat-sort-{{ $category->id }}">Ordre</label><input id="cat-sort-{{ $category->id }}" name="sort_order" type="number" class="field" value="{{ $category->sort_order }}" min="0" required></div>
                    <div><input type="hidden" name="is_active" value="0"><label class="flex gap-2 items-center text-sm mb-3"><input type="checkbox" name="is_active" value="1" @checked($category->is_active)> Active</label>
                        <button class="btn-light w-full" type="submit">Enregistrer</button></div>
                </div>
                <p class="mt-2 text-xs text-quiet">/categories/{{ $category->slug }} · slug protégé pour le SEO</p>
            </form>
        @endforeach
    </div>
</div>
@endsection
