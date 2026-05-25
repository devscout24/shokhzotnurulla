<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * Adds location-aware scoping to any model that has a `location_id` column.
 *
 * Usage in controllers:
 *   Model::forDealer($dealerId)->forActiveLocation()->get();
 *
 * The scope reads the active location from the LocationContext service.
 * When no location is active (i.e. "All Locations"), it is a no-op.
 */
trait HasLocationScope
{
    /**
     * Scope to the currently active location (session-driven).
     * If no location is active, this is a no-op and returns all records.
     */
    public function scopeForActiveLocation(Builder $query): Builder
    {
        $locationId = app(\App\Services\Location\LocationContext::class)->getActiveLocationId();

        return $query->when($locationId, fn (Builder $q) => $q->where(
            $this->getTable() . '.location_id',
            $locationId
        ));
    }

    /**
     * Scope to a specific location by ID.
     */
    public function scopeForLocation(Builder $query, ?int $locationId): Builder
    {
        return $query->when($locationId, fn (Builder $q) => $q->where(
            $this->getTable() . '.location_id',
            $locationId
        ));
    }

    /**
     * Get the active location ID from the session context.
     * Useful in store/create methods.
     */
    public static function getActiveLocationId(): ?int
    {
        return app(\App\Services\Location\LocationContext::class)->getActiveLocationId();
    }
}
