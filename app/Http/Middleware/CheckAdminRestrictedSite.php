<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\AdminSetting;
use App\Models\AdminRestrictedSite;
use App\Models\User;

class CheckAdminRestrictedSite
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabled = AdminSetting::where('key', 'restricted_login_enabled')->value('value');

        if ($enabled === '1') {
            $user = $request->user();

            if (!$user && $request->has('email')) {
                $user = User::where('email', $request->input('email'))->first();
            }

            if ($user && $user->isSystemUser()) {
                $host = $request->getHost();
                $exists = AdminRestrictedSite::where('domain', $host)->exists();

                if (!$exists) {
                    return redirect()->back()
                        ->withInput($request->only('email', 'remember'))
                        ->withErrors(['email' => 'Login not allowed from this domain.']);
                }
            }
        }

        return $next($request);
    }
}
