<?php

namespace App\Models\Website;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
