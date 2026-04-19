@extends('layouts.dealer.app')

@section('title', __('Authentication') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/settings.css',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content" data-view="authentication">
            <p>Authentication</p>
        </div>
    </main>
@endsection