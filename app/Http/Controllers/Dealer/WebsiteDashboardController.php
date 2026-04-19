<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteDashboardController extends Controller
{
    public function dashboard()
    {
        return view('dealer.pages.dashboard');
    }
}
