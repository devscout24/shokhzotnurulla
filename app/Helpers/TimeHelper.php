<?php

namespace App\Helpers;

use App\Models\Dealership\Dealer;
use DateTime;

class TimeHelper
{
    /**
     * Convert frontend time string (e.g., "9:00 AM") to database time format (H:i:s).
     */
    public static function generateNumericId(): string
    {
        do {
            // Get current timestamp (e.g., 1716567890)
            $timestamp = time();

            // Take first 7 digits (changes every ~16 minutes)
            $prefix = substr($timestamp, 0, 7);

            // Append 3 random digits (000-999)
            $suffix = str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);

            $candidate = $prefix.$suffix;

        } while (Dealer::query()->where('internal_id', $candidate)->exists()); // Check collision

        return $candidate;
    }

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

    public static function strip_tags($text) {
        if(empty($text)) {
            return '';
        }
        // Remove script and style blocks (including their content)
        $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text);
        $text = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $text);

        // Strip all remaining HTML tags
        $text = strip_tags($text);

        // Decode HTML entities (e.g. &amp; → &, &nbsp; → space)
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Clean up excess whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        return $text;
    }
}
