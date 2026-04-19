<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryOptionGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
    ];

    /*
    |--------------
    | Relationships
    |--------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(FactoryOptionCategory::class, 'category_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(FactoryOption::class, 'group_id');
    }
}
