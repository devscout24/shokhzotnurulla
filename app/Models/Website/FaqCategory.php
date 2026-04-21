<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    protected $fillable = [
        'dealer_id',
        'name',
        'sort_order',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDealer(Builder $query, int $dealerId): Builder
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
