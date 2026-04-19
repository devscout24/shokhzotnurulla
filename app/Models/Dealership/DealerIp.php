<?php

namespace App\Models\Dealership;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerIp extends Model
{
    protected $fillable = ['dealer_id', 'ip_address', 'description'];

    protected $casts = [
        'ip_address' => 'string',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}