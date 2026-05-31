<?php
namespace App\Http\Controllers\Dealer;

use App\Enums\IntegrationStatus;
use App\Events\IntegrationSubmitted;
use App\Http\Controllers\Controller;
use App\Models\Dealership\DealerIntegration;
use Illuminate\Http\Request;

class ConnectionController extends Controller
{
    public function apps()
    {
        $dealer       = request()->user()->currentDealer;
        $integrations = $dealer ? $dealer->integrations->keyBy('provider') : collect();

        return view('dealer.pages.connections.apps', compact('integrations'));
    }

    /**
     * Save integration credentials from the dealer-side modals.
     * Sets status to pending_approval — Super Admin must approve before activation.
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
                'settings'         => $request->settings,
                'is_active'        => false, // NOT active until admin approves
                'status'           => IntegrationStatus::PENDING_APPROVAL,
                'submitted_by'     => $request->user()->id,
                'submitted_at'     => now(),
                'rejection_reason' => null, // Clear any previous rejection
            ]
        );

        // Notify Super Admin(s) that a new integration request is pending
        event(new IntegrationSubmitted($integration));

        return response()->json([
            'success' => true,
            'message' => ucfirst($request->provider) . ' settings submitted for approval.',
        ]);
    }

    public function links()
    {
        return view('dealer.pages.connections.links');
    }
}
