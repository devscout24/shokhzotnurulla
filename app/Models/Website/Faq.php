<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id',
        'faq_category_id',
        'question',
        'answer',
        'author',
        'status',
        'sort_order',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDealer(Builder $query, int $dealerId): Builder
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'Published');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('faq_category_id', $categoryId);
    }
}
