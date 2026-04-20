<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id',
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'tags',
        'is_active',
        'is_featured',
        'published_at',
    ];

    protected $casts = [
        'tags'         => 'array',
        'is_active'    => 'boolean',
        'is_featured'  => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDealer(Builder $query, int $dealerId): Builder
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeByTag(Builder $query, string $tag): Builder
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeOrderByPublished(Builder $query): Builder
    {
        return $query->orderBy('published_at', 'desc');
    }

    // ── Methods ──────────────────────────────────────────────────────────────

    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if (!$this->published_at) {
            return 'Draft';
        }

        if ($this->published_at > now()) {
            return 'Scheduled';
        }

        return 'Published';
    }
}
