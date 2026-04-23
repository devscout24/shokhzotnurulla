<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class JobPostCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function jobPosts()
    {
        return $this->hasMany(JobPost::class, 'job_post_category_id');
    }
}
