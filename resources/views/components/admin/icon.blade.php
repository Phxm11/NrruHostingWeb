@props(['name', 'size' => 20])
<svg {{ $attributes->class(['admin-icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('requests')<path d="M9 3H5v18h14V3h-4M9 2h6v4H9zM8 11h8M8 15h5"/>@break
        @case('accounts')<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="9" cy="10" r="2"/><path d="M6 16c0-3 6-3 6 0M15 9h3M15 13h3"/>@break
        @case('users')<circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M17 15a4 4 0 0 1 4 4v2"/>@break
        @case('domains')<circle cx="12" cy="12" r="9"/><ellipse cx="12" cy="12" rx="4" ry="9"/><path d="M3 12h18"/>@break
        @case('reports')<path d="M14 2H4v20h16V8zM14 2v6h6M8 17v-3M12 17v-6M16 17v-4"/>@break
        @case('calendar')<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 11h18M8 15h2M14 15h2"/>@break
        @case('filter')<path d="M4 7h16M7 17h10"/><circle cx="9" cy="7" r="2" fill="currentColor"/><circle cx="15" cy="17" r="2" fill="currentColor"/>@break
        @case('print')<path d="M6 9V3h12v6M6 17H3V9h18v8h-3M6 14h12v7H6zM17 11h1"/>@break
        @case('check')<circle cx="12" cy="12" r="9"/><path d="m8 12 3 3 5-6"/>@break
        @case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
        @case('building')<path d="M4 21V5l10-3v19M14 9h6v12M2 21h20M8 7h2M8 11h2M8 15h2M8 19h2M17 13h1M17 17h1"/>@break
        @case('chart')<path d="M3 3v18h18M7 16l4-5 4 2 6-8M17 5h4v4"/>@break
        @case('renew')<path d="M3 10a9 9 0 0 1 15-5l3 3M21 3v5h-5M21 14a9 9 0 0 1-15 5l-3-3M3 21v-5h5"/>@break
        @case('server')<rect x="3" y="3" width="18" height="7" rx="2"/><rect x="3" y="14" width="18" height="7" rx="2"/><path d="M7 6.5h.01M7 17.5h.01M12 6.5h5M12 17.5h5"/>@break
        @case('search')<circle cx="10.5" cy="10.5" r="7"/><path d="m16 16 5 5"/>@break
        @case('plus')<path d="M12 5v14M5 12h14"/>@break
        @case('back')<path d="M20 12H4m7-7-7 7 7 7"/>@break
        @case('arrow')<path d="M4 12h16m-7-7 7 7-7 7"/>@break
        @case('chevron')<path d="m9 5 7 7-7 7"/>@break
        @case('external')<path d="M14 3h7v7M21 3 10 14M10 3H3v18h18v-7"/>@break
        @case('menu')<path d="M4 6h16M4 12h16M4 18h16"/>@break
        @case('close')<path d="m6 6 12 12M6 18 18 6"/>@break
        @case('logout')<path d="M9 3H4v18h5M10 12h11m-5-5 5 5-5 5"/>@break
        @case('save')<path d="M19 21H5a2 2 0 0 1-2-2V3h14l4 4v12a2 2 0 0 1-2 2ZM7 3v6h9V3M7 21v-7h10v7"/>@break
        @case('info')<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/>@break
        @case('list')<path d="M9 6h12M9 12h12M9 18h12M3 6h.01M3 12h.01M3 18h.01"/>@break
    @endswitch
</svg>
