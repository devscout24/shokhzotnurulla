<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __(config('app.name')))</title>

    <!-- Styles & Scripts -->
    @vite([
        'resources/sass/app.scss',
        'resources/css/app.css',
        'resources/css/auth/app.css',
        'resources/js/app.js',
        'resources/js/auth/app.js'
    ])
</head>
<body>
    <div class="admin-wrapper">
        <!-- Main Content -->
        @yield('content')
    </div>

    <!-- Toastr Notifications / Alerts -->
    @include('partials.toastr-alerts')
</body>
</html>
