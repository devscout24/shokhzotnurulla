<?php

namespace App\Providers;

use App\Models\Dealership\Dealer;
use App\Models\Website\Domain;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $host = request()->getHost();

        // 1. Identify Dealer by domain
        $dealer = $this->resolveDealer($host);

        // 2. Store in service container for easy access (e.g., app('currentDealer'))
        if ($dealer) {
            $this->app->instance('currentDealer', $dealer);
        }
    }

    /**
     * Resolve dealer from the database based on the request domain.
     */
    private function resolveDealer(string $host): ?Dealer
    {
        // First, check the domains table (many domains per dealer)
        $domainRecord = Domain::where('domain', $host)->first();

        if ($domainRecord) {
            return $domainRecord->dealer;
        }

        // Fallback: Check the primary domain field on the dealers table
        return Dealer::where('domain', $host)
            ->orWhere('staging_domain', $host)
            ->first();
    }
}
