@extends('layouts.admin')
@section('title', 'Articles')
@section('content')
<div class="flex flex-wrap justify-between gap-5 items-end mb-8">
    <div>
        <p class="kicker">Éditorial</p>
        <h1 class="text-3xl font-extrabold mt-2">Articles</h1>
    </div>
    <a class="btn-red" href="{{ route('admin.blog.create') }}">Nouvel article <span aria-hidden="true">+</span></a>
</div>
<nav aria-label="Filtrer les articles" class="flex items-center gap-5 text-sm border-b border-slate-200 mb-6">
    @foreach(['' => 'Tous', 'published' => 'Publiés', 'draft' => 'Brouillons'] as $key => $label)
        <a href="{{ route('admin.blog.index', $key ? ['statut' => $key] : []) }}"
            class="pb-3 font-semibold border-b-2 {{ $selectedStatus === $key ? 'border-rouge text-rouge' : 'border-transparent text-quiet' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>
<div class="bg-white border border-slate-200 overflow-x-auto">
    <table class="w-full min-w-[700px] text-left text-sm">
        <thead class="bg-cloud text-xs uppercase tracking-wide text-quiet">
            <tr><th class="p-4">Article</th><th class="p-4">Rubrique</th><th class="p-4">Statut</th><th class="p-4">Date</th><th class="p-4 text-right">Actions</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
        @forelse($posts as $post)
            <tr>
                <td class="p-4">
                    <strong class="block font-extrabold">{{ $post->title }}</strong>
                    <span class="text-xs text-quiet">{{ $post->slug }}</span>
                </td>
                <td class="p-4">{{ $post->category }}</td>
                <td class="p-4">
                    @if($post->status === 'published' && $post->published_at?->isFuture())
                        <span class="text-amber-700 font-bold">Programmé</span>
                    @else
                        <span class="font-bold {{ $post->status === 'published' ? 'text-emerald-700' : 'text-quiet' }}">{{ $post->status === 'published' ? 'Publié' : 'Brouillon' }}</span>
                    @endif
                </td>
                <td class="p-4 text-quiet">{{ ($post->published_at ?? $post->created_at)->format('d/m/Y') }}</td>
                <td class="p-4 text-right">
                    <a class="font-extrabold text-rouge hover:underline" href="{{ route('admin.blog.edit', $post) }}">Modifier</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-12 text-center text-quiet">Aucun article pour le moment.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $posts->links() }}</div>
@endsection
