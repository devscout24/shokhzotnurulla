<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    use VerifiesEmails;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    protected function redirectTo(): string
    {
        $user = auth()->user();

        setPermissionsTeamId($user->current_dealer_id);

        session()->flash('success', 'Your email has been verified successfully. Welcome aboard!');

        return $user->isSystemUser()
            ? route('admin.dashboard')
            : route('dealer.website.dashboard');
    }
}