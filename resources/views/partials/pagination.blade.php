@if ($paginator->hasPages())
    <div class="pagination-container">
        <div class="pagination-info">
            Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
        </div>
        <nav>
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="disabled">&laquo; Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Prev</a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="disabled">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &raquo;</a>
            @else
                <span class="disabled">Next &raquo;</span>
            @endif
        </nav>
    </div>
@endif
