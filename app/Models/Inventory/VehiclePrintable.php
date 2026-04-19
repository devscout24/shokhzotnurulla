<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehiclePrintable extends Model
{
    protected $fillable = [
        'vehicle_id',
        'dealer_id',
        'name',
        'cta',
        'description',
        'layout',
        'html_template',
    ];

    // ── Printable types — "only one of each" constraint ───────────────────────

    public const TYPES = [
        'Window Sticker' => 'Window Sticker',
        'Buyer\'s Guide' => 'Buyer\'s Guide',
        'Generate Quote' => 'Generate Quote',
    ];

    public const CTA_OPTIONS = [
        'Print Sticker'  => 'Print Sticker',
        'Print Guide'    => 'Print Guide',
        'Generate Quote' => 'Generate Quote',
    ];

    public const LAYOUTS = [
        'portrait'  => 'Portrait',
        'landscape' => 'Landscape',
    ];

    // ── Relationships ─────────────────────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
