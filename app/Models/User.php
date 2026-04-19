<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Dealership\Dealer;
use App\Models\Role;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'timezone',
        'password',
        'is_active',
        'is_system_user',
        'current_dealer_id',
        'is_2fa_required',
        'password_complexity',
        'password_reuse_policy',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_system_user' => 'boolean',
            'is_2fa_required' => 'boolean',
            'password_complexity' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Many-to-Many relationship with Dealer
     */
    public function dealers(): BelongsToMany
    {
        return $this->belongsToMany(Dealer::class)
                    ->withPivot('is_owner')
                    ->withTimestamps();
    }

    /**
     * Current dealer convenience relation
     */

    public function currentDealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'current_dealer_id');
    }

    /**
     * User password histories relation
     */

    public function passwordHistories(): HasMany
    {
        return $this->hasMany(PasswordHistory::class);
    }

    // Overriding Email (password reset) method
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    // Overriding Email Verification method
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }

    // Panel Type Helpers
    private function ensureCoreRelationsLoaded(): void
    {
        $this->loadMissing([
            'roles',
            'currentDealer'
        ]);
    }

    // Configuration Validation
    public function hasValidDealer(): bool
    {
        $this->ensureCoreRelationsLoaded();
        return $this->currentDealer !== null;
    }

    public function isSystemUser(): bool
    {
        // Layer 0 - Dealer must exist first
        if (!$this->hasValidDealer()) {
            return false;
        }

        // Layer 1  - system flag
        if (!$this->is_system_user) {
            return false;
        }

        // Layer 2 - system dealer slug
        if ($this->currentDealer->slug !== config('systemuser.dealer_slug')) {
            return false;
        }

        // Layer 3 - role must belong to system team
        // role must belong to system team dynamically
        $teamColumn = config('permission.column_names.team_foreign_key', 'dealer_id');
        $systemTeamId = Role::where('name', 'super_admin')
            ->value($teamColumn);
        $currentUserTeamId = getPermissionsTeamId();

        if (!$systemTeamId || !$currentUserTeamId || $systemTeamId !== $currentUserTeamId) {
            return false;
        }

        if ($this->roles->where($teamColumn, $systemTeamId)->isEmpty()) {
            return false;
        }

        return true;
    }

    public function isDealerUser(): bool
    {
        // Layer 0 - Dealer must exist first
        if (!$this->hasValidDealer()) {
            return false;
        }

        // Layer 1 - must not be a system user
        if ($this->is_system_user) {
            return false;
        }

        // Layer 2 - not have system dealer slug
        if ($this->currentDealer->slug == config('systemuser.dealer_slug')) {
            return false;
        }

        // Layer 3 - role must belong to non system team
        $teamColumn = config('permission.column_names.team_foreign_key', 'dealer_id');
        $ownerDealerTeamID = $this->current_dealer_id;
        $currentUserTeamId = getPermissionsTeamId();

        if (!$ownerDealerTeamID || !$currentUserTeamId || $ownerDealerTeamID !== $currentUserTeamId) {
            return false;
        }

        if ($this->roles->where($teamColumn, $ownerDealerTeamID)->isEmpty()) {
            return false;
        }

        return true;
    }

    public function dealerIsActive(): bool
    {
        return $this->hasValidDealer() && $this->currentDealer?->is_active;
    }

    public function hasInvalidRoleState(): bool
    {
        $this->ensureCoreRelationsLoaded();

        // User must have at least one role
        if ($this->roles->isEmpty()) {
            return true;
        }

        // Any inactive role assigned?
        return $this->roles->contains(fn ($role) => !$role->is_active);
    }

    // Panel Integrity Check
    public function hasValidPanelConfiguration(): bool
    {
        // User must be either system user or dealer user
        if ($this->isSystemUser() || $this->isDealerUser()) {
            return true;
        }

        // Otherwise, invalid configuration
        return false;
    }

    // Master Login Validation
    public function validateUserLogin(): ?string
    {
        if (!$this->is_active) {
            return 'Your account is inactive!';
        }

        if (!$this->hasValidDealer()) {
            return 'Your assigned dealer is missing!';
        }

        if (!$this->dealerIsActive()) {
            return 'Your dealer is inactive!';
        }

        if ($this->hasInvalidRoleState()) {
            return 'Your assigned role is invalid or inactiveee!';
        }

        if (!$this->hasValidPanelConfiguration()) {
            return 'Invalid system configuration!';
        }

        return null;
    }
}
