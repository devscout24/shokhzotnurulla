<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;

class EnsureDealerIsActive
{
    use HandlesUserLogout;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->dealerIsActive()) {
            AuditLogger::toChannel('audit-dealer', 'warning', 'User of inactive dealer attempted access', [
                'user_id'   => $user->id,
                'email'     => $user->email,
                'dealer_id' => $user->dealer_id ?? null,
                'url'       => request()->url(),
                'method'    => request()->method(),
                'ip'        => request()->ip(),
                'at'        => now(),
            ]);

            return $this->logoutWithMessage($request, 'Your dealership account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}