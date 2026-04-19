<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

// Catalog models
use App\Models\Catalog\Make;
use App\Models\Catalog\MakeModel;
use App\Models\Catalog\BodyType;
use App\Models\Catalog\BodyStyle;
use App\Models\Catalog\FuelType;
use App\Models\Catalog\TransmissionType;
use App\Models\Catalog\DrivetrainType;
use App\Models\Catalog\Color;
use App\Models\Catalog\Feature;
use App\Models\Catalog\FactoryOption;
use App\Models\Inventory\VehiclePrintable;

// Dealership models
use App\Models\Dealership\Dealer;

// Observer
use App\Observers\Inventory\VehicleObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([VehicleObserver::class])]

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealer_id',
        'ulid',
        'stock_number',
        'vin',
        'model_number',
        'year',
        'make_id',
        'make_model_id',
        'trim',
        'body_type_id',
        'body_style_id',
        'vehicle_condition',
        'is_certified',
        'is_commercial',
        'location_status',
        'fuel_type_id',
        'transmission_type_id',
        'drivetrain_type_id',
        'engine',
        'mileage',
        'exterior_color_id',
        'interior_color_id',
        'doors',
        'seating_capacity',
        'list_price',
        'status',
        'is_on_hold',
        'is_spotlight',
        'ignore_feed_updates',
        'source',
        'featured',
        'inventory_date',
        'original_price',
        'listed_at',
        'sold_at',
    ];

    protected $casts = [
        'is_certified'        => 'boolean',
        'is_commercial'       => 'boolean',
        'is_on_hold'          => 'boolean',
        'is_spotlight'        => 'boolean',
        'ignore_feed_updates' => 'boolean',
        'featured'            => 'boolean',
        'list_price'          => 'decimal:2',
        'original_price'      => 'decimal:2',
        'inventory_date'      => 'date',
        'listed_at'           => 'datetime',
        'sold_at'             => 'datetime',
    ];

    // ─── Relationships — Dealership ───────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    // ─── Relationships — Catalog ──────────────────────────────────────────────

    public function make(): BelongsTo
    {
        return $this->belongsTo(Make::class);
    }

    public function makeModel(): BelongsTo
    {
        return $this->belongsTo(MakeModel::class, 'make_model_id');
    }

    public function bodyType(): BelongsTo
    {
        return $this->belongsTo(BodyType::class);
    }

    public function bodyStyle(): BelongsTo
    {
        return $this->belongsTo(BodyStyle::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function transmissionType(): BelongsTo
    {
        return $this->belongsTo(TransmissionType::class);
    }

    public function drivetrainType(): BelongsTo
    {
        return $this->belongsTo(DrivetrainType::class);
    }

    public function exteriorColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'exterior_color_id');
    }

    public function interiorColor(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'interior_color_id');
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'vehicle_features')
                    ->withTimestamps();
    }

    public function factoryOptions(): BelongsToMany
    {
        return $this->belongsToMany(FactoryOption::class, 'factory_option_vehicle')
                    ->withPivot('is_starred')
                    ->withTimestamps();
    }

    public function hiddenIncentives(): HasMany
    {
        return $this->hasMany(VehicleHiddenIncentive::class);
    }


    // ─── Relationships — Inventory (same namespace, no import needed) ─────────

    public function prices(): HasOne
    {
        return $this->hasOne(VehiclePrice::class);
    }

    public function specs(): HasOne
    {
        return $this->hasOne(VehicleSpec::class);
    }

    public function notes(): HasOne
    {
        return $this->hasOne(VehicleNote::class);
    }

    public function video(): HasOne
    {
        return $this->hasOne(VehicleVideo::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(VehiclePhoto::class)->where('is_primary', true);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(VehicleTag::class);
    }

    public function premiumOptions(): HasMany
    {
        return $this->hasMany(VehiclePremiumOption::class);
    }

    public function printables(): HasMany
    {
        return $this->hasMany(VehiclePrintable::class);
    }

    public function dailyStats(): HasMany
    {
        return $this->hasMany(VehicleDailyStat::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDisplayTitleAttribute(): string
    {
        return trim(implode(' ', array_filter([
            $this->year,
            $this->make?->name,
            $this->makeModel?->name,
            $this->trim,
        ])));
    }

    public function getDaysOnLotAttribute(): int
    {
        if (! $this->listed_at) {
            return 0;
        }

        $end = $this->sold_at ?? now();

        return (int) $this->listed_at->diffInDays($end);
    }

    // ─── Slug Accessor ────────────────────────────────────────────────────────────
    public function getSlugAttribute(): string
    {
        $parts = [
            'used',
            $this->year,
            Str::slug($this->make?->name ?? ''),
            Str::slug($this->makeModel?->name ?? ''),
        ];

        if ($this->trim) {
            $parts[] = Str::slug($this->trim);
        }

        if ($this->vin) {
            $parts[] = strtolower($this->vin);
        }

        $parts[] = 'in-smyrna-tn';

        return implode('-', array_filter($parts));
    }


    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForDealer($query, int $dealerId)
    {
        return $query->where('dealer_id', $dealerId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeSold($query)
    {
        return $query->where('status', 'sold');
    }

    public function scopeOnHold($query)
    {
        return $query->where('is_on_hold', true);
    }
}