<?php

namespace App\Models\Website;

use App\Models\User;
use App\Models\Dealership\Dealer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    protected $fillable = [
        'dealer_id',
        'user_id',
        'original_name',
        'name',
        'path',
        'disk',
        'url',
        'type',
        'mime_type',
        'size',
        'width',
        'height',
        'title',
        'alt_text',
    ];

    protected $casts = [
        'size'   => 'integer',
        'width'  => 'integer',
        'height' => 'integer',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getDimensionsAttribute(): string
    {
        if ($this->width && $this->height) {
            return "{$this->width} x {$this->height}";
        }
        return '—';
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeForDealer($query, int $dealerId)
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeFiles($query)
    {
        return $query->where('type', 'file');
    }
}