<svg width="{{ $size ?? 16 }}" height="{{ $size ?? 16 }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('renew')
            <path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 4v5h-5"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
            @break
        @case('calendar')
            <rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 3v3M16 3v3m-8 9 2 2 4-4"/>
            @break
        @case('note')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>
            @break
        @case('history')
            <path d="M3 11a9 9 0 1 1 2.7 7M3 4v7h7M12 7v5l3 2"/>
            @break
        @case('back')
            <path d="M19 12H5m7 7-7-7 7-7"/>
            @break
        @case('info')
            <circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/>
            @break
        @default
            <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
    @endswitch
</svg>
