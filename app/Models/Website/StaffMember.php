<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    protected $fillable = [
        'staff_member_category_id',
        'full_name',
        'job_title',
        'photo_url',
        'email_address',
        'phone_number',
        'short_bio',
        'status',
        'author',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(StaffMemberCategory::class, 'staff_member_category_id');
    }
}
