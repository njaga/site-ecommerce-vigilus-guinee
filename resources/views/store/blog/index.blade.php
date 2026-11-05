@extends('layouts.store')
@section('title', 'Journal & conseils sécurité électronique | Vigilus Guinée')
@section('meta_description', 'Articles, conseils et actualités sur les caméras, la vidéosurveillance, les enregistreurs et les équipements de sécurité électronique en Guinée.')
@section('canonical', route('solution.videosurveillance'))
@section('content')
<section class="container-wide journal-page">
    <div class="journal-section-label">
        <span>Le journal Vigilus</span>
        <span aria-hidden="true"></span>
    </div>
    <div class="journal-heading">
        <h1>Actualités<span>.</span></h1>
        <p>Décryptages, nouveautés et conseils pour mieux choisir vos équipements.</p>
    </div>

    @if($primary)
        <div class="journal-feature-grid">
            @include('store.blog.partials.card', ['post' => $primary, 'variant' => 'featured'])
            @foreach($secondary as $post)
                @include('store.blog.partials.card', ['post' => $post])
            @endforeach
        </div>
        @if($remaining->isNotEmpty())
            <div class="journal-more-header">
                <h2>À découvrir</h2><span></span>
            </div>
            <div class="journal-more-grid">
                @foreach($remaining as $post)
                    @include('store.blog.partials.card', ['post' => $post])
                @endforeach
            </div>
        @endif
        <div class="journal-pagination">{{ $posts->links() }}</div>
    @else
        <div class="journal-empty">
            <h2>Le journal arrive bientôt.</h2>
            <p>En attendant, retrouvez notre sélection d'équipements de sécurité électronique.</p>
            <a class="journal-empty-link" href="{{ route('catalog') }}">Voir le catalogue <span aria-hidden="true">↗</span></a>
        </div>
    @endif
</section>
@endsection
