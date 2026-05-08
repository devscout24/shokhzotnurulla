<?php

namespace App\Enums;

enum IntegrationStatus: string
{
    case DRAFT            = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED         = 'approved';
    case REJECTED         = 'rejected';
    case REVOKED          = 'revoked';

    public function label(): string
    {
        return match($this) {
            self::DRAFT            => 'Draft',
            self::PENDING_APPROVAL => 'Pending Approval',
            self::APPROVED         => 'Approved',
            self::REJECTED         => 'Rejected',
            self::REVOKED          => 'Revoked',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::DRAFT            => '#6c757d',
            self::PENDING_APPROVAL => '#f0ad4e',
            self::APPROVED         => '#1e8e3e',
            self::REJECTED         => '#d93025',
            self::REVOKED          => '#c0392b',
        };
    }

    public function badgeBgColor(): string
    {
        return match($this) {
            self::DRAFT            => '#f1f3f4',
            self::PENDING_APPROVAL => '#fef7e0',
            self::APPROVED         => '#e6f4ea',
            self::REJECTED         => '#fce8e6',
            self::REVOKED          => '#fce8e6',
        };
    }

    /** Only approved integrations should be injected/used. */
    public function isOperational(): bool
    {
        return $this === self::APPROVED;
    }
}
