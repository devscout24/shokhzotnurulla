<?php

namespace App\Models\Dealership;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\Inventory\DealerInterestRate;
use App\Models\Inventory\DealerInventoryFee;
use App\Models\Inventory\Incentive;
use App\Models\Inventory\VehicleHiddenIncentive;
use App\Models\Inventory\PricingSpecial;
use App\Models\Website\FormEntry;
use App\Models\Website\Media;
use App\Models\Website\Menu;
use App\Models\Website\Location;
use App\Models\Website\Domain;
use App\Models\Website\Redirect;
use App\Models\Website\DigitalRetailSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

use App\Observers\Dealership\DealerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([DealerObserver::class])]

class Dealer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'is_active',
        'legal_name',
        'corporate_address',
        'support_email',
        'abandoned_form_minutes',
        'social_links',
        'finance_disclaimer',
        'inventory_disclaimer',
        'deposit_disclaimer',
        'pricing_disclaimer',
        'optional_disclaimer',
        'banner_text',
        'banner_hover_title',
        'banner_text_color',
        'banner_bg_color',
        'banner_desktop_media_id',
        'banner_mobile_media_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'social_links' => AsArrayObject::class,
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('is_owner')
                    ->withTimestamps();
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('is_owner')
                    ->wherePivot('is_owner', true)
                    ->withTimestamps();
    }

    public function interestRates(): HasMany
    {
        return $this->hasMany(DealerInterestRate::class);
    }

    public function inventoryFees(): HasMany
    {
        return $this->hasMany(DealerInventoryFee::class);
    }

    public function incentives(): HasMany
    {
        return $this->hasMany(Incentive::class);
    }

    public function formEntries(): HasMany
    {
        return $this->hasMany(FormEntry::class);
    }

    public function hiddenIncentives(): HasMany
    {
        return $this->hasMany(VehicleHiddenIncentive::class);
    }

    public function pricingSpecials(): HasMany
    {
        return $this->hasMany(PricingSpecial::class);
    }

    public function medias(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class)->orderBy('order');
    }

    public function bannerDesktopMedia(): belongsTo
    {
        return $this->belongsTo(Media::class, 'banner_desktop_media_id');
    }

    public function bannerMobileMedia(): belongsTo
    {
        return $this->belongsTo(Media::class, 'banner_mobile_media_id');
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function redirects(): HasMany
    {
        return $this->hasMany(Redirect::class);
    }

    public function dealerIps(): HasMany
    {
        return $this->hasMany(DealerIp::class);
    }

    public function digitalRetailSettings(): HasOne
    {
        return $this->hasOne(DigitalRetailSetting::class);
    }

    // ── Scoped Queries (not relations) ────────────────────────────────

    public function roles(): Builder
    {
        return Role::where('dealer_id', $this->id);
    }

    public function permissions(): Builder
    {
        return Permission::where('dealer_id', $this->id);
    }
}