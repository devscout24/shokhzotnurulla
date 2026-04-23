<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'reviewer_name',
        'review_headline',
        'review_date',
        'review_source',
        'star_count',
        'customer_review_category_id',
        'photo_url',
        'content',
        'status',
        'author',
        'sort_order',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CustomerReviewCategory::class, 'customer_review_category_id');
    }
}
