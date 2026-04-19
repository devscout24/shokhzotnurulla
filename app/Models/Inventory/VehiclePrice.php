<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePrice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'msrp',
        'dealer_cost',
        'pricing_disclaimer',
        'special_price',
        'special_price_label',
        'addon_price',
        'addon_price_label',
        'addon_price_description',
        'adjustment_label',
        'internet_price',
        'asking_price',
        'sold_price',
        'sold_date',
        'sold_to',
    ];

    protected $casts = [
        'msrp'          => 'decimal:2',
        'dealer_cost'   => 'decimal:2',
        'special_price' => 'decimal:2',
        'addon_price'   => 'decimal:2',
        'internet_price'=> 'decimal:2',
        'asking_price'  => 'decimal:2',
        'sold_price'    => 'decimal:2',
        'sold_date'     => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
