@extends('layouts.app')

@section('eyebrow', auth()->user()->isSuperAdmin() ? ($context->name ?? 'Cartas') : 'Cartas')
@section('title', $menu->exists ? 'Editar menú' : 'Nuevo menú')

@php
    $catalog = $products->map(fn ($product) => [
        'id' => $product->id,
        'restaurant_id' => $product->restaurant_id,
        'name' => $product->name,
        'price_label' => $product->priceLabel(),
        'category' => $product->category,
    ])->values();
    $byId = $products->keyBy('id');
    $attached = old('items', $menu->exists ? $menu->products->map(fn ($product) => [
        'product_id' => $product->id,
        'section' => $product->pivot->section,
    ])->all() : []);
    $fixedRestaurant = $menu->restaurant_id ?: ($context->id ?? null);
@endphp

@section('content')
    <form class="form-card" method="POST" action="{{ $menu->exists ? \App\Support\Panel::menusUpdate($menu) : \App\Support\Panel::menusStore($context) }}">
        @csrf
        @if ($menu->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <label class="field">
                <span>Nombre del menú</span>
                <input name="name" value="{{ old('name', $menu->name) }}" required placeholder="Almuerzo, cena, barra">
            </label>
            <label class="check">
                <input type="hidden" name="is_published" value="0">
                <input type="checkbox" name="is_published" value="1" @checked((string) old('is_published', ($menu->is_published ?? true) ? '1' : '0') === '1')>
                <span>Publicado. El QR sigue existiendo aunque lo ocultes.</span>
            </label>
            <label class="field field-span">
                <span>Descripción</span>
                <textarea name="description" rows="3">{{ old('description', $menu->description) }}</textarea>
            </label>
        </div>

        <div class="builder" data-menu-builder data-restaurant="{{ $fixedRestaurant }}">
            <div class="section-head">
                <h2>Productos de la carta</h2>
            </div>
            <div class="builder-add">
                <select data-picker>
                    <option value="">Agregar un producto</option>
                </select>
                <button class="btn btn-ghost" type="button" data-add>Agregar</button>
            </div>
            <p class="builder-empty" data-empty @if (count($attached)) hidden @endif>Este menú todavía no tiene platos.</p>
            <ul class="builder-list" data-items>
                @foreach ($attached as $index => $item)
                    @php $product = $byId->get((int) ($item['product_id'] ?? 0)); @endphp
                    @if ($product)
                        <li class="builder-item">
                            <div class="builder-main">
                                <strong>{{ $product->name }}</strong>
                                <span>{{ $product->priceLabel() }}</span>
                            </div>
                            <label class="field field-compact">
                                <span>Sección</span>
                                <input data-section name="items[{{ $index }}][section]" value="{{ $item['section'] ?? $product->category }}" list="menu-sections">
                            </label>
                            <div class="builder-actions">
                                <button type="button" data-up aria-label="Subir">↑</button>
                                <button type="button" data-down aria-label="Bajar">↓</button>
                                <button type="button" data-remove>Quitar</button>
                            </div>
                            <input type="hidden" data-id name="items[{{ $index }}][product_id]" value="{{ $product->id }}">
                        </li>
                    @endif
                @endforeach
            </ul>
            <datalist id="menu-sections">
                @foreach (\App\Models\Product::CATEGORIES as $category)
                    <option value="{{ $category }}"></option>
                @endforeach
            </datalist>
            <template id="builder-template">
                <li class="builder-item">
                    <div class="builder-main">
                        <strong data-label></strong>
                        <span data-price></span>
                    </div>
                    <label class="field field-compact">
                        <span>Sección</span>
                        <input data-section list="menu-sections">
                    </label>
                    <div class="builder-actions">
                        <button type="button" data-up aria-label="Subir">↑</button>
                        <button type="button" data-down aria-label="Bajar">↓</button>
                        <button type="button" data-remove>Quitar</button>
                    </div>
                    <input type="hidden" data-id>
                </li>
            </template>
        </div>

        <script type="application/json" id="product-catalog">@json($catalog)</script>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ $menu->exists ? 'Guardar menú' : 'Crear menú' }}</button>
            <a class="btn btn-ghost" href="{{ \App\Support\Panel::menusIndex($context) }}">Cancelar</a>
        </div>
    </form>

    @if ($menu->exists)
        <form class="danger-zone" method="POST" action="{{ \App\Support\Panel::menusDestroy($menu) }}" data-confirm="¿Eliminar este menú? El QR dejará de abrir.">
            @csrf
            @method('DELETE')
            <div>
                <strong>Eliminar menú</strong>
                <p>Los productos se quedan. El código QR de esta carta deja de funcionar.</p>
            </div>
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    @endif
@endsection
