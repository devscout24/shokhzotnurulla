<?php

namespace App\Models\Dealership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerIntegration extends Model
{
    protected $fillable = [
        'dealer_id',
        'provider',
        'settings',
        'is_active',
        'last_connected_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'encrypted:json', // Encrypted at rest, handled as array in PHP
        'last_connected_at' => 'datetime',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}
