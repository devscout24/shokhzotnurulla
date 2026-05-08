<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealership\DealerIntegration;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function apps()
    {
        $dealer = request()->user()->currentDealer;
        $integrations = $dealer ? $dealer->integrations->keyBy('provider') : collect();

        return view('dealer.pages.connections.apps', compact('integrations'));
    }

    /**
     * Save integration credentials from the dealer-side modals.
     */
    public function saveIntegration(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'settings' => 'required|array',
        ]);

        $dealer = $request->user()->currentDealer;

        if (! $dealer) {
            return response()->json(['success' => false, 'message' => 'No dealer found.'], 403);
        }

        $integration = DealerIntegration::updateOrCreate(
            ['dealer_id' => $dealer->id, 'provider' => $request->provider],
            [
                'settings'  => $request->settings,
                'is_active' => filter_var($request->input('is_active', true), FILTER_VALIDATE_BOOLEAN),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->provider) . ' settings saved successfully.',
        ]);
    }

    public function links()
    {
        return view('dealer.pages.connections.links');
    }
}
