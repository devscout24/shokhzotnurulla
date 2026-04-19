<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function apps()
    {
        return view('dealer.pages.connections.apps');
    }

    public function links()
    {
        return view('dealer.pages.connections.links');
    }
}
