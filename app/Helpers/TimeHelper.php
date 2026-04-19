<?php

namespace App\Helpers;

use DateTime;

class TimeHelper
{
    /**
     * Convert frontend time string (e.g., "9:00 AM") to database time format (H:i:s).
     */
    public static function toDatabase(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        $date = DateTime::createFromFormat('g:i A', $time);
        return $date ? $date->format('H:i:s') : null;
    }

    /**
     * Convert database time (H:i:s) to frontend display format (e.g., "9:00 AM").
     */
    public static function fromDatabase(?string $time): ?string
    {
        if (empty($time)) {
            return null;
        }

        $date = DateTime::createFromFormat('H:i:s', $time);
        return $date ? $date->format('g:i A') : null;
    }
}