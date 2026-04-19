<?php

namespace App\Models\Inventory;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleHiddenIncentive extends Model
{
    protected $fillable = [
        'vehicle_id',
        'incentive_id',
        'dealer_id',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function incentive(): BelongsTo
    {
        return $this->belongsTo(Incentive::class);
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}