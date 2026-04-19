<?php

namespace App\Http\Controllers\Dealer;

use App\Actions\Inventory\BulkUpdateInterestRatesAction;
use App\Actions\Inventory\CloneInterestRateAction;
use App\Actions\Inventory\StoreInterestRateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\BulkUpdateInterestRatesRequest;
use App\Http\Requests\Inventory\StoreInterestRateRequest;
use App\Actions\Inventory\SyncInterestRatesAction;
use App\Http\Requests\Inventory\SyncInterestRatesRequest;
use App\Models\Catalog\Make;
use App\Models\Inventory\DealerInterestRate;
use App\Models\Inventory\DealerInventoryFee;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Actions\Inventory\ReorderInventoryFeesAction;
use App\Actions\Inventory\StoreInventoryFeeAction;
use App\Actions\Inventory\UpdateInventoryFeeAction;
use App\Http\Requests\Inventory\ReorderInventoryFeesRequest;
use App\Http\Requests\Inventory\StoreInventoryFeeRequest;
use App\Http\Requests\Inventory\UpdateInventoryFeeRequest;

class InventorySettingController extends Controller
{
    public function __construct(
        private readonly StoreInterestRateAction       $storeInterestRate,
        private readonly BulkUpdateInterestRatesAction $bulkUpdateInterestRates,
        private readonly CloneInterestRateAction       $cloneInterestRate,
        private readonly SyncInterestRatesAction       $syncInterestRates,
        private readonly StoreInventoryFeeAction       $storeInventoryFee,
        private readonly UpdateInventoryFeeAction      $updateInventoryFee,
        private readonly ReorderInventoryFeesAction    $reorderInventoryFees,
    ) {}

    // ── Pages ─────────────────────────────────────────────────────────

    public function rates(Request $request): View
    {
        $dealer = $request->user()->currentDealer;
        $makes  = Make::orderBy('name')->get(['id', 'name']);

        $grouped = $dealer->interestRates()
            ->orderByDesc('max_model_year')
            ->orderByDesc('min_model_year')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->groupBy(fn(DealerInterestRate $r) => $r->year_range_key);

        return view('dealer.pages.inventory.settings.rates', compact('grouped', 'makes'));
    }

    // ── Fees Page ─────────────────────────────────────────────────────

    public function fees(Request $request): View
    {
        $dealer = $request->user()->currentDealer;

        $fees = $dealer->inventoryFees()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('dealer.pages.inventory.settings.fees', compact('fees'));
    }

    // ── Fees CRUD ─────────────────────────────────────────────────────

    public function storeFee(StoreInventoryFeeRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $fee    = ($this->storeInventoryFee)($dealer, $request->validated());

        AuditLogger::info($request, 'Inventory fee created', ['fee_id' => $fee->id]);

        return response()->json([
            'success'  => true,
            'fee_id'   => $fee->id,
            'row_html' => view('dealer.components.inventory.settings.fee-row',
                            compact('fee', 'dealer'))->render(),
        ]);
    }

    public function updateFee(UpdateInventoryFeeRequest $request, DealerInventoryFee $fee): JsonResponse
    {
        $this->authorizeFee($request, $fee);

        $fee = ($this->updateInventoryFee)($fee, $request->validated());

        AuditLogger::info($request, 'Inventory fee updated', ['fee_id' => $fee->id]);

        return response()->json([
            'success'  => true,
            'row_html' => view('dealer.components.inventory.settings.fee-row',
                            ['fee' => $fee, 'dealer' => $request->user()->currentDealer])->render(),
        ]);
    }

    public function destroyFee(Request $request, DealerInventoryFee $fee): JsonResponse
    {
        $this->authorizeFee($request, $fee);

        $feeId = $fee->id;
        $fee->delete();

        AuditLogger::info($request, 'Inventory fee deleted', ['fee_id' => $feeId]);

        return response()->json(['success' => true]);
    }

    public function reorderFees(ReorderInventoryFeesRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;

        ($this->reorderInventoryFees)($dealer, $request->validated('ids'));

        AuditLogger::info($request, 'Inventory fees reordered');

        return response()->json(['success' => true]);
    }

    public function syndication(): View
    {
        return view('dealer.pages.inventory.settings.syndication');
    }

    // ── Interest Rate CRUD ────────────────────────────────────────────

    public function storeRate(StoreInterestRateRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;
        $rate   = ($this->storeInterestRate)($dealer, $request->validated());
        $makes  = Make::orderBy('name')->get(['id', 'name']);

        AuditLogger::info($request, 'Interest rate created', ['rate_id' => $rate->id]);

        return response()->json([
            'success'        => true,
            'rate_id'        => $rate->id,
            'year_range_key' => $rate->year_range_key,
            'row_html'       => view('dealer.components.inventory.settings.interest-rate-row', compact('rate', 'makes'))->render(),
        ]);
    }

    public function bulkUpdateRates(BulkUpdateInterestRatesRequest $request): JsonResponse
    {
        $dealer = $request->user()->currentDealer;

        ($this->bulkUpdateInterestRates)($dealer, $request->validated('rates'));

        AuditLogger::info($request, 'Interest rates bulk updated', [
            'count' => count($request->validated('rates')),
        ]);

        return response()->json(['success' => true]);
    }

    public function cloneRate(Request $request, DealerInterestRate $rate): JsonResponse
    {
        $this->authorizeRate($request, $rate);

        $clone = ($this->cloneInterestRate)($rate);
        $makes = Make::orderBy('name')->get(['id', 'name']);

        AuditLogger::info($request, 'Interest rate cloned', [
            'original_id' => $rate->id,
            'clone_id'    => $clone->id,
        ]);

        return response()->json([
            'success'        => true,
            'rate_id'        => $clone->id,
            'year_range_key' => $clone->year_range_key,
            'row_html'       => view('dealer.components.inventory.settings.interest-rate-row', ['rate' => $clone, 'makes' => $makes])->render(),
        ]);
    }

    public function syncRates(SyncInterestRatesRequest $request): JsonResponse
    {
        $dealer   = $request->user()->currentDealer;
        $creates  = $request->validated('creates');
        $updates  = $request->validated('updates');
        $deletes  = $request->validated('deletes');

        ($this->syncInterestRates)($dealer, $creates, $updates, $deletes);

        AuditLogger::info($request, 'Interest rates synced', [
            'created' => count($creates),
            'updated' => count($updates),
            'deleted' => count($deletes),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroyRate(Request $request, DealerInterestRate $rate): JsonResponse
    {
        $this->authorizeRate($request, $rate);

        $rateId = $rate->id;
        $rate->delete();

        AuditLogger::info($request, 'Interest rate deleted', ['rate_id' => $rateId]);

        return response()->json(['success' => true]);
    }

    // ── Private Helpers ───────────────────────────────────────────────

    private function authorizeRate(Request $request, DealerInterestRate $rate): void
    {
        abort_if($rate->dealer_id !== $request->user()->current_dealer_id, 403);
    }

    private function authorizeFee(Request $request, DealerInventoryFee $fee): void
    {
        abort_if($fee->dealer_id !== $request->user()->current_dealer_id, 403);
    }
}