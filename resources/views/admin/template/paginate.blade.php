@if ($paginator->hasPages())
    <nav class="adminPagination" aria-label="Phân trang">
        <ul class="adminPagination_list">
            {{-- Previous Page Link --}}
            @if($paginator->onFirstPage())
                <li class="adminPagination_item adminPagination_item--disabled">
                    <span class="adminPagination_link" aria-label="Trang trước" aria-disabled="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </span>
                </li>
            @else
                <li class="adminPagination_item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="adminPagination_link adminPagination_link--prev" aria-label="Trang trước" rel="prev">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"/>
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="adminPagination_item adminPagination_item--active" aria-current="page">
                                <span class="adminPagination_link adminPagination_link--active">{{ $page }}</span>
                            </li>
                        @else
                            <li class="adminPagination_item">
                                <a href="{{ $url }}" class="adminPagination_link">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="adminPagination_item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="adminPagination_link adminPagination_link--next" aria-label="Trang sau" rel="next">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </a>
                </li>
            @else
                <li class="adminPagination_item adminPagination_item--disabled">
                    <span class="adminPagination_link" aria-label="Trang sau" aria-disabled="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Page Info --}}
        <div class="adminPagination_info">
            <span class="adminPagination_info_text">
                Trang <strong>{{ $paginator->currentPage() }}</strong> / <strong>{{ $paginator->lastPage() }}</strong>
            </span>
            <span class="adminPagination_info_separator">•</span>
            <span class="adminPagination_info_text">
                Tổng <strong>{{ $paginator->total() }}</strong> mục
            </span>
    </div>
    </nav>
@endif
