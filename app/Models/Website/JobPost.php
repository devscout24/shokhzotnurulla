<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    protected $fillable = [
        'job_post_category_id',
        'job_title',
        'job_description',
        'status',
        'author',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(JobPostCategory::class, 'job_post_category_id');
    }
}
