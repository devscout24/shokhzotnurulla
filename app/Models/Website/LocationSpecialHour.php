<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationSpecialHour extends Model
{
    protected $fillable = ['location_id', 'department', 'date', 'open_time', 'close_time', 'is_closed', 'appointment_only'];

    protected $casts = [
        'date' => 'date',
        'is_closed' => 'boolean',
        'appointment_only' => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}