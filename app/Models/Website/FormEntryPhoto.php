<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEntryPhoto extends Model
{
    protected $fillable = [
        'form_entry_id',
        'path',
        'disk',
        'url',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function formEntry(): BelongsTo
    {
        return $this->belongsTo(FormEntry::class);
    }
}