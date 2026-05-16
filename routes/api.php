<?php

use App\Models\Dealership\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/is-domain-valid', function (Request $request) {
    $is_valid = Dealer::where('domain', $request->get('domain'))
        ->orWhere('staging_domain', $request->get('domain'))
        ->exists();
    
    return response()->json(['is_valid' => $is_valid]);
});