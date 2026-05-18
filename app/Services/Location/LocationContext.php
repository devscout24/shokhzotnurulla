<?php

namespace App\Services\Location;

use App\Models\Website\Location;
use Illuminate\Support\Facades\Session;

class LocationContext
{
    /**
     * Memoized active location instance to avoid redundant DB queries.
     */
    protected ?Location $activeLocation = null;

    /**
     * Get the active location ID from the session.
     */
    public function getActiveLocationId(): ?int
    {
        return Session::get('active_location_id');
    }

    /**
     * Set the active location ID in the session.
     */
    public function setActiveLocationId(int $locationId): void
    {
        Session::put('active_location_id', $locationId);
        $this->activeLocation = null; // Clear memoization
    }

    /**
     * Clear the active location context.
     */
    public function clearActiveLocationId(): void
    {
        Session::put('active_location_id', 0); // 0 explicitly represents "All Locations"
        $this->activeLocation = null;
    }

    /**
     * Get the active Location model instance.
     */
    public function getActiveLocation(): ?Location
    {
        if ($this->activeLocation !== null) {
            return $this->activeLocation;
        }

        $locationId = $this->getActiveLocationId();

        if (!$locationId) {
            return null;
        }

        // Fetch location and ensure it belongs to the current dealer context to prevent cross-tenant access.
        $dealer = null;
        if (auth()->check() && auth()->user()->current_dealer_id) {
            $dealer = auth()->user()->currentDealer;
        } elseif (app()->bound('currentDealer')) {
            $dealer = app('currentDealer');
        }

        $query = Location::query();
        if ($dealer) {
            $query->where('dealer_id', $dealer->id);
        }

        $this->activeLocation = $query->find($locationId);

        // If the location isn't valid under the current dealer, clear it from session
        if (!$this->activeLocation) {
            $this->clearActiveLocationId();
        }

        return $this->activeLocation;
    }

    /**
     * Determine if a location context is currently active.
     */
    public function hasActiveLocation(): bool
    {
        return $this->getActiveLocationId() !== null;
    }
}
