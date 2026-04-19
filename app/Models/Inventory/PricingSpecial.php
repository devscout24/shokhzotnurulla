<?php

namespace App\Models\Inventory;

use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\Color;
use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingSpecial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id', 'title', 'type', 'button_text', 'discount_label',
        'stackable', 'priority', 'discount_type', 'amount',
        'finance_rate', 'finance_term', 'condition', 'is_certified',
        'model_number', 'year', 'make_id', 'make_model_id', 'trim',
        'body_style', 'exterior_color_id', 'stock_number', 'tag',
        'min_days', 'max_days', 'send_email', 'hide_price',
        'starts_at', 'ends_at', 'notes', 'disclaimer', 'is_enabled',
    ];

    protected $casts = [
        'stackable'    => 'boolean',
        'is_certified' => 'boolean',
        'send_email'   => 'boolean',
        'hide_price'   => 'boolean',
        'is_enabled'   => 'boolean',
        'amount'       => 'decimal:2',
        'finance_rate' => 'decimal:2',
        'starts_at'    => 'date',
        'ends_at'      => 'date',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function makeModel(): BelongsTo
    {
        return $this->belongsTo(MakeModel::class, 'make_model_id');
    }

    public function exteriorColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'exterior_color_id');
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDiscountTypeLabelAttribute(): string
    {
        return match ($this->discount_type) {
            'fixed'          => 'Fixed Amount',
            'percentage'     => 'Percentage off',
            'dollars'        => 'Dollars off',
            'offsetdollar'   => 'Offset dollars off',
            'special'        => 'Special Financing',
            'offsetincrease' => 'Offset price increase',
            'increase'       => 'Price increase',
            default          => ucfirst($this->discount_type ?? '—'),
        };
    }

    public function getAmountDisplayAttribute(): string
    {
        if ($this->discount_type === 'special') {
            return $this->finance_rate ? $this->finance_rate . '%' : '—';
        }

        if (! $this->amount) {
            return '—';
        }

        return $this->discount_type === 'percentage'
            ? $this->amount . '%'
            : '$' . number_format((float) $this->amount, 2);
    }

    public function getMonthsDisplayAttribute(): string
    {
        return $this->discount_type === 'special' && $this->finance_term
            ? $this->finance_term . ' mo.'
            : '—';
    }

    public function getCriteriaDisplayAttribute(): string
    {
        $parts = array_filter([
            $this->condition,
            $this->make?->name,
            $this->year ? (string) $this->year : null,
        ]);

        return $parts ? implode(', ', $parts) : 'All';
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

    public function scopeActive($query): void
    {
        $query->where(function ($q) {
            $q->whereNull('ends_at')
              ->orWhere('ends_at', '>=', now()->toDateString());
        });
    }
}