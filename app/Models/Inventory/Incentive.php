<?php

namespace App\Models\Inventory;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incentive extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id', 'title', 'type', 'category', 'description',
        'amount', 'amount_type', 'program_code',
        'is_guaranteed', 'is_featured', 'is_enabled', 'expires_at',
    ];

    protected $casts = [
        'is_guaranteed' => 'boolean',
        'is_featured'   => 'boolean',
        'is_enabled'    => 'boolean',
        'expires_at'    => 'date',
        'amount'        => 'decimal:2',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'cash'           => 'Cash',
            'finance'        => 'Finance',
            'ivc_dvc'        => 'IVC / DVC',
            'lease'          => 'Lease',
            'percentage_off' => 'Percentage Off',
            default          => ucfirst($this->type),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'all'  => 'All',
            'used' => 'Used',
            'new'  => 'New',
            'cpo'  => 'CPO',
            default => strtoupper($this->category),
        };
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDealer($query, int $dealerId): void
    {
        $query->where('dealer_id', $dealerId);
    }

    public function scopeEnabled($query): void
    {
        $query->where('is_enabled', true);
    }
}