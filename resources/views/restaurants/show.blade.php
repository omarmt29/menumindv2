@extends('layouts.app')

@section('eyebrow', 'Cliente')
@section('title', $restaurant->name)

@section('content')
    <div class="section-head">
        <p class="page-intro">{{ $restaurant->cuisine ?: 'Restaurante' }} · {{ $restaurant->city ?: 'Sin ciudad' }}</p>
        <div class="row-actions">
            <a class="btn btn-ghost" href="{{ route('restaurants.edit', $restaurant) }}">Editar</a>
            <a class="btn btn-primary" href="{{ \App\Support\Panel::menusCreate($restaurant) }}">Nuevo menú</a>
        </div>
    </div>

    <section class="stats">
        <article class="stat">
            <span class="stat-value">{{ $restaurant->products_count }}</span>
            <span class="stat-label">Productos</span>
        </article>
        <article class="stat">
            <span class="stat-value">{{ $restaurant->menus_count }}</span>
            <span class="stat-label">Menús</span>
        </article>
        <article class="stat">
            <span class="stat-value">{{ $restaurant->is_active ? 'Sí' : 'No' }}</span>
            <span class="stat-label">Activo</span>
        </article>
    </section>

    <div class="split">
        <section class="card">
            <h2>Ficha</h2>
            <dl class="facts">
                <div><dt>Contacto</dt><dd>{{ $restaurant->email ?: '—' }}</dd></div>
                <div><dt>Teléfono</dt><dd>{{ $restaurant->phone ?: '—' }}</dd></div>
                <div><dt>Dirección</dt><dd>{{ $restaurant->address ?: '—' }}</dd></div>
                <div><dt>Acceso</dt><dd>{{ $restaurant->admin?->email ?: 'Sin usuario' }}</dd></div>
            </dl>
            @if ($restaurant->description)
                <p class="prose">{{ $restaurant->description }}</p>
            @endif
        </section>

        <section class="card">
            <div class="section-head">
                <h2>Menús</h2>
                <a class="btn btn-small btn-ghost" href="{{ \App\Support\Panel::menusIndex($restaurant) }}">Ver todos</a>
            </div>
            @forelse ($restaurant->menus as $menu)
                <a class="list-row" href="{{ \App\Support\Panel::menuShow($menu) }}">
                    <span>
                        <strong>{{ $menu->name }}</strong>
                        <small>{{ $menu->products_count }} productos · {{ $menu->qr_code }}</small>
                    </span>
                    <span class="badge {{ $menu->is_published ? 'badge-ok' : 'badge-off' }}">{{ $menu->is_published ? 'Publicado' : 'Oculto' }}</span>
                </a>
            @empty
                <div class="empty">Este cliente todavía no tiene menús.</div>
            @endforelse
        </section>
    </div>

    <section class="card block">
        <div class="section-head">
            <h2>Productos</h2>
            <a class="btn btn-small btn-ghost" href="{{ \App\Support\Panel::productsIndex($restaurant) }}">Ver todos</a>
        </div>
        @forelse ($restaurant->products as $product)
            <a class="list-row" href="{{ \App\Support\Panel::productsEdit($product) }}">
                <span>
                    <strong>{{ $product->name }}</strong>
                    <small>{{ $product->category }}</small>
                </span>
                <span class="num">{{ $product->priceLabel() }}</span>
            </a>
        @empty
            <div class="empty">Este cliente todavía no tiene productos.</div>
        @endforelse
    </section>
@endsection
