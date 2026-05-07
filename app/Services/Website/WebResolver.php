<?php

namespace App\Services\Website;

use App\Models\Dealership\Dealer;
use App\Enums\DealerStatus;
use Illuminate\Support\Facades\Cache;

class WebResolver
{
    /**
     * Memoized resolved dealer to avoid redundant queries during a single request.
     */
    private static ?Dealer $resolvedDealer = null;

    /**
     * Resolve the dealer based on the current request domain.
     * 
     * @return Dealer|null
     */
    public function resolve(): ?Dealer
    {
        if (self::$resolvedDealer !== null) {
            return self::$resolvedDealer;
        }

        $domain = strtolower(request()->getHost());

        self::$resolvedDealer = Cache::remember(
            "resolved_dealer_by_domain:{$domain}",
            now()->addHour(),
            function () use ($domain) {
                // Find dealer by domain and ensure they are active
                return Dealer::where('domain', $domain)
                    ->where('status', DealerStatus::ACTIVE)
                    ->first();
            }
        );

        return self::$resolvedDealer;
    }

    /**
     * Clear the cache for a specific domain.
     */
    public static function clearCache(string $domain): void
    {
        Cache::forget("resolved_dealer_by_domain:" . strtolower($domain));
    }
}
