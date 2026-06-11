@extends('layouts.base')

@push('panel-assets')
    @vite([
        'resources/css/dealer/app.css',
        'resources/js/dealer/app.js'
    ])
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush


@push('base-assets')
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml" />
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

    <!-- SweetAlert2 Global Confirm Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Intercept all forms with data-swal-confirm attribute
            document.querySelectorAll('form[data-swal-confirm]').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const message = form.getAttribute('data-swal-confirm') || 'Are you sure you want to proceed?';
                    const title = form.getAttribute('data-swal-title') || 'Are you sure?';

                    Swal.fire({
                        title: title,
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: 'purple',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Continue',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true,
                        customClass: {
                            popup: 'swal2-admin-popup',
                            title: 'swal2-admin-title',
                            confirmButton: 'swal2-admin-confirm',
                            cancelButton: 'swal2-admin-cancel'
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection
