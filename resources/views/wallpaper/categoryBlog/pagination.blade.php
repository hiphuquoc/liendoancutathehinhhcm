@if ($paginator->hasPages())
    <div class="bottom-nav">
        <ul>
            <!-- Liên kết "Previous" -->
            @if ($paginator->onFirstPage())
                <li class="bottom-link unaction"><span>Previous</span></li>
            @else
                <li class="bottom-link"><a href="{{ $paginator->previousPageUrl() }}">Previous</a></li>
            @endif

            <!-- Các số trang -->
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="bottom-number-link unaction"><span>{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="bottom-number-link active"><a href="{{ $url }}">{{ $page }}</a></li>
                        @else
                            <li class="bottom-number-link"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Liên kết "Next" -->
            @if ($paginator->hasMorePages())
                <li class="bottom-link"><a href="{{ $paginator->nextPageUrl() }}">Next</a></li>
            @else
                <li class="bottom-link unaction"><span>Next</span></li>
            @endif
        </ul>
    </div>
@endif
