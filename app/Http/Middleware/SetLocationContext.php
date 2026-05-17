<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\Location\LocationContext;
use Symfony\Component\HttpFoundation\Response;

class SetLocationContext
{
    protected LocationContext $locationContext;

    public function __construct(LocationContext $locationContext)
    {
        $this->locationContext = $locationContext;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Allow switching the location context via query parameter (e.g., ?location_context_id=X)
        if ($request->has('location_context_id')) {
            $newLocationId = (int) $request->query('location_context_id');
            
            if ($newLocationId === 0) {
                // Special case to clear/reset the active location (e.g. "All Locations")
                $this->locationContext->clearActiveLocationId();
            } else {
                // Ensure the location exists and belongs to the resolved dealer
                $dealer = app()->bound('currentDealer') ? app('currentDealer') : null;
                $exists = \App\Models\Website\Location::query()
                    ->when($dealer, fn($q) => $q->where('dealer_id', $dealer->id))
                    ->where('id', $newLocationId)
                    ->exists();

                if ($exists) {
                    $this->locationContext->setActiveLocationId($newLocationId);
                }
            }
        }

        // 2. Default to the first added location for this dealer if no context is set in the session yet
        if (!\Illuminate\Support\Facades\Session::has('active_location_id')) {
            $dealer = app()->bound('currentDealer') ? app('currentDealer') : null;
            if ($dealer) {
                $firstLocation = $dealer->locations()->first();
                if ($firstLocation) {
                    $this->locationContext->setActiveLocationId($firstLocation->id);
                }
            }
        }

        // 3. Resolve the active Location model (this also validates it against the current tenant/dealer)
        $activeLocation = $this->locationContext->getActiveLocation();

        // 3. Share the active location with the service container and Laravel's view layer for UI access
        if ($activeLocation) {
            app()->instance('currentLocation', $activeLocation);
            view()->share('currentLocation', $activeLocation);
        } else {
            view()->share('currentLocation', null);
        }

        // 4. Also share all available locations for this dealer (e.g. to populate context switcher dropdowns in blade/views)
        $dealer = app()->bound('currentDealer') ? app('currentDealer') : null;
        if ($dealer) {
            $availableLocations = \Illuminate\Support\Facades\Cache::remember(
                "dealer_locations:{$dealer->id}",
                now()->addMinutes(15),
                fn() => $dealer->locations()->get(['id', 'name', 'city', 'state'])
            );
            view()->share('availableLocations', $availableLocations);
        } else {
            view()->share('availableLocations', collect());
        }

        return $next($request);
    }
}
