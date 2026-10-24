@extends('layouts.app')

@section('eyebrow', 'Clientes')
@section('title', 'Restaurantes')

@section('content')
    <div class="section-head">
        <p class="page-intro">Cada cliente es un restaurante con su propio acceso al panel.</p>
        <a class="btn btn-primary" href="{{ route('restaurants.create') }}">Nuevo restaurante</a>
    </div>

    <form class="toolbar" method="GET" action="{{ route('restaurants.index') }}">
        <label class="search">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por nombre, ciudad o cocina">
        </label>
    </form>

    @if ($restaurants->isEmpty())
        <div class="empty">No hay restaurantes con ese criterio.</div>
    @else
        <div class="grid-cards">
            @foreach ($restaurants as $restaurant)
                <article class="card">
                    <div class="card-top">
                        <h3>{{ $restaurant->name }}</h3>
                        <span class="badge {{ $restaurant->is_active ? 'badge-ok' : 'badge-off' }}">{{ $restaurant->is_active ? 'Activo' : 'Pausado' }}</span>
                    </div>
                    <p class="meta">{{ $restaurant->cuisine ?: 'Sin cocina' }} · {{ $restaurant->city ?: 'Sin ciudad' }}</p>
                    <p class="meta">{{ $restaurant->products_count }} productos · {{ $restaurant->menus_count }} menús</p>
                    <div class="row-actions">
                        <a class="btn btn-small btn-ghost" href="{{ route('restaurants.show', $restaurant) }}">Abrir</a>
                        <a class="btn btn-small btn-ghost" href="{{ route('restaurants.edit', $restaurant) }}">Editar</a>
                    </div>
                </article>
            @endforeach
        </div>
        <x-pager :paginator="$restaurants" />
    @endif
@endsection
