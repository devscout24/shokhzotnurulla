<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DealerStatusMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Get the current dealer from the service container (resolved in TenantServiceProvider)
        $dealer = app('currentDealer');

        // 2. If no dealer is resolved for the current domain, abort (or redirect)
        if (!$dealer) {
            abort(404, 'Dealer not found for this domain.');
        }

        // 3. Check if the dealer is active
        if (!$dealer->is_active) {
            // Option A: Abort with 403 Forbidden
            // abort(403, 'Your account has been suspended. Please contact support.');

            // Option B: Redirect to a dedicated suspension page
            return response()->view('errors.suspended', [
                'dealer' => $dealer
            ], 403);
        }

        return $next($request);
    }
}
