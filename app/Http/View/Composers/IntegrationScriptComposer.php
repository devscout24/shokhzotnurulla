<?php

namespace App\Http\View\Composers;

use App\Models\Dealership\Dealer;
use App\Services\Integrations\ScriptInjector;
use Illuminate\View\View;

class IntegrationScriptComposer
{
    /**
     * Bind tenant-specific integration scripts to frontend views.
     * This ensures Dealer A's GA4 tag is never injected into Dealer B's pages.
     */
    public function compose(View $view): void
    {
        $dealer = $this->resolveDealer();

        if (! $dealer) {
            $view->with('integrationScripts', [
                'ga4_head'      => '',
                'gtm_head'      => '',
                'gtm_body'      => '',
                'carnow_script' => '',
            ]);
            return;
        }

        $injector = new ScriptInjector($dealer);

        $view->with('integrationScripts', [
            'ga4_head'      => $injector->ga4Head(),
            'gtm_head'      => $injector->gtmHead(),
            'gtm_body'      => $injector->gtmBody(),
            'carnow_script' => $injector->carnowScript(),
        ]);
    }

    /**
     * Resolve the current dealer from auth session or request domain.
     */
    private function resolveDealer(): ?Dealer
    {
        // Option 1: Authenticated dealer user
        if (auth()->check() && auth()->user()->currentDealer) {
            return auth()->user()->currentDealer;
        }

        // Option 2: Resolve from request domain (public-facing frontend)
        $host = request()->getHost();

        return Dealer::where('is_active', true)
            ->whereHas('domains', function ($q) use ($host) {
                $q->where('domain', $host);
            })
            ->first();
    }
}
