<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class DevLog
{
    /**
     * Debug — only writes when APP_DEBUG=true or env is local.
     * Use this for tracing/development noise that should never reach production logs.
     */
    public static function debug(string $message, array $context = []): void
    {
        if (config('app.debug')) {
            Log::debug($message, $context);
        }
    }

    /**
     * Info — same guard as debug.
     */
    public static function info(string $message, array $context = []): void
    {
        if (config('app.debug')) {
            Log::info($message, $context);
        }
    }

    /**
     * Error — always writes regardless of environment.
     * Use this for actual failures you want to catch in production.
     */
    public static function error(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * Warning — always writes.
     */
    public static function warning(string $message, array $context = []): void
    {
        Log::warning($message, $context);
    }

    /**
     * Log to a specific channel with the same debug guard.
     */
    public static function channel(string $channel, string $message, array $context = []): void
    {
        if (config('app.debug')) {
            Log::channel($channel)->debug($message, $context);
        }
    }
}
