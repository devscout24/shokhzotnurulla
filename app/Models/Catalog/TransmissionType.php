<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Inventory\Vehicle;

class TransmissionType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'standard',
    ];

    // ─── Relationships ──────────────────────────────────────────────

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'transmission_type_id');
    }
}
