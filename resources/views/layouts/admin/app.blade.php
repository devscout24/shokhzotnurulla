@extends('layouts.base')

@push('panel-assets')
    @vite([
        'resources/css/dealer/app.css',
        'resources/js/dealer/app.js'
    ])
@endpush

@section('panel-content')
    <!-- Main Topbar -->
    @include('admin.partials.topbar')

    <div class="layout">
        <!-- Main Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        @yield('page-content')
    </div>

    <!-- Toastr Notifications / Alerts -->
    @include('partials.toastr-alerts')
@endsection
