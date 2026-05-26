<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

class Page extends Model
{
    use SoftDeletes;
    use \App\Models\Traits\HasLocationScope;

    public const HOME_ABOUT_CARD_CTA_SLUG = 'home-about-card-cta';

    protected $fillable = [
        'dealer_id',
        'location_id',
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

    public function scopeForAllLocations(Builder $query): Builder
    {
        return $query->whereNull($this->getTable() . '.location_id');
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

    public static function firstOrCreateGlobalSectionContent(
        int $dealerId,
        string $slug,
        string $title,
        string $defaultContent
    ): ?string {
        try {
            $page = static::query()
                ->forDealer($dealerId)
                ->where('slug', $slug)
                ->forAllLocations()
                ->first();

            if (! $page) {
                $page = static::query()
                    ->forDealer($dealerId)
                    ->where('slug', $slug)
                    ->first();
            }

            if (! $page) {
                $page = static::create([
                    'dealer_id'        => $dealerId,
                    'location_id'      => null,
                    'title'            => $title,
                    'slug'             => $slug,
                    'content'          => $defaultContent,
                    'meta_title'       => $title,
                    'meta_description' => null,
                    'meta_keywords'    => null,
                    'tags'             => ['homepage', 'global'],
                    'is_active'        => true,
                    'is_featured'      => false,
                    'published_at'     => now(),
                ]);
            } elseif ($page->location_id !== null) {
                $page->forceFill(['location_id' => null])->save();
            }

            if (! $page->is_active || ($page->published_at && $page->published_at->isFuture())) {
                return null;
            }

            return filled($page->content) ? $page->content : null;
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }
}
