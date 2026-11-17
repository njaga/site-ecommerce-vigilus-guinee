@if ($paginator->hasPages())
    <nav class="v-pagination" role="navigation" aria-label="{{ __('pagination.navigation') }}">
        @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <p class="v-pagination-summary">
                {{ __('pagination.summary', [
                    'from' => number_format($paginator->firstItem(), 0, ',', ' '),
                    'to' => number_format($paginator->lastItem(), 0, ',', ' '),
                    'total' => number_format($paginator->total(), 0, ',', ' '),
                ]) }}
            </p>
        @endif

        <div class="v-pagination-controls">
            @if ($paginator->onFirstPage())
                <span class="v-pagination-button v-pagination-direction is-disabled" aria-disabled="true">
                    <span aria-hidden="true">←</span> {{ __('pagination.previous') }}
                </span>
            @else
                <a class="v-pagination-button v-pagination-direction" href="{{ $paginator->previousPageUrl() }}"
                   rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <span aria-hidden="true">←</span> {{ __('pagination.previous') }}
                </a>
            @endif

            @if ($paginator instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="v-pagination-pages">
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="v-pagination-ellipsis" aria-hidden="true">…</span>
                        @elseif (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page === $paginator->currentPage())
                                    <span class="v-pagination-button v-pagination-number is-current"
                                          aria-current="page" aria-label="{{ __('pagination.page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a class="v-pagination-button v-pagination-number" href="{{ $url }}"
                                       aria-label="{{ __('pagination.page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @endif

            @if ($paginator->hasMorePages())
                <a class="v-pagination-button v-pagination-direction" href="{{ $paginator->nextPageUrl() }}"
                   rel="next" aria-label="{{ __('pagination.next') }}">
                    {{ __('pagination.next') }} <span aria-hidden="true">→</span>
                </a>
            @else
                <span class="v-pagination-button v-pagination-direction is-disabled" aria-disabled="true">
                    {{ __('pagination.next') }} <span aria-hidden="true">→</span>
                </span>
            @endif
        </div>
    </nav>
@endif
