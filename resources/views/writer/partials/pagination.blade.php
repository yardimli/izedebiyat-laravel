@if ($paginator->total() > 0)
    <nav class="library-pagination" aria-label="{{ __($paginationLabel ?? 'Works pagination') }}">
        <p class="muted">
            {{ __($summaryKey ?? 'Showing :first–:last of :total works', ['first' => $paginator->firstItem() ?? 0, 'last' => $paginator->lastItem() ?? 0, 'total' => $paginator->total()]) }}
        </p>
        <ul>
            @if ($paginator->onFirstPage())
                <li><span aria-disabled="true">{{ __('First page') }}</span></li>
                <li><span aria-disabled="true">← {{ __('Previous') }}</span></li>
            @else
                <li><a href="{{ $paginator->url(1) }}">{{ __('First page') }}</a></li>
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">← {{ __('Previous') }}</a></li>
            @endif
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="pagination-gap">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span aria-current="page"
                                    aria-label="{{ __('Page :page', ['page' => $page]) }}">{{ $page }}</span>
                            </li>
                        @else<li><a href="{{ $url }}"
                                    aria-label="{{ __('Page :page', ['page' => $page]) }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('Next') }} →</a></li>
                <li><a href="{{ $paginator->url($paginator->lastPage()) }}">{{ __('Last page') }}</a></li>
            @else
                <li><span aria-disabled="true">{{ __('Next') }} →</span></li>
                <li><span aria-disabled="true">{{ __('Last page') }}</span></li>
            @endif
        </ul>
    </nav>
@endif
