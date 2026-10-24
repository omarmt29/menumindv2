@extends('layouts.app')

@section('eyebrow', auth()->user()->isSuperAdmin() ? ($context->name ?? 'Carta') : 'Carta')
@section('title', 'Productos')

@section('content')
    <div class="section-head">
        <p class="page-intro">El detalle que ve el comensal: nombre, descripción y precio.</p>
        <a class="btn btn-primary" href="{{ \App\Support\Panel::productsCreate($context) }}">Nuevo producto</a>
    </div>

    <form class="toolbar" method="GET" action="{{ \App\Support\Panel::productsIndex($context) }}">
        <label class="search">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar producto">
        </label>
    </form>

    @if ($products->isEmpty())
        <div class="empty">No hay productos todavía.</div>
    @else
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if ($product->detail)
                                    <small>{{ \Illuminate\Support\Str::limit($product->detail, 80) }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category }}</td>
                            <td class="num">{{ $product->priceLabel() }}</td>
                            <td><span class="badge {{ $product->is_available ? 'badge-ok' : 'badge-off' }}">{{ $product->is_available ? 'Disponible' : 'Agotado' }}</span></td>
                            <td class="row-actions">
                                <a href="{{ \App\Support\Panel::productsEdit($product) }}">Editar</a>
                                <form method="POST" action="{{ \App\Support\Panel::productsDestroy($product) }}" data-confirm="¿Eliminar {{ $product->name }}? Se quita de los menús.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <x-pager :paginator="$products" />
    @endif
@endsection
