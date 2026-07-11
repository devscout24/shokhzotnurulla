<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Inventory\Vehicle;

// Test 1: Does the vehicle exist and is it active?
$vin = '3C6LRVDG0PE582729';
$vehicle = Vehicle::where('vin', $vin)->first();
echo "Vehicle found: " . ($vehicle ? 'YES (id=' . $vehicle->id . ')' : 'NO') . "\n";
echo "Is active: " . ($vehicle && $vehicle->is_active ? 'YES' : 'NO') . "\n";
echo "Dealer ID: " . ($vehicle->dealer_id ?? 'null') . "\n";

// Test 2: Does whereHas('specs') work for gvwr=1000?
$hasSpecs = Vehicle::where('vin', $vin)->whereHas('specs', function ($q) {
    $q->where('gvwr', '>=', 1000)->where('gvwr', '<=', 2000);
})->exists();
echo "whereHas(specs, gvwr 1000-2000): " . ($hasSpecs ? 'MATCHES' : 'DOES NOT MATCH') . "\n";

// Test 3: What does the query look like?
$query = Vehicle::forDealer($vehicle->dealer_id)
    ->active()
    ->whereHas('specs', function ($q) {
        $q->where('gvwr', '>=', 1000)->where('gvwr', '<=', 2000);
    });
echo "Total matching vehicles: " . $query->count() . "\n";
echo "Matching VINs: " . $query->pluck('vin')->implode(', ') . "\n";

// Test 4: Check location context
$locationId = app(\App\Services\Location\LocationContext::class)->getResolvedLocationId($vehicle->dealer_id);
echo "Location ID: " . ($locationId ?? 'null (all)') . "\n";
if ($locationId) {
    $withLocation = Vehicle::forDealer($vehicle->dealer_id)
        ->active()
        ->where('location_id', $locationId)
        ->whereHas('specs', function ($q) {
            $q->where('gvwr', '>=', 1000)->where('gvwr', '<=', 2000);
        });
    echo "Matching with location filter: " . $withLocation->count() . "\n";
    echo "Vehicle location_id: " . ($vehicle->location_id ?? 'null') . "\n";
}
