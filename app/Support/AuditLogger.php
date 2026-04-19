<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    // Panel Detection
    public static function resolveChannel(Request $request): string
    {
        return match(true) {
            str_contains($request->url(), '/admin')  => 'audit-admin',
            str_contains($request->url(), '/dealer') => 'audit-dealer',
            default                                  => 'audit-frontend',
        };
    }

    // Core Log Methods
    public static function warning(Request $request, string $message, array $context = []): void
    {
        $channel = static::resolveChannel($request);

        Log::channel($channel)->warning($message, static::buildContext($request, $context));
    }

    public static function info(Request $request, string $message, array $context = []): void
    {
        $channel = static::resolveChannel($request);

        Log::channel($channel)->info($message, static::buildContext($request, $context));
    }

    public static function error(Request $request, string $message, array $context = []): void
    {
        $channel = static::resolveChannel($request);

        Log::channel($channel)->error($message, static::buildContext($request, $context));
    }

    // Channel Specific — jab manually channel chahiye
    public static function toChannel(string $channel, string $level, string $message, array $context = []): void
    {
        Log::channel($channel)->$level($message, $context);
    }

    // Context Builder
    private static function buildContext(Request $request, array $extra = []): array
    {
        return array_merge([
            'url'    => $request->url(),
            'method' => $request->method(),
            'ip'     => $request->ip(),
            'at'     => now(),
        ], $extra);
    }
}