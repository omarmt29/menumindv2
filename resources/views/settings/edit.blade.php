@extends('layouts.app')

@section('eyebrow', 'Cuenta')
@section('title', 'Configuración')

@section('content')
    <form class="form-card" method="POST" action="{{ route('settings.update') }}">
        @csrf
        @method('PUT')

        <h2 class="form-subtitle">Tu acceso</h2>
        <div class="form-grid">
            <label class="field">
                <span>Nombre</span>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </label>
            <label class="field">
                <span>Correo</span>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </label>
            <label class="field">
                <span>Nueva contraseña</span>
                <input type="password" name="password" autocomplete="new-password" placeholder="Dejar en blanco para no cambiarla">
            </label>
            <label class="field">
                <span>Confirmar contraseña</span>
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </label>
        </div>

        @if ($user->isAdmin() && $restaurant)
            <h2 class="form-subtitle">Restaurante</h2>
            <div class="form-grid">
                <label class="field">
                    <span>Nombre</span>
                    <input name="restaurant_name" value="{{ old('restaurant_name', $restaurant->name) }}" required>
                </label>
                <label class="field">
                    <span>Cocina</span>
                    <input name="cuisine" value="{{ old('cuisine', $restaurant->cuisine) }}">
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
                    <input type="email" name="contact_email" value="{{ old('contact_email', $restaurant->email) }}">
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
                    <span>Restaurante activo y visible en las cartas públicas</span>
                </label>
            </div>
        @endif

        <div class="form-actions">
            <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
    </form>
@endsection
