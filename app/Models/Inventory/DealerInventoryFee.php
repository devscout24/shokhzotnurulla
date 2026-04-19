<?php

namespace App\Models\Inventory;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerInventoryFee extends Model
{
    protected $fillable = [
        'dealer_id',
        'name',
        'description',
        'type',
        'value',
        'tax',
        'is_optional',
        'condition',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'value'       => 'decimal:2',
            'is_optional' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getFormattedValueAttribute(): string
    {
        return $this->type === 'amount'
            ? '$' . number_format($this->value, 2)
            : $this->value . '%';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'amount' ? 'Amount' : 'Percentage';
    }

    public function getTaxLabelAttribute(): string
    {
        return $this->tax === 'pre-tax' ? 'Pre-tax' : 'Post-tax';
    }

    public function getOptionalLabelAttribute(): string
    {
        return $this->is_optional ? 'Optional' : 'Guaranteed';
    }

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'new'   => 'New',
            'used'  => 'Used',
            'cpo'   => 'CPO',
            'vpo'   => 'VPO',
            default => 'Any',
        };
    }
}