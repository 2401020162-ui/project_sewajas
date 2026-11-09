@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Navigasi halaman">
        @if ($paginator->onFirstPage())
            <span class="page-link disabled" aria-disabled="true">&laquo; Sebelumnya</span>
        @else
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
        @endif

        <div class="page-numbers">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        @if ($paginator->hasMorePages())
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
        @else
            <span class="page-link disabled" aria-disabled="true">Berikutnya &raquo;</span>
        @endif
    </nav>
@endif
