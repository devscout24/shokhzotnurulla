<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EnsureTwoFactorAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_2fa_required && !Session::get('2fa_verified')) {
            $user = Auth::user();

            if ($user->isSystemUser()) {
                // Exclude the Admin 2FA verify routes to prevent infinite loop
                if (!$request->routeIs('admin.2fa.*')) {
                    if (empty($user->google2fa_secret)) {
                        if (!$request->routeIs('admin.profile.edit')) {
                            return redirect()->route('admin.profile.edit')
                                ->with('warning', 'You must set up Two-Factor Authentication before continuing.');
                        }
                    } else {
                        return redirect()->route('admin.2fa.verify');
                    }
                }
            } else {
                // Exclude the Dealer 2FA verify routes to prevent infinite loop
                if (!$request->routeIs('dealer.2fa.*')) {
                    if (empty($user->google2fa_secret)) {
                        if (!$request->routeIs('dealer.settings.authentication')) {
                            return redirect()->route('dealer.settings.authentication')
                                ->with('warning', 'You must set up Two-Factor Authentication before continuing.');
                        }
                    } else {
                        return redirect()->route('dealer.2fa.verify');
                    }
                }
            }
        }

        return $next($request);
    }
}
