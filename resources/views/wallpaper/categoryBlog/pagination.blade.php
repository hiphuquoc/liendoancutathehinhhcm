@if ($paginator->hasPages())
    <nav class="pagination-nav" aria-label="Phân trang">
        <ul class="pagination-nav__list" role="list">
            @if ($paginator->onFirstPage())
                <li class="pagination-nav__item pagination-nav__item--disabled" aria-disabled="true">
                    <span class="pagination-nav__link">Trước</span>
                </li>
            @else
                <li class="pagination-nav__item">
                    <a class="pagination-nav__link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Trước</a>
                </li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="pagination-nav__item pagination-nav__item--ellipsis" aria-hidden="true"><span>{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="pagination-nav__item {{ $page == $paginator->currentPage() ? 'pagination-nav__item--active' : '' }}">
                            @if ($page == $paginator->currentPage())
                                <span class="pagination-nav__link pagination-nav__link--current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="pagination-nav__link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li class="pagination-nav__item">
                    <a class="pagination-nav__link" href="{{ $paginator->nextPageUrl() }}" rel="next">Sau</a>
                </li>
            @else
                <li class="pagination-nav__item pagination-nav__item--disabled" aria-disabled="true">
                    <span class="pagination-nav__link">Sau</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
