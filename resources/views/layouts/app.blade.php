<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel') · MenuMind</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,520;9..144,620&family=Outfit:wght@340;420;520;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menumind.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}">
</head>
<body class="app">
    @php($user = auth()->user())
    @php($contextRestaurant = request()->route('restaurant'))
    <div class="backdrop" data-sidebar-close></div>
    <div class="shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-copy">
                    <strong>MenuMind</strong>
                    <small>Carta digital</small>
                </span>
            </a>

            <nav class="nav" aria-label="Principal">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}">
                    <x-icon name="panel" /> <span>Panel</span>
                </a>
                @if ($user->isSuperAdmin())
                    <a class="nav-link {{ request()->routeIs('restaurants.*') ? 'is-active' : '' }}" href="{{ route('restaurants.index') }}">
                        <x-icon name="store" /> <span>Restaurantes</span>
                    </a>
                @else
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}">
                        <x-icon name="dish" /> <span>Productos</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('menus.*') ? 'is-active' : '' }}" href="{{ route('menus.index') }}">
                        <x-icon name="book" /> <span>Menús</span>
                    </a>
                @endif
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'is-active' : '' }}" href="{{ route('settings.edit') }}">
                    <x-icon name="settings" /> <span>Configuración</span>
                </a>
            </nav>

            <div class="sidebar-foot">
                <div class="who">
                    <span class="avatar">{{ $user->initials() }}</span>
                    <span>
                        <strong>{{ $user->name }}</strong>
                        <small>{{ $user->role->label() }}</small>
                    </span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-ghost logout" type="submit">
                        <x-icon name="logout" /> Salir
                    </button>
                </form>
            </div>
        </aside>

        <div class="main">
            <header class="topbar">
                <button class="icon-btn" type="button" data-sidebar-toggle aria-label="Abrir menú">
                    <span></span><span></span><span></span>
                </button>
                <div class="topbar-title">
                    <p class="eyebrow">@yield('eyebrow', 'MenuMind')</p>
                    <h1>@yield('title', 'Panel')</h1>
                </div>
                <div class="topbar-place">
                    @if ($user->isSuperAdmin() && $contextRestaurant instanceof \App\Models\Restaurant)
                        <span>{{ $contextRestaurant->name }}</span>
                        <small>{{ $contextRestaurant->city ?: 'Restaurante' }}</small>
                    @elseif ($user->restaurant)
                        <span>{{ $user->restaurant->name }}</span>
                        <small>{{ $user->restaurant->city ?: 'Tu restaurante' }}</small>
                    @else
                        <span>{{ now()->translatedFormat('d M') }}</span>
                        <small>{{ now()->translatedFormat('l') }}</small>
                    @endif
                </div>
            </header>
            <div class="content">
                @if ($user->isSuperAdmin() && $contextRestaurant instanceof \App\Models\Restaurant)
                    @include('restaurants.subnav', ['restaurant' => $contextRestaurant])
                @endif
                @include('partials.alerts')
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('js/menumind.js') }}"></script>
    @stack('scripts')
</body>
</html>
