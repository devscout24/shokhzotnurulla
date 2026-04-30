<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- SEO Meta -->
    @include('partials.seo')

    <!-- Base Assets -->
    @vite([
        'resources/sass/app.scss',
        'resources/css/app.css',
        'resources/js/app.js'
    ])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

    <!-- Panel specific Assets -->
    @stack('panel-assets')
    @stack('page-assets')
    <!-- Panel specific Style or Extra Styles -->
    @stack('panel-styles')
    @stack('page-styles')

    <!-- Tracking (Head Based) -->
    @include('partials.tracking-head')
</head>
<body>
    <!-- Tracking (Body Start) -->
    @include('partials.tracking-body-start')

    <!-- Main Content (Panel Wise) -->
    @yield('panel-content')

    <!-- Modals / Popups -->
    @stack('panel-modals')
    @stack('page-modals')

    <!-- CDN / inline / small custom JS -->
    @stack('pannel-scripts')
    @stack('page-scripts')

    <!-- Tracking (Body End) -->
    @include('partials.tracking-body-end')
</body>
</html>
