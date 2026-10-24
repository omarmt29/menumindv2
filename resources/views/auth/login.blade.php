@extends('layouts.guest')

@section('title', 'Entrar')

@section('content')
    <div class="login-grid">
        <section class="login-hero">
            <a class="brand brand-light" href="{{ route('login') }}">
                <span class="brand-mark" aria-hidden="true"></span>
                <span class="brand-copy">
                    <strong>MenuMind</strong>
                    <small>Carta digital</small>
                </span>
            </a>
            <div class="login-copy">
                <p class="eyebrow">Para restaurantes</p>
                <h1>La carta, bajo control.</h1>
                <p>Productos, menús y un QR permanente para cada cliente. Lo que cambia es el plato, no el código.</p>
            </div>
        </section>

        <section class="login-panel">
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf
                <h2>Entrar</h2>
                <p class="hint">Super admin o el acceso de tu restaurante.</p>
                @include('partials.alerts')

                <label class="field">
                    <span>Correo</span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="tu@restaurante.com">
                </label>
                <label class="field">
                    <span>Contraseña</span>
                    <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                </label>
                <label class="check">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span>Recordarme en este equipo</span>
                </label>
                <button class="btn btn-primary" type="submit">Continuar</button>
            </form>
        </section>
    </div>
@endsection
