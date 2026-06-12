@if ($paginator->hasPages())
<nav class="pagination">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span><i data-feather="chevron-left"></i></span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"><i data-feather="chevron-left"></i></a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span>{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"><i data-feather="chevron-right"></i></a>
    @else
        <span><i data-feather="chevron-right"></i></span>
    @endif
</nav>
@endif
