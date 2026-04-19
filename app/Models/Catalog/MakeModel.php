<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MakeModel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'make_id',
        'name',
        'slug',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(\App\Models\Inventory\Vehicle::class, 'make_model_id');
    }
}
