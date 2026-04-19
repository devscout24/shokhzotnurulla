<?php

namespace App\Models\Inventory;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerInterestRate extends Model
{
    protected $fillable = [
        'dealer_id',
        'make',
        'min_model_year',
        'max_model_year',
        'min_term',
        'max_term',
        'min_credit_score',
        'max_credit_score',
        'condition',
        'rate',
        'sort_order',
    ];

    protected $casts = [
        'min_model_year'   => 'integer',
        'max_model_year'   => 'integer',
        'min_term'         => 'integer',
        'max_term'         => 'integer',
        'min_credit_score' => 'integer',
        'max_credit_score' => 'integer',
        'rate'             => 'decimal:2',
        'sort_order'       => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeForCondition($query, ?string $condition): static
    {
        if ($condition && $condition !== 'any') {
            $query->where('condition', $condition);
        }
        return $query;
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getConditionLabelAttribute(): string
    {
        return match ($this->condition) {
            'new'   => 'New',
            'used'  => 'Used',
            'cpo'   => 'Certified Pre-owned (CPO)',
            'vpo'   => 'Verified Pre-owned (VPO)',
            default => 'Any',
        };
    }

    public function getYearRangeKeyAttribute(): string
    {
        return "{$this->min_model_year}_{$this->max_model_year}";
    }

    public function getYearRangeLabelAttribute(): string
    {
        return $this->min_model_year === $this->max_model_year
            ? "Model Years: {$this->min_model_year} - {$this->min_model_year}"
            : "Model Years: {$this->min_model_year} - {$this->max_model_year}";
    }
}