@extends('layouts.app')

@section('eyebrow', auth()->user()->isSuperAdmin() ? ($context->name ?? 'Carta') : 'Carta')
@section('title', $product->exists ? 'Editar producto' : 'Nuevo producto')

@section('content')
    <form class="form-card" method="POST" action="{{ $product->exists ? \App\Support\Panel::productsUpdate($product) : \App\Support\Panel::productsStore($context) }}">
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <label class="field">
                <span>Nombre</span>
                <input name="name" value="{{ old('name', $product->name) }}" required>
            </label>
            <label class="field">
                <span>Categoría</span>
                <input name="category" list="product-categories" value="{{ old('category', $product->category) }}" required>
                <datalist id="product-categories">
                    @foreach (\App\Models\Product::CATEGORIES as $category)
                        <option value="{{ $category }}"></option>
                    @endforeach
                </datalist>
            </label>
            <label class="field field-span">
                <span>Detalle</span>
                <textarea name="detail" rows="4" placeholder="Qué lleva, cómo se sirve, una nota corta.">{{ old('detail', $product->detail) }}</textarea>
            </label>
            <label class="field">
                <span>Precio</span>
                <input name="price" type="number" min="0" step="0.01" value="{{ old('price', $product->price) }}" required inputmode="decimal">
            </label>
            <label class="check">
                <input type="hidden" name="is_available" value="0">
                <input type="checkbox" name="is_available" value="1" @checked((string) old('is_available', ($product->is_available ?? true) ? '1' : '0') === '1')>
                <span>Disponible en la carta</span>
            </label>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ $product->exists ? 'Guardar producto' : 'Crear producto' }}</button>
            <a class="btn btn-ghost" href="{{ \App\Support\Panel::productsIndex($context) }}">Cancelar</a>
        </div>
    </form>
@endsection
