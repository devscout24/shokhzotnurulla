<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class StaffMemberCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class, 'staff_member_category_id');
    }
}
