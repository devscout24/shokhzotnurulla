<?php

namespace App\Services\Integrations;

use App\Models\Dealership\Dealer;
use App\Models\Dealership\DealerIntegration;

class IntegrationManager
{
    /**
     * Get an operational integration for a dealer.
     * Returns null if not configured or not approved.
     */
    public static function for(Dealer $dealer, string $provider): ?DealerIntegration
    {
        return $dealer->integrations()
            ->where('provider', $provider)
            ->operational()
            ->first();
    }

    /**
     * Resolve the provider service class for API-type integrations.
     * Returns null if the provider has no service class or is not operational.
     */
    public static function resolve(Dealer $dealer, string $provider): ?IntegrationProvider
    {
        $integration = self::for($dealer, $provider);
        if (! $integration) {
            return null;
        }

        $config = config("integrations.providers.{$provider}");
        $class  = $config['provider'] ?? null;

        if (! $class || ! class_exists($class)) {
            return null;
        }

        return new $class($integration);
    }

    /**
     * Get all operational integrations for a dealer, keyed by provider slug.
     */
    public static function allOperational(Dealer $dealer): array
    {
        return $dealer->integrations()
            ->operational()
            ->get()
            ->keyBy('provider')
            ->toArray();
    }
}
