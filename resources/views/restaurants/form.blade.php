@extends('layouts.app')

@section('eyebrow', 'Clientes')
@section('title', $restaurant->exists ? 'Editar restaurante' : 'Nuevo restaurante')

@section('content')
    <form class="form-card" method="POST" action="{{ $restaurant->exists ? route('restaurants.update', $restaurant) : route('restaurants.store') }}">
        @csrf
        @if ($restaurant->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <label class="field">
                <span>Nombre</span>
                <input name="name" value="{{ old('name', $restaurant->name) }}" required class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
            </label>
            <label class="field">
                <span>Cocina</span>
                <input name="cuisine" value="{{ old('cuisine', $restaurant->cuisine) }}" placeholder="Criolla, mariscos, café">
            </label>
            <label class="field field-span">
                <span>Descripción</span>
                <textarea name="description" rows="3">{{ old('description', $restaurant->description) }}</textarea>
            </label>
            <label class="field">
                <span>Teléfono</span>
                <input name="phone" value="{{ old('phone', $restaurant->phone) }}">
            </label>
            <label class="field">
                <span>Correo de contacto</span>
                <input type="email" name="email" value="{{ old('email', $restaurant->email) }}">
            </label>
            <label class="field">
                <span>Dirección</span>
                <input name="address" value="{{ old('address', $restaurant->address) }}">
            </label>
            <label class="field">
                <span>Ciudad</span>
                <input name="city" value="{{ old('city', $restaurant->city) }}">
            </label>
            <label class="check field-span">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked((string) old('is_active', $restaurant->is_active ? '1' : '0') === '1')>
                <span>Restaurante activo. Si lo pausas, sus cartas públicas dejan de mostrarse.</span>
            </label>
        </div>

        <h2 class="form-subtitle">Acceso del restaurante</h2>
        <p class="hint">Con este correo entra a su panel. No es el mismo que el correo público de contacto.</p>
        <div class="form-grid">
            <label class="field">
                <span>Nombre</span>
                <input name="admin_name" value="{{ old('admin_name', $admin->name) }}" required>
            </label>
            <label class="field">
                <span>Correo de acceso</span>
                <input type="email" name="admin_email" value="{{ old('admin_email', $admin->email) }}" required>
            </label>
            <label class="field field-span">
                <span>Contraseña</span>
                <input type="text" name="admin_password" autocomplete="new-password" {{ $restaurant->exists && $admin->exists ? '' : 'required' }} placeholder="{{ $restaurant->exists ? 'Dejar en blanco para no cambiarla' : 'Mínimo 8 caracteres' }}">
            </label>
        </div>

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">{{ $restaurant->exists ? 'Guardar cambios' : 'Crear restaurante' }}</button>
            <a class="btn btn-ghost" href="{{ $restaurant->exists ? route('restaurants.show', $restaurant) : route('restaurants.index') }}">Cancelar</a>
        </div>
    </form>

    @if ($restaurant->exists)
        <form class="danger-zone" method="POST" action="{{ route('restaurants.destroy', $restaurant) }}" data-confirm="¿Eliminar este restaurante, sus productos, menús y el acceso?">
            @csrf
            @method('DELETE')
            <div>
                <strong>Eliminar cliente</strong>
                <p>Se borran productos, menús y el usuario de acceso. Los QR dejan de abrir.</p>
            </div>
            <button class="btn btn-danger" type="submit">Eliminar</button>
        </form>
    @endif
@endsection
