@extends('layouts.public')

@section('title', $restaurant->name.' · '.$menu->name)

@section('content')
    <header class="menu-hero">
        <p class="kicker">{{ $restaurant->cuisine ?: 'Carta' }}@if ($restaurant->city) · {{ $restaurant->city }}@endif</p>
        <h1>{{ $restaurant->name }}</h1>
        <h2>{{ $menu->name }}</h2>
        @if ($menu->description && $open)
            <p>{{ $menu->description }}</p>
        @endif
    </header>

    @if (! $open)
        <main class="closed">
            <p class="kicker">QR permanente</p>
            <h2>Esta carta está en pausa.</h2>
            <p>Cuando el restaurante la vuelva a publicar, este mismo código la abre.</p>
        </main>
    @else
        <main class="menu-body">
            @forelse ($sections as $name => $items)
                <section class="menu-section">
                    <h3>{{ $name }}</h3>
                    @foreach ($items as $product)
                        <article class="dish {{ $product->is_available ? '' : 'is-soldout' }}">
                            <div>
                                <h4>{{ $product->name }}</h4>
                                @if ($product->detail)
                                    <p>{{ $product->detail }}</p>
                                @endif
                                @unless ($product->is_available)
                                    <span class="soldout">Agotado</span>
                                @endunless
                            </div>
                            <span class="price">{{ $product->priceLabel() }}</span>
                        </article>
                    @endforeach
                </section>
            @empty
                <div class="closed">
                    <h2>La carta está por armarse.</h2>
                    <p>Vuelve en un momento.</p>
                </div>
            @endforelse
        </main>
    @endif

    <footer class="menu-foot">
        <span>{{ $restaurant->address }}</span>
        <span>MenuMind</span>
    </footer>
@endsection
