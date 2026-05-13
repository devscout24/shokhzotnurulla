<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteVisitorLog extends Model
{
    protected $fillable = [
        'dealer_id',
        'ip_address',
        'device_brand',
        'device_model',
        'device_type',
        'country',
        'state',
        'city',
        'url',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'session_id',
        'language',
    ];
}
