@if ($paginator->hasPages())
    <nav>
        <ul style="display:flex;gap:8px;justify-content:center;list-style:none;margin:0;padding:0">
            @if ($paginator->onFirstPage())
                <li><span style="padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#d0d0d0">‹</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" style="display:block;padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#4a4a5a;text-decoration:none">‹</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span style="padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#8a8a9a">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><span style="display:block;padding:8px 14px;background:#4758D6;color:#fff;border-radius:8px;font-size:14px;font-weight:700">{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}" style="display:block;padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#4a4a5a;text-decoration:none">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" style="display:block;padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#4a4a5a;text-decoration:none">›</a></li>
            @else
                <li><span style="padding:8px 14px;border:1px solid #e2e4e9;border-radius:8px;font-size:14px;color:#d0d0d0">›</span></li>
            @endif
        </ul>
    </nav>
@endif
