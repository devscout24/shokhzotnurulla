<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;

class EnsureUserIsActive
{
    use HandlesUserLogout;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if (! $user->is_active) {
            AuditLogger::warning($request, 'Inactive user attempted access', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return $this->logoutWithMessage($request, 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}