@extends('layouts.dealer.app')

@section('title', __('Dashboard') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header">
            <h2 class="view-title">Dashboard</h2>
            {{-- <div class="date-picker-box">
                <i class="bi bi-calendar3"></i>
                <input type="text" id="dateRange" readonly>
            </div> --}}
        </div>

        <hr>

        {{-- dashboard.blade.php mein --}}
        <div class="view-content" data-view="dashboard">
            @include('dealer.components.dashboard.dashboard-stats')
        </div>
    </main>
@endsection