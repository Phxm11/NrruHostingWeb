@if ($paginator->hasPages())
    <div class="site-pagination">
        <div class="site-pagination__info">
            แสดง <strong>{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}</strong>
            จากทั้งหมด <strong>{{ $paginator->total() }}</strong> รายการ
        </div>

        <nav class="site-pagination__nav" role="navigation" aria-label="Pagination Navigation">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="site-pagination__btn is-disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 18l-6-6 6-6"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="site-pagination__btn" rel="prev" aria-label="ก่อนหน้า">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 18l-6-6 6-6"/></svg>
                </a>
            @endif

            {{-- Page numbers --}}
            <div class="site-pagination__pages">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="site-pagination__dots">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="site-pagination__page is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="site-pagination__page">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="site-pagination__btn" rel="next" aria-label="ถัดไป">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            @else
                <span class="site-pagination__btn is-disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M9 18l6-6-6-6"/></svg>
                </span>
            @endif
        </nav>
    </div>

    @once
        <style>
            .site-pagination {
                display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
                gap: 14px; padding-top: 18px; margin-top: 6px; border-top: 1px solid var(--line, #e8e4d5);
            }
            .site-pagination__info {
                font-size: 13px; color: var(--ink-soft, #5c6659);
            }
            .site-pagination__info strong { color: var(--ink, #15231a); font-weight: 600; }
            .site-pagination__nav {
                display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
            }
            .site-pagination__btn {
                display: inline-flex; align-items: center; justify-content: center;
                width: 34px; height: 34px; border-radius: 9px;
                border: 1px solid var(--line, #e8e4d5); background: #fff; color: var(--ink, #15231a);
                text-decoration: none; cursor: pointer; transition: background .15s ease, border-color .15s ease, color .15s ease, transform .1s ease;
                flex-shrink: 0;
            }
            .site-pagination__btn:hover { background: var(--moss-light, #e8f0dc); border-color: var(--moss, #6c9752); color: var(--forest, #1a3323); transform: translateY(-1px); }
            .site-pagination__btn.is-disabled { opacity: .35; cursor: not-allowed; pointer-events: none; }
            .site-pagination__pages { display: flex; align-items: center; gap: 3px; margin: 0 2px; }
            .site-pagination__page {
                display: inline-flex; align-items: center; justify-content: center;
                min-width: 34px; height: 34px; padding: 0 4px; border-radius: 9px;
                font-size: 13.5px; font-weight: 600; color: var(--ink-soft, #5c6659);
                text-decoration: none; transition: background .15s ease, color .15s ease;
            }
            .site-pagination__page:hover { background: var(--moss-light, #e8f0dc); color: var(--forest, #1a3323); }
            .site-pagination__page.is-current {
                background: linear-gradient(135deg, var(--forest, #1a3323), var(--forest-2, #244430));
                color: #fff;
            }
            .site-pagination__dots {
                display: inline-flex; align-items: center; justify-content: center;
                min-width: 26px; height: 34px; color: #b5b5ab; font-size: 13px;
            }

            @media (max-width: 560px) {
                .site-pagination { justify-content: center; text-align: center; }
                .site-pagination__info { width: 100%; text-align: center; order: 2; }
                .site-pagination__nav { order: 1; width: 100%; justify-content: center; }
            }
        </style>
    @endonce
@endif
