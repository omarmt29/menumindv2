@props(['name' => 'dot'])
<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" {{ $attributes->merge(['class' => 'icon']) }}>
    @switch($name)
        @case('panel')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5"></rect>
            <rect x="13.5" y="3.5" width="7" height="4.5" rx="1.5"></rect>
            <rect x="13.5" y="11" width="7" height="9.5" rx="1.5"></rect>
            <rect x="3.5" y="13.5" width="7" height="7" rx="1.5"></rect>
            @break
        @case('store')
            <path d="M4 10.5 5.2 5.8A1.5 1.5 0 0 1 6.7 4.7h10.6a1.5 1.5 0 0 1 1.5 1.1L20 10.5"></path>
            <path d="M4 10.5h16v8.2a1.3 1.3 0 0 1-1.3 1.3H5.3A1.3 1.3 0 0 1 4 18.7z"></path>
            <path d="M9 20v-4.2h6V20"></path>
            @break
        @case('dish')
            <path d="M4 15.5h16"></path>
            <path d="M6.5 15.5a5.5 5.5 0 0 1 11 0"></path>
            <path d="M12 6.2v2.2"></path>
            @break
        @case('book')
            <path d="M5 5.5h6.2A2.3 2.3 0 0 1 13.5 7.8V19a2 2 0 0 0-2-1.6H5z"></path>
            <path d="M19 5.5h-6.2A2.3 2.3 0 0 0 10.5 7.8V19a2 2 0 0 1 2-1.6H19z"></path>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M12 3.8v2.1M12 18.1v2.1M3.8 12h2.1M18.1 12h2.1M6.1 6.1l1.5 1.5M16.4 16.4l1.5 1.5M17.9 6.1l-1.5 1.5M7.6 16.4l-1.5 1.5"></path>
            @break
        @case('logout')
            <path d="M9 7V5.8A1.8 1.8 0 0 1 10.8 4h6.4A1.8 1.8 0 0 1 19 5.8v12.4a1.8 1.8 0 0 1-1.8 1.8h-6.4A1.8 1.8 0 0 1 9 18.2V17"></path>
            <path d="M12 12H4.5M6.2 9.6 4 12l2.2 2.4"></path>
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14"></path>
            @break
        @case('search')
            <circle cx="11" cy="11" r="6"></circle>
            <path d="m16 16 4 4"></path>
            @break
        @case('qr')
            <path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2.2M18 14v2.2M14 18h6"></path>
            @break
        @case('external')
            <path d="M9 6H6.8A1.8 1.8 0 0 0 5 7.8v9.4A1.8 1.8 0 0 0 6.8 19h9.4a1.8 1.8 0 0 0 1.8-1.8V15"></path>
            <path d="M13 5h6v6M19 5l-8 8"></path>
            @break
        @case('copy')
            <rect x="8" y="8" width="11" height="11" rx="1.6"></rect>
            <path d="M6.5 16H5.8A1.8 1.8 0 0 1 4 14.2V5.8A1.8 1.8 0 0 1 5.8 4h8.4A1.8 1.8 0 0 1 16 5.8V6.5"></path>
            @break
        @default
            <circle cx="12" cy="12" r="1.2" fill="currentColor" stroke="none"></circle>
    @endswitch
</svg>
