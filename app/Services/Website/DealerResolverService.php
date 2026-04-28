<?php

namespace App\Services\Website;

use App\Models\Dealership\Dealer;
use App\Models\Website\Domain;
use Illuminate\Support\Facades\Cache;

class DealerResolverService
{
    /** Per-request memoization — eliminates duplicate cache queries. */
    private static ?int $resolved = null;

    public function resolve(): int
    {
        if (self::$resolved !== null) {
            return self::$resolved;
        }

        $domain = strtolower(request()->getHost());

        self::$resolved = Cache::remember(
            "dealer_id_by_domain:{$domain}",
            now()->addHour(),
            function () use ($domain) {
                $domainRecord = Domain::where('domain', $domain)->first(['dealer_id']);
                if ($domainRecord) {
                    return $domainRecord->dealer_id;
                }

                $dealer = Dealer::where('domain', $domain)
                    ->orWhere('staging_domain', $domain)
                    ->first(['id']);

                if ($dealer) {
                    return $dealer->id;
                }

                // Fallback for local development
                if (app()->isLocal() || config('app.debug') || str_ends_with($domain, '.test') || str_ends_with($domain, '.team') || in_array($domain, ['localhost', '127.0.0.1', '::1'])) {
                    // If an admin is logged in, prioritize their current dealer
                    if (auth()->check() && auth()->user()->current_dealer_id) {
                        return auth()->user()->current_dealer_id;
                    }

                    $firstDealer = Dealer::first(['id']);
                    if ($firstDealer) {
                        return $firstDealer->id;
                    }
                }

                throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Dealer not found for domain: {$domain}");
            }
        );

        return self::$resolved;
    }
}