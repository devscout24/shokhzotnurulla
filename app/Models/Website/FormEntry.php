<?php

namespace App\Models\Website;

use App\Models\Dealership\Dealer;
use App\Models\Inventory\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormEntry extends Model
{
    protected $fillable = [
        'dealer_id',
        'form_type',
        'borrower_type',
        'status',
        'is_read',
        'read_at',
        'vehicle_id',
        'referrer',
        'first_name',
        'last_name',
        'email',
        'phone',
        'data',
        'nps_rating',
        'visitor_data',
        'submitted_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'visitor_data' => 'array',
        'is_read'      => 'boolean',
        'read_at'      => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // ── Constants ─────────────────────────────────────────────────────────

    public const FORM_TYPES = [
        'trade_in'             => 'Trade-in',
        'get_approved'         => 'Get Approved',
        'unlock_calculator'    => 'Unlock Calculator',
        'managers_special'     => "Unlock Manager's Special",
        'ask_question'         => 'Ask a Question',
        'schedule_test_drive'  => 'Schedule a Test Drive',
        'contact_us'           => 'Contact Us',
        'unlock_eprice'       => 'Unlock e-Price',
    ];

    public const BORROWER_TYPES = [
        'single' => 'Single Borrower',
        'joint'  => 'Joint Borrower',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FormEntryPhoto::class)->orderBy('sort_order');
    }

    // ── Accessors ──────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFormTypeLabelAttribute(): string
    {
        return self::FORM_TYPES[$this->form_type] ?? $this->form_type;
    }

    public function getBorrowerTypeLabelAttribute(): ?string
    {
        if (!$this->borrower_type) {
            return null;
        }

        return self::BORROWER_TYPES[$this->borrower_type] ?? $this->borrower_type;
    }

    public function getIsCompleteAttribute(): bool
    {
        return $this->status === 'complete';
    }

    public function getIsAbandonedAttribute(): bool
    {
        return $this->status === 'abandoned';
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopeForDealer($query, int $dealerId): void
    {
        $query->where('dealer_id', $dealerId);
    }

    public function scopeUnread($query): void
    {
        $query->where('is_read', false);
    }

    public function scopeCompleted($query): void
    {
        $query->where('status', 'complete');
    }

    public function scopeAbandoned($query): void
    {
        $query->where('status', 'abandoned');
    }

    public function scopeRead($query): void
    {
        $query->where('is_read', true);
    }

    public function scopeOfType($query, string $formType): void
    {
        $query->where('form_type', $formType);
    }

    public function scopeSearchByName($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"]);
        });
    }

    public function scopeDateRange($query, string $from, string $to): void
    {
        $query->whereBetween('submitted_at', [
            $from . ' 00:00:00',
            $to   . ' 23:59:59',
        ]);
    }

    // ── Methods ────────────────────────────────────────────────────────────

    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null,
        ]);
    }

    public function markAsComplete(): void
    {
        $this->update(['status' => 'complete']);
    }
}