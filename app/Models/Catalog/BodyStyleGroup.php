<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyStyleGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // ─── Relationships ───────────────────────────────────────────

    public function bodyStyles(): HasMany
    {
        return $this->hasMany(BodyStyle::class);
    }
}