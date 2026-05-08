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
        // ... more mappings as they are implemented
    ];

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
        $request->validate([
            'provider'        => 'required|string',
            'settings'        => 'required|array',
        ]);

        $integration = DealerIntegration::updateOrCreate(
            ['dealer_id' => $dealer->id, 'provider' => $request->provider],
            [
                'settings'  => $request->settings,
                'is_active' => filter_var($request->input('is_active', false), FILTER_VALIDATE_BOOLEAN),
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
