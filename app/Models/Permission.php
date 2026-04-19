<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;
use App\Models\Dealership\Dealer;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'dealer_id', // Team/Dealer scope
    ];

    // /**
    //  * Relationship: Permission belongs to a Dealer (team)
    //  */
    // public function dealer()
    // {
    //     return $this->belongsTo(Dealer::class, 'dealer_id');
    // }

    // /**
    //  * Scope: permissions for a specific dealer
    //  */
    // public function scopeForDealer($query, $dealerId)
    // {
    //     return $query->where('dealer_id', $dealerId);
    // }
}
