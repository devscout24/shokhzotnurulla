<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Inventory\Vehicle;

class FactoryOption extends Model
{
    protected $fillable = [
        'category_id',
        'group_id',
        'option_key',
        'label',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FactoryOptionCategory::class, 'category_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(FactoryOptionGroup::class, 'group_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'factory_option_vehicle')
                    ->withPivot('is_starred')
                    ->withTimestamps();
    }
}