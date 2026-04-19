<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Observers\Website\MenuObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([MenuObserver::class])]

class Menu extends Model
{
    protected $fillable = [
        'dealer_id',
        'location',
        'label',
        'url',
        'target',
        'parent_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'parent_id'  => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForDealer($query, int $dealerId)
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeForLocation($query, string $location)
    {
        return $query->where('location', $location);
    }
}