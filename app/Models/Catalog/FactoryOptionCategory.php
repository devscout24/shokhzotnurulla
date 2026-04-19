<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryOptionCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function groups(): HasMany
    {
        return $this->hasMany(FactoryOptionGroup::class, 'category_id');
    }
}
