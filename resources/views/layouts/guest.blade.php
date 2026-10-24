<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Entrar') · MenuMind</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,520;9..144,620&family=Outfit:wght@340;420;520;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/menumind.css') }}">
    <link rel="icon" href="{{ asset('favicon.svg') }}">
</head>
<body class="guest">
    @yield('content')
    <script src="{{ asset('js/menumind.js') }}"></script>
</body>
</html>
