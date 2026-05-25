<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use \App\Models\Traits\HasLocationScope;

    protected $fillable = [
        'location_id',
        'event_category_id',
        'title',
        'photo_url',
        'detail_link',
        'registration_link',
        'event_date',
        'start_time',
        'end_time',
        'description',
        'status',
        'author',
        'sort_order',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }
}
