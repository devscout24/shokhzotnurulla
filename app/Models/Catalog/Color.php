<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory\Vehicle;

class Color extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'standard_name',
        'slug',
    ];

    // ─── Relationships ──────────────────────────────────────────────

    public function vehiclesExterior(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'exterior_color_id');
    }

    public function vehiclesInterior(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'interior_color_id');
    }
}
