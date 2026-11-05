@extends('layouts.store')
@section('title', ($post->seo_title ?: $post->title.' | Journal Vigilus Guinée'))
@section('meta_description', $post->seo_description ?: $post->excerpt)
@section('canonical', route('blog.show', $post->slug))
@section('robots', !empty($preview) ? 'noindex,nofollow' : 'index,follow')
@if(empty($preview))
@push('jsonld')
@php
    $articleSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $post->title,
        'description' => $post->seo_description ?: $post->excerpt,
        'datePublished' => $post->published_at?->toIso8601String(),
        'dateModified' => $post->updated_at->toIso8601String(),
        'mainEntityOfPage' => route('blog.show', $post->slug),
        'publisher' => ['@type' => 'Organization', 'name' => 'Vigilus Guinée'],
    ];
    if ($post->cover_url) {
        $articleSchema['image'] = $post->cover_url;
    }
@endphp
<script type="application/ld+json">@json($articleSchema)</script>
@endpush
@endif
@section('content')
@if(!empty($preview))
    <div class="container-wide mt-6 bg-amber-50 border border-amber-200 p-4 text-sm">
        Aperçu réservé à l’administration. <a class="underline font-semibold" href="{{ route('admin.blog.edit', $post) }}">Retour à l’édition</a>
    </div>
@endif
<article class="journal-article">
    <div class="container-wide">
        <nav class="journal-breadcrumb" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}">Accueil</a><span>/</span>
            <a href="{{ route('solution.videosurveillance') }}">Journal</a><span>/</span>
            <span>{{ $post->category }}</span>
        </nav>
        <header class="journal-article-heading">
            <p class="journal-article-category">{{ $post->category }}</p>
            <h1>{{ $post->title }}</h1>
            <p class="journal-article-lead">{{ $post->excerpt }}</p>
            <div class="journal-article-meta">
                <span>Vigilus Guinée</span>
                <span aria-hidden="true">·</span>
                <time datetime="{{ $post->published_at?->toDateString() ?? $post->created_at->toDateString() }}">
                    {{ ($post->published_at ?? $post->created_at)->locale('fr')->translatedFormat('d F Y') }}
                </time>
            </div>
        </header>
        @if($post->cover_url)
            <figure class="journal-article-cover">
                <img src="{{ $post->cover_url }}" alt="{{ $post->cover_alt }}" width="1250" height="760" fetchpriority="high">
            </figure>
        @endif
        <div class="journal-article-body">
            {!! \Illuminate\Support\Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false]) !!}
        </div>
        <div class="journal-article-end">
            <a href="{{ route('solution.videosurveillance') }}">← Tous les articles</a>
            <a href="{{ route('catalog') }}">Explorer les équipements ↗</a>
        </div>
    </div>
</article>
@if($related->isNotEmpty())
<section class="container-wide journal-related">
    <div class="journal-more-header"><h2>À lire aussi</h2><span></span></div>
    <div class="journal-more-grid">
        @foreach($related as $post)
            @include('store.blog.partials.card', ['post' => $post])
        @endforeach
    </div>
</section>
@endif
@endsection
