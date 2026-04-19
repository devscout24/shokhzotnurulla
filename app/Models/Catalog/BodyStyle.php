<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyStyle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'body_style_group_id',
        'name',
        'slug',
    ];

    /*
    |--------------
    | Relationships
    |--------------
    */

    public function group(): BelongsTo
    {
        return $this->belongsTo(BodyStyleGroup::class, 'body_style_group_id');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'body_style_id');
    }
}
