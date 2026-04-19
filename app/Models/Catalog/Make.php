<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Make extends Model
{
    protected $fillable = ['name', 'slug', 'is_active'];

    // ─── Relationships ───────────────────────────────────────────

    public function models(): HasMany
    {
        return $this->hasMany(MakeModel::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(\App\Models\Inventory\Vehicle::class);
    }
}
