<?php

namespace App\Http\Controllers\Dealer;

use App\Actions\Inventory\DeleteIncentiveAction;
use App\Actions\Inventory\StoreIncentiveAction;
use App\Actions\Inventory\UpdateIncentiveAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreIncentiveRequest;
use App\Http\Requests\Inventory\UpdateIncentiveRequest;
use App\Models\Inventory\Incentive;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncentiveController extends Controller
{
    public function __construct(
        private readonly StoreIncentiveAction  $storeIncentive,
        private readonly UpdateIncentiveAction $updateIncentive,
        private readonly DeleteIncentiveAction $deleteIncentive,
    ) {}

    // ─── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;

        $query = Incentive::forDealer($dealerId)->latest();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $incentives = $query->paginate(25)->withQueryString();
        $dealer     = $request->user()->currentDealer;

        return view('dealer.pages.inventory.incentives', compact('incentives', 'dealer'));
    }

    // ─── Store ────────────────────────────────────────────────────────────────

    public function store(StoreIncentiveRequest $request): JsonResponse
    {
        $dealer    = $request->user()->currentDealer;
        $incentive = ($this->storeIncentive)($dealer, $this->prepareBooleans($request->validated()));

        AuditLogger::info($request, 'Incentive created', ['incentive_id' => $incentive->id]);

        return response()->json([
            'success' => true,
            'message' => 'Incentive created successfully.',
        ]);
    }

    // ─── Update ───────────────────────────────────────────────────────────────

    public function update(UpdateIncentiveRequest $request, Incentive $incentive): JsonResponse
    {
        $this->authorizeIncentive($request, $incentive);

        ($this->updateIncentive)($incentive, $this->prepareBooleans($request->validated()));

        AuditLogger::info($request, 'Incentive updated', ['incentive_id' => $incentive->id]);

        return response()->json([
            'success' => true,
            'message' => 'Incentive updated successfully.',
        ]);
    }

    // ─── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Request $request, Incentive $incentive): JsonResponse
    {
        $this->authorizeIncentive($request, $incentive);

        ($this->deleteIncentive)($incentive);

        AuditLogger::warning($request, 'Incentive deleted', ['incentive_id' => $incentive->id]);

        return response()->json([
            'success' => true,
            'message' => 'Incentive deleted successfully.',
        ]);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function authorizeIncentive(Request $request, Incentive $incentive): void
    {
        abort_if($incentive->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function prepareBooleans(array $data): array
    {
        $data['is_guaranteed'] = (bool) ($data['is_guaranteed'] ?? false);
        $data['is_featured']   = (bool) ($data['is_featured']   ?? false);
        $data['is_enabled']    = (bool) ($data['is_enabled']    ?? false);

        return $data;
    }
}