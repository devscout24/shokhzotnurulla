<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Models\Dealership\DealerIntegration;
use Exception;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    /**
     * Map providers to their class implementations.
     */
    protected array $providers = [
        'carfax'    => \App\Services\Integrations\CarfaxProvider::class,
        '700credit' => \App\Services\Integrations\Credit700Provider::class,
        'stripe'    => \App\Services\Integrations\StripeProvider::class,
        'ga4'       => \App\Services\Integrations\Ga4Provider::class,
        'gtm'       => \App\Services\Integrations\GtmProvider::class,
        // ... more mappings as they are implemented
    ];

    /**
     * Get dynamic validation rules based on the integration provider.
     */
    protected function getValidationRules(string $provider): array
    {
        $baseRules = [
            'provider' => 'required|string',
            'settings' => 'required|array',
        ];

        // Specific validation rules for each app's credentials
        $settingRules = match ($provider) {
            'carfax' => [
                'settings.username' => 'required|string',
                'settings.password' => 'required|string',
            ],
            '700credit' => [
                'settings.api_key'     => 'required|string',
                'settings.dealer_code' => 'required|string',
            ],
            'ga4' => [
                'settings.measurement_id' => ['required', 'string', 'regex:/^G-[A-Z0-9]+$/'],
            ],
            'gtm' => [
                'settings.container_id' => ['required', 'string', 'regex:/^GTM-[A-Z0-9]+$/'],
            ],
            'stripe' => [
                'settings.public_key' => 'required|string|starts_with:pk_',
                'settings.secret_key' => 'required|string|starts_with:sk_',
            ],
            'carnow' => [
                'settings.dealer_id' => 'required|string',
            ],
            'complyauto' => [
                'settings.api_token' => 'required|string',
            ],
            'ipacket' => [
                'settings.api_key' => 'required|string',
            ],
            'promax' => [
                'settings.dealer_id' => 'required|string',
                'settings.password'  => 'required|string',
            ],
            // Default for services that might just require a toggle or simpler settings
            default => [],
        };

        return array_merge($baseRules, $settingRules);
    }

    public function index(Dealer $dealer)
    {
        $dealer->load('integrations');
        return view('admin.pages.dealers.integrations', compact('dealer'));
    }

    /**
     * Save integration settings and optionally test connection.
     */
    public function save(Request $request, Dealer $dealer)
    {
        $request->validate($this->getValidationRules($request->input('provider')));

        $integration = DealerIntegration::updateOrCreate(
            ['dealer_id' => $dealer->id, 'provider' => $request->provider],
            [
                'settings'  => $request->settings,
                'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
            ]
        );

        if (filter_var($request->input('test_connection', false), FILTER_VALIDATE_BOOLEAN)) {
            return $this->test($integration);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Settings saved successfully.',
            'integration' => $integration,
        ]);
    }

    /**
     * Remove/unconfigure an integration for a dealer by setting it inactive.
     */
    public function destroy(Dealer $dealer, string $provider)
    {
        $integration = DealerIntegration::where('dealer_id', $dealer->id)
            ->where('provider', $provider)
            ->first();

        if ($integration) {
            $integration->update(['is_active' => false]);
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($provider) . ' integration made inactive successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Integration not found.',
        ], 404);
    }

    /**
     * Test the connection for an integration.
     */
    public function test(DealerIntegration $integration)
    {
        $providerClass = $this->providers[$integration->provider] ?? null;

        if (! $providerClass) {
            return response()->json([
                'success' => false,
                'message' => "No provider implementation found for {$integration->provider}.",
            ], 422);
        }

        try {
            $provider = new $providerClass($integration);
            $provider->testConnection();

            $integration->update(['last_connected_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => "Connection to {$integration->provider} successful!",
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
