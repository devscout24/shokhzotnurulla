<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DigitalRetailSetting extends Model
{
    protected $fillable = [
        'dealer_id', 'shipping_free_miles', 'shipping_discount_dollars',
        'deposit_minimum', 'deposit_hold_hours', 'digital_retail_hold_hours',
        'trade_days_valid',
    ];

    protected $casts = [
        'shipping_free_miles' => 'integer',
        'shipping_discount_dollars' => 'decimal:2',
        'deposit_minimum' => 'decimal:2',
        'deposit_hold_hours' => 'integer',
        'digital_retail_hold_hours' => 'integer',
        'trade_days_valid' => 'integer',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}