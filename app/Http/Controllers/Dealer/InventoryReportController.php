<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Inventory\Vehicle;
use App\Models\Catalog\Make;
use App\Models\Website\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Display the inventory reports page.
     */
    public function index(Request $request): View
    {
        $dealerId = $request->user()->current_dealer_id;
        
        $query = Vehicle::with([
                'make',
                'makeModel',
                'primaryPhoto',
                'prices',
                'specs'
            ])
            ->forDealer($dealerId);

        // ── Filters ───────────────────────────────────────────────────────────

        // Time frame (inventory_date or created_at fallback)
        if ($request->filled(['from', 'to'])) {
            $from = $request->from;
            $to = $request->to;
            $query->where(function($q) use ($from, $to) {
                $q->whereBetween('inventory_date', [$from, $to])
                  ->orWhere(function($sq) use ($from, $to) {
                      $sq->whereNull('inventory_date')
                         ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                  });
            });
        }

        // Make
        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }

        // Model
        if ($request->filled('model_id')) {
            $query->where('make_model_id', $request->model_id);
        }

        // Search by stock or vin
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('stock_number', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        // Location
        if ($request->filled('location_id')) {
            // If vehicles table had location_id, we would filter here.
            // For now, it's a placeholder as per user request.
        }
        
        $vehicles = $query->orderByDesc('inventory_date')->get();

        // ── Calculations ──────────────────────────────────────────────────────
        
        $totalQuantity     = $vehicles->count();
        $totalInvestment   = 0;
        $totalSales        = 0;
        $totalDaysOnMarket = 0;

        // Sold-only accumulators (used for gross profit / margin / avg gross profit)
        $soldCostTotal   = 0;
        $soldCount       = 0;
        $grossProfit     = 0;

        foreach ($vehicles as $vehicle) {
            $cost      = (float) ($vehicle->prices->dealer_cost ?? 0);
            $soldPrice = (float) ($vehicle->prices->sold_price  ?? 0);
            $isSold    = ($soldPrice > 0);

            // Total investment = all vehicles' cost
            $totalInvestment += $cost;
            $totalSales      += $soldPrice;

            // Days on market: inventory_date → sold_date (or now if still active)
            $startDate = $vehicle->inventory_date ?? $vehicle->created_at;
            $endDate   = $vehicle->prices->sold_date ?? now();
            $days      = $startDate ? (int) $startDate->diffInDays($endDate) : 0;

            $vehicle->report_days = $days;
            $totalDaysOnMarket   += $days;

            // Per-vehicle profit & margin — only for sold vehicles
            if ($isSold) {
                $profit = $soldPrice - $cost;
                $vehicle->report_profit = $profit;
                $vehicle->report_margin = ($soldPrice > 0) ? ($profit / $soldPrice) * 100 : 0;

                $grossProfit   += $profit;
                $soldPriceTotal = ($soldPriceTotal ?? 0) + $soldPrice;
                $soldCount++;
            } else {
                $vehicle->report_profit = null;
                $vehicle->report_margin = null;
            }
        }

        $avgDaysOnMarket = $totalQuantity > 0 ? $totalDaysOnMarket / $totalQuantity : 0;

        // Gross margin and avg gross profit are based on SOLD vehicles only
        // Margin = profit / sold revenue (standard gross margin formula)
        $soldPriceTotal = $soldPriceTotal ?? 0;
        $grossMargin    = $soldPriceTotal > 0 ? ($grossProfit / $soldPriceTotal) * 100 : 0;
        $avgGrossProfit = $soldCount      > 0 ? $grossProfit / $soldCount            : 0;

        // ── Dropdowns for filters ─────────────────────────────────────────────
        $makes = Make::whereHas('vehicles', fn ($q) => $q->where('dealer_id', $dealerId))
            ->orderBy('name')
            ->get(['id', 'name']);
            
        $locations = Location::where('dealer_id', $dealerId)->get();

        return view('dealer.pages.inventory.reports', compact(
            'vehicles', 'makes', 'locations',
            'totalQuantity', 'totalInvestment', 'totalSales', 
            'grossProfit', 'grossMargin', 'avgGrossProfit', 'avgDaysOnMarket'
        ));
    }

    /**
     * Export the report data to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $filename = 'inventory-report-' . now()->format('Y-m-d') . '.csv';

        $query = Vehicle::with([
                'make', 'makeModel', 'bodyType', 'exteriorColor', 'drivetrainType', 'prices', 'specs', 'dealer'
            ])
            ->forDealer($dealerId);

        // Apply same filters as index
        if ($request->filled(['from', 'to'])) {
            $from = $request->from;
            $to = $request->to;
            $query->where(function($q) use ($from, $to) {
                $q->whereBetween('inventory_date', [$from, $to])
                  ->orWhere(function($sq) use ($from, $to) {
                      $sq->whereNull('inventory_date')
                         ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                  });
            });
        }
        if ($request->filled('make_id')) {
            $query->where('make_id', $request->make_id);
        }
        if ($request->filled('model_id')) {
            $query->where('make_model_id', $request->model_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('stock_number', 'like', "%{$search}%")
                  ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->get();

        return response()->streamDownload(function () use ($vehicles) {
            $handle = fopen('php://output', 'w');

            // Header based on user request:
            // id, dealer_id, status, year, make, model, modelnumber, trim, series, body, vin, stocknumber, 
            // condition, exteriorcolorstandard, drivetrainstandard, created, city, state, views, pct, title, url
            fputcsv($handle, [
                'id', 'dealer_id', 'status', 'year', 'make', 'model', 'modelnumber', 'trim', 'series', 'body', 
                'vin', 'stocknumber', 'condition', 'exteriorcolorstandard', 'drivetrainstandard', 
                'created', 'city', 'state', 'views', 'pct', 'title', 'url'
            ]);

            foreach ($vehicles as $v) {
                fputcsv($handle, [
                    $v->id,
                    $v->dealer_id,
                    $v->status,
                    $v->year,
                    $v->make?->name,
                    $v->makeModel?->name,
                    $v->model_number,
                    $v->trim,
                    $v->series, 
                    $v->bodyType?->name,
                    $v->vin,
                    $v->stock_number,
                    $v->vehicle_condition,
                    $v->exteriorColor?->name,
                    $v->specs?->drivetrain_standard ?? $v->drivetrainType?->name,
                    $v->created_at->format('Y-m-d H:i:s'),
                    $v->dealer?->city,
                    $v->dealer?->state,
                    $v->views ?? 0,
                    $v->pct ?? 0,
                    $v->display_title,
                    route('dealer.inventory.vdp.show', $v)
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    
    /**
     * Update vehicle price details via AJAX.
     */
    public function updatePrice(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'dealer_cost' => 'nullable|numeric',
            'msrp'        => 'nullable|numeric',
            'sold_price'  => 'nullable|numeric',
        ]);

        $vehicle->prices()->updateOrCreate([], [
            'dealer_cost' => $request->dealer_cost,
            'msrp'        => $request->msrp,
            'sold_price'  => $request->sold_price,
        ]);

        return response()->json(['success' => true]);
    }
}
