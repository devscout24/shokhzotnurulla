<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Redirect extends Model
{
    protected $fillable = ['dealer_id', 'source_url', 'target_url', 'is_regex', 'status_code', 'is_enabled'];

    protected $casts = [
        'is_regex' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }
}