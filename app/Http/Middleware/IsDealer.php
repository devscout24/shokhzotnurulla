<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;

class IsDealer
{
    use HandlesUserLogout;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            AuditLogger::warning($request, 'Guest attempted dealer panel access');

            return $this->logoutWithMessage($request, 'Please login to access dealer panel.');
        }

        if (! $user->isDealerUser()) {
            AuditLogger::warning($request, 'Unauthorized role attempted dealer panel access', [
                'user_id' => $user->id,
                'email'   => $user->email,
            ]);

            return $this->logoutWithMessage($request, 'Unauthorized access to dealer panel.');
        }

        return $next($request);
    }
}