<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;

class IsAdmin
{
    use HandlesUserLogout;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            AuditLogger::warning($request, 'Guest attempted admin panel access');

            return $this->logoutWithMessage($request, 'Please login to access admin panel.');
        }

        if (! $user->isSystemUser()) {
            AuditLogger::warning($request, 'Unauthorized role attempted admin panel access', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return $this->logoutWithMessage($request, 'Unauthorized access to admin panel.');
        }

        return $next($request);
    }
}