@if ($paginator->hasPages())
    <div class="sama-pag-wrap">
        <div class="sama-pag-info">
            Affichage de {{ $paginator->firstItem() }} à {{ $paginator->lastItem() }}
            sur {{ $paginator->total() }} résultat(s)
        </div>

        <nav class="sama-pag" role="navigation" aria-label="Pagination">
            {{-- Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="sama-pag-btn is-disabled" aria-disabled="true">&lsaquo; Précédent</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="sama-pag-btn" rel="prev">&lsaquo; Précédent</a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="sama-pag-dots" aria-hidden="true">{{ $element }}</span>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="sama-pag-num is-active" aria-current="page">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="sama-pag-num">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Suivant --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="sama-pag-btn" rel="next">Suivant &rsaquo;</a>
            @else
                <span class="sama-pag-btn is-disabled" aria-disabled="true">Suivant &rsaquo;</span>
            @endif
        </nav>
    </div>

    <style>
        .sama-pag-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px 16px;
            margin-top: 4px;
        }
        .sama-pag-info {
            font-size: 12px;
            color: var(--text-muted, #8aaa95);
            font-family: 'DM Sans', 'IBM Plex Sans', sans-serif;
        }
        .sama-pag {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }
        .sama-pag-btn,
        .sama-pag-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 12px;
            border: 1px solid var(--border, #d0ddd4);
            border-radius: 8px;
            background: var(--surface, #fff);
            color: var(--text, #1a2d22);
            font-family: 'DM Sans', 'IBM Plex Sans', sans-serif;
            font-size: 12.5px;
            font-weight: 600;
            text-decoration: none;
            line-height: 1;
            transition: background .15s ease, border-color .15s ease, color .15s ease;
        }
        .sama-pag-num { padding: 0; }
        .sama-pag-btn:hover,
        .sama-pag-num:hover {
            border-color: var(--primary, #267a47);
            color: var(--primary, #267a47);
            background: var(--primary-lt, #d8eee2);
        }
        .sama-pag-num.is-active {
            background: var(--primary, #267a47);
            border-color: var(--primary, #267a47);
            color: #fff;
            cursor: default;
        }
        .sama-pag-btn.is-disabled {
            color: var(--text-muted, #8aaa95);
            background: var(--surface2, #eef3f0);
            border-color: var(--border, #d0ddd4);
            cursor: not-allowed;
            opacity: .65;
        }
        .sama-pag-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 26px;
            height: 34px;
            color: var(--text-muted, #8aaa95);
            font-size: 13px;
            font-weight: 700;
        }
        @media (max-width: 560px) {
            .sama-pag-wrap { justify-content: center; }
            .sama-pag-info { width: 100%; text-align: center; }
            .sama-pag-btn { padding: 0 9px; }
        }
    </style>
@endif
