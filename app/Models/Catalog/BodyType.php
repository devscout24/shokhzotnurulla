<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Inventory\Vehicle;

class BodyType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'body_type_group_id',
        'name',
        'slug',
    ];

    // ─── Relationships ──────────────────────────────────────────────

    public function group(): BelongsTo
    {
        return $this->belongsTo(BodyTypeGroup::class, 'body_type_group_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'body_type_id');
    }
}
