<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BodyTypeGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // ─── Relationships ───────────────────────────────────────────

    public function bodyTypes(): HasMany
    {
        return $this->hasMany(BodyType::class);
    }
}