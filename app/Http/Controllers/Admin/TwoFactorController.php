<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    /**
     * Verify the code to enable 2FA for admin users.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        $secret = session('google2fa_secret_setup');

        if (!$secret) {
            return back()->with('error', 'Session expired. Please try again.');
        }

        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

        if ($valid) {
            // Save the secret and enforce 2FA
            $user->update([
                'google2fa_secret' => $secret,
                'is_2fa_required' => true,
            ]);

            // Clear setup session and set verified session
            session()->forget('google2fa_secret_setup');
            Session::put('2fa_verified', true);

            return back()->with('success', 'Two-Factor Authentication has been enabled successfully.');
        }

        return back()->with('error', 'Invalid verification code. Please try again.');
    }

    /**
     * Disable 2FA for admin users.
     */
    public function disable(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $user->update([
            'google2fa_secret' => null,
            'is_2fa_required' => false,
        ]);

        Session::forget('2fa_verified');

        return back()->with('success', 'Two-Factor Authentication has been disabled.');
    }

    /**
     * Show the 2FA login verification form for admin users.
     */
    public function showVerifyForm(): View
    {
        return view('auth.2fa', [
            'verifyRoute' => route('admin.2fa.verify.post'),
        ]);
    }

    /**
     * Verify the 2FA code during admin login.
     */
    public function verifyLogin(Request $request): RedirectResponse
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            Session::put('2fa_verified', true);

            // Redirect to intended or admin dashboard
            return redirect()->intended(route('admin.dealers.index'));
        }

        return back()->withErrors(['one_time_password' => 'Invalid verification code. Please try again.']);
    }
}
