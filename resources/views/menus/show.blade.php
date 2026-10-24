@extends('layouts.app')

@section('eyebrow', $menu->restaurant->name)
@section('title', $menu->name)

@section('content')
    <div class="qr-layout">
        <section class="qr-card">
            <p class="eyebrow">QR permanente</p>
            <div class="qr-frame">{!! $qr !!}</div>
            <p class="mono">{{ $menu->qr_code }}</p>
            <p class="hint">Este código no cambia si editas el nombre, los platos o el precio.</p>
            <div class="row-actions">
                <button class="btn btn-ghost" type="button" data-copy="{{ $menu->publicUrl() }}">
                    <x-icon name="copy" /> <span data-copy-label>Copiar enlace</span>
                </button>
                <a class="btn btn-ghost" href="{{ \App\Support\Panel::menusQr($menu) }}">Descargar SVG</a>
            </div>
        </section>

        <section class="card">
            <div class="card-top">
                <h2>Carta</h2>
                <span class="badge {{ $menu->is_published ? 'badge-ok' : 'badge-off' }}">{{ $menu->is_published ? 'Publicada' : 'Oculta' }}</span>
            </div>
            @if ($menu->description)
                <p class="prose">{{ $menu->description }}</p>
            @endif
            <p class="link-line"><a href="{{ $menu->publicUrl() }}" target="_blank" rel="noopener">{{ $menu->publicUrl() }}</a></p>

            <ul class="plain-list">
                @forelse ($menu->products as $product)
                    <li>
                        <span>
                            <strong>{{ $product->name }}</strong>
                            <small>{{ $product->pivot->section }}</small>
                        </span>
                        <span>{{ $product->priceLabel() }}</span>
                    </li>
                @empty
                    <li class="empty">Sin productos todavía.</li>
                @endforelse
            </ul>

            <div class="form-actions">
                <a class="btn btn-primary" href="{{ \App\Support\Panel::menusEdit($menu) }}">Editar menú</a>
                <a class="btn btn-ghost" href="{{ $menu->publicUrl() }}" target="_blank" rel="noopener">
                    <x-icon name="external" /> Ver como comensal
                </a>
            </div>
        </section>
    </div>
@endsection
