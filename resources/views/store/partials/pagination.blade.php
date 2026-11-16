@if ($paginator->hasPages())
<nav class="catalog-pagination" aria-label="Pagination du catalogue">
    @if ($paginator->onFirstPage())
        <span class="pagination-control is-disabled" aria-disabled="true"><x-store-icon name="arrow-left" :size="16"/><span>Précédent</span></span>
    @else
        <a class="pagination-control" href="{{ $paginator->previousPageUrl() }}" rel="prev"><x-store-icon name="arrow-left" :size="16"/><span>Précédent</span></a>
    @endif

    <div class="pagination-pages">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-ellipsis">{{ $element }}</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-page is-current" aria-current="page" aria-label="Page {{ $page }}">{{ $page }}</span>
                    @else
                        <a class="pagination-page" href="{{ $url }}" aria-label="Page {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>
    <span class="pagination-mobile-status">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>

    @if ($paginator->hasMorePages())
        <a class="pagination-control" href="{{ $paginator->nextPageUrl() }}" rel="next"><span>Suivant</span><x-store-icon name="arrow-right" :size="16"/></a>
    @else
        <span class="pagination-control is-disabled" aria-disabled="true"><span>Suivant</span><x-store-icon name="arrow-right" :size="16"/></span>
    @endif
</nav>
@endif
