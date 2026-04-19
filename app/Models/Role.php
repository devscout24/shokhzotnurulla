<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use App\Models\Dealership\Dealer;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'dealer_id', // Team/Dealer scope
        'is_active', // Active/inactive flag
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationship: Role belongs to a Dealer (team)
     */
    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    /**
     * Scope: only active roles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
