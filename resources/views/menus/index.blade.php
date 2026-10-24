@extends('layouts.app')

@section('eyebrow', auth()->user()->isSuperAdmin() ? ($context->name ?? 'Cartas') : 'Cartas')
@section('title', 'Menús')

@section('content')
    <div class="section-head">
        <p class="page-intro">Cada menú nace con un QR. Puedes cambiar los platos; el código se queda.</p>
        <a class="btn btn-primary" href="{{ \App\Support\Panel::menusCreate($context) }}">Nuevo menú</a>
    </div>

    <form class="toolbar" method="GET" action="{{ \App\Support\Panel::menusIndex($context) }}">
        <label class="search">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar menú">
        </label>
    </form>

    @if ($menus->isEmpty())
        <div class="empty">No hay menús todavía.</div>
    @else
        <div class="grid-cards">
            @foreach ($menus as $menu)
                <article class="card">
                    <div class="card-top">
                        <h3>{{ $menu->name }}</h3>
                        <span class="badge {{ $menu->is_published ? 'badge-ok' : 'badge-off' }}">{{ $menu->is_published ? 'Publicado' : 'Oculto' }}</span>
                    </div>
                    <p class="meta">{{ $menu->products_count }} productos</p>
                    <p class="mono">{{ $menu->qr_code }}</p>
                    <div class="row-actions">
                        <a class="btn btn-small btn-ghost" href="{{ \App\Support\Panel::menuShow($menu) }}">QR</a>
                        <a class="btn btn-small btn-ghost" href="{{ \App\Support\Panel::menusEdit($menu) }}">Editar</a>
                        <a class="btn btn-small btn-ghost" href="{{ $menu->publicUrl() }}" target="_blank" rel="noopener">Ver carta</a>
                    </div>
                </article>
            @endforeach
        </div>
        <x-pager :paginator="$menus" />
    @endif
@endsection
