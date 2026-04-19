@extends('layouts.base')

@push('panel-assets')
    @vite([
        'resources/css/dealer/app.css',
        'resources/js/dealer/app.js'
    ])
@endpush

@section('panel-content')
    <!-- Main Topbar -->
    @include('dealer.partials.main-topbar')

    <div class="layout">
        <!-- Main Sidebar -->
        @if (request()->routeIs('dealer.website.*'))
            @include('dealer.partials.main-sidebar')
        @endif

        <!-- Main Content -->
        @yield('page-content')
    </div>

    <!-- Toastr Notifications / Alerts -->
    @include('partials.toastr-alerts')
@endsection