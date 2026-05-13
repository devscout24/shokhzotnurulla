<?php

namespace App\Models\Dealership;

use App\Enums\IntegrationStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealerIntegration extends Model
{
    protected $fillable = [
        'dealer_id',
        'provider',
        'settings',
        'is_active',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'submitted_by',
        'submitted_at',
        'last_connected_at',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'settings'          => 'encrypted:json', // Encrypted at rest, handled as array in PHP
        'status'            => IntegrationStatus::class,
        'approved_at'       => 'datetime',
        'submitted_at'      => 'datetime',
        'last_connected_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────────

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    /**
     * Only integrations that are both active AND approved.
     */
    public function scopeOperational($query)
    {
        return $query->where('is_active', true)
                     ->where('status', IntegrationStatus::APPROVED);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', IntegrationStatus::PENDING_APPROVAL);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Check if this integration is ready for production use.
     */
    public function isOperational(): bool
    {
        return $this->is_active && $this->status->isOperational();
    }

    /**
     * Safely retrieve a single setting value.
     */
    public function getSetting(string $key, $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }
}
