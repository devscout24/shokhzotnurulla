<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebsiteReportController extends Controller
{
    /**
     * Display the reports landing page.
     */
    public function index()
    {
        return view('dealer.pages.website.reports.index');
    }
}
