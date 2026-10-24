@extends('layouts.app')

@section('eyebrow', auth()->user()->isSuperAdmin() ? 'Operación' : 'Tu casa')
@section('title', 'Panel')

@section('content')
    <p class="page-intro">
        @if (auth()->user()->isSuperAdmin())
            Elige un restaurante para administrar sus productos y menús.
        @elseif (auth()->user()->restaurant)
            {{ auth()->user()->restaurant->cuisine ?: 'Tu restaurante' }} · {{ auth()->user()->restaurant->city }}
        @else
            Este acceso todavía no tiene un restaurante asignado.
        @endif
    </p>

    <section class="stats" aria-label="Resumen">
        @foreach ($stats as $stat)
            <article class="stat">
                <span class="stat-value">{{ $stat['value'] }}</span>
                <span class="stat-label">{{ $stat['label'] }}</span>
            </article>
        @endforeach
    </section>

    @if (auth()->user()->isSuperAdmin())
        <section class="block">
            <div class="section-head">
                <h2>Restaurantes</h2>
                <a class="btn btn-primary btn-small" href="{{ route('restaurants.create') }}">Nuevo cliente</a>
            </div>
            @if ($restaurants->isEmpty())
                <div class="empty">Todavía no hay restaurantes.</div>
            @else
                <div class="grid-cards">
                    @foreach ($restaurants as $restaurant)
                        <a class="card card-link" href="{{ route('restaurants.show', $restaurant) }}">
                            <div class="card-top">
                                <h3>{{ $restaurant->name }}</h3>
                                <span class="badge {{ $restaurant->is_active ? 'badge-ok' : 'badge-off' }}">{{ $restaurant->is_active ? 'Activo' : 'Pausado' }}</span>
                            </div>
                            <p class="meta">{{ $restaurant->cuisine ?: 'Sin cocina' }} · {{ $restaurant->city ?: 'Sin ciudad' }}</p>
                            <p class="meta">{{ $restaurant->products_count }} productos · {{ $restaurant->menus_count }} menús</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @unless (auth()->user()->isSuperAdmin())
    <section class="block">
        <div class="section-head">
            <h2>Tus menús</h2>
            <a class="btn btn-ghost btn-small" href="{{ route('menus.create') }}">Nuevo menú</a>
        </div>
        @if ($menus->isEmpty())
            <div class="empty">Cuando crees un menú, su QR queda fijo para siempre.</div>
        @else
            <div class="grid-cards">
                @foreach ($menus as $menu)
                    <a class="card card-link" href="{{ route('menus.show', $menu) }}">
                        <div class="card-top">
                            <h3>{{ $menu->name }}</h3>
                            <span class="badge {{ $menu->is_published ? 'badge-ok' : 'badge-off' }}">{{ $menu->is_published ? 'Publicado' : 'Oculto' }}</span>
                        </div>
                        <p class="meta">{{ $menu->products_count }} productos · QR {{ $menu->qr_code }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
    @endunless
@endsection
