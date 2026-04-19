@extends('layouts.frontend.app')

@section('title', __('Direction') . ' | '. __(config('app.name')))

@section('page-content')
    <div class="d-block d-xl-none h-63"></div>

    <main class="d-flex align-items-center justify-content-center" style="min-height: 60vh;">
	    <div class="text-center py-5 px-4">
	        <div class="mb-4">
	            <i class="fa-solid fa-screwdriver-wrench text-primary" style="font-size: 64px;"></i>
	        </div>
	        <h1 class="fw-bold mb-3">Coming Soon</h1>
	        <p class="text-muted mb-4" style="max-width: 420px; margin: 0 auto;">
	            We're working on something great. This page will be available soon.
	        </p>
	        <a href="{{ route('frontend.inventory') }}" class="btn btn-primary px-4 me-2">
	            Browse Inventory
	        </a>
	        <a href="{{ route('frontend.home') }}" class="btn btn-default px-4">
	            Go Home
	        </a>
	    </div>
	</main>
@endsection
