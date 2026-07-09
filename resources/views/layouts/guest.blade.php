<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Vacaciones') - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}?v=5" rel="stylesheet">
    @stack('styles')
</head>
<body class="guest-body">
    <div class="institutional-bar"></div>
    <div class="guest-wrapper">
        <header class="guest-header text-center py-4">
            <div class="brand-badge mb-3">
                <i class="bi bi-building"></i> Sistema institucional
            </div>
            <h1 class="h3 mb-1 brand-title">
                <i class="bi bi-calendar2-week me-2"></i>{{ config('app.name') }}
            </h1>
            <p class="text-muted mb-0">Registro de vacaciones del personal</p>
        </header>

        <main class="container pb-5">
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
