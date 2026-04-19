<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;

class EnsureRoleIsActive
{
    use HandlesUserLogout;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->hasInvalidRoleState()) {
            AuditLogger::warning($request, 'User with invalid role attempted access', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'role_id' => $user->role_id ?? null,
            ]);

            return $this->logoutWithMessage($request, 'Your assigned role is invalid or inactive. Please contact support.');
        }

        return $next($request);
    }
}