@php $featured = ($variant ?? '') === 'featured'; @endphp
<article class="journal-card {{ $featured ? 'journal-card-featured' : '' }}">
    <div class="journal-card-date">
        <span>{{ mb_strtoupper($post->published_at->locale('fr')->translatedFormat('M')) }}</span>
        <strong>{{ $post->published_at->format('d') }}</strong>
    </div>
    <a href="{{ route('blog.show', $post->slug) }}" class="journal-card-image" aria-label="Lire {{ $post->title }}">
        @if($post->cover_url)
            <img src="{{ $post->cover_url }}" alt="{{ $post->cover_alt }}"
                width="{{ $featured ? 900 : 550 }}" height="{{ $featured ? 650 : 400 }}" loading="{{ $featured ? 'eager' : 'lazy' }}">
        @else
            <span class="journal-card-image-empty">Vigilus Guinée</span>
        @endif
    </a>
    <div class="journal-card-content">
        <p class="journal-card-category">{{ $post->category }}</p>
        <h2 class="journal-card-title"><a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a></h2>
        <p class="journal-card-excerpt">{{ $post->excerpt }}</p>
        <a class="journal-card-read" href="{{ route('blog.show', $post->slug) }}" aria-label="Lire l'article : {{ $post->title }}">
            Lire l’article <span aria-hidden="true">↗</span>
        </a>
    </div>
</article>
