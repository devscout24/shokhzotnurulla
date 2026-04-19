<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory\Vehicle;

class FuelType extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    // ─── Relationships ──────────────────────────────────────────────

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'fuel_type_id');
    }
}
