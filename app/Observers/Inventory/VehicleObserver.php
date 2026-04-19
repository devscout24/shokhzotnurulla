<?php

namespace App\Observers\Inventory;

use App\Models\Inventory\Vehicle;
use App\Services\Inventory\InventoryListingService;

class VehicleObserver
{
    public function __construct(
        private readonly InventoryListingService $inventoryListing,
    ) {}

    public function created(Vehicle $vehicle): void
    {
        $this->inventoryListing->invalidateFilterCache($vehicle->dealer_id);
    }

    public function updated(Vehicle $vehicle): void
    {
        $this->inventoryListing->invalidateFilterCache($vehicle->dealer_id);
    }

    public function deleted(Vehicle $vehicle): void
    {
        $this->inventoryListing->invalidateFilterCache($vehicle->dealer_id);
    }

    public function restored(Vehicle $vehicle): void
    {
        $this->inventoryListing->invalidateFilterCache($vehicle->dealer_id);
    }

    public function forceDeleted(Vehicle $vehicle): void
    {
        $this->inventoryListing->invalidateFilterCache($vehicle->dealer_id);
    }
}