<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleDailyStat;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WebsiteReportController extends Controller
{
    /**
     * Display the reports landing page.
     */
    public function index()
    {
        return view('dealer.pages.website.reports.index');
    }

    /**
     * Hot Vehicles Report
     */
    public function hotVehicles(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;

        // Date range
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        // Total views for popularity calculation
        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1; // Avoid division by zero

        // Views per vehicle
        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->having('total_views', '>', 0)
            ->orderByDesc('total_views')
            ->get()
            ->keyBy('vehicle_id');

        $vehicleIds = $stats->keys();

        $vehicles = Vehicle::with(['make', 'makeModel'])
            ->whereIn('id', $vehicleIds)
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortByDesc('total_views');

        return view('dealer.pages.website.reports.hot-vehicles', compact('vehicles', 'from', 'to'));
    }

    /**
     * Export Hot Vehicles to CSV
     */
    public function exportHotVehicles(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        // Total views for popularity calculation
        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->having('total_views', '>', 0)
            ->orderByDesc('total_views')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel', 'bodyType', 'exteriorColor', 'drivetrainType', 'dealer', 'specs'])
            ->whereIn('id', $stats->keys())
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortByDesc('total_views');

        $filename = 'hot-vehicles-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($vehicles) {
            $handle = fopen('php://output', 'w');

            // Header based on user request:
            // id, dealer_id, status, year, make, model, modelnumber, trim, series, body, vin, stocknumber, condition, exteriorcolorstandard, drivetrainstandard, created, city, state, views, pct, title, url
            fputcsv($handle, [
                'id', 'dealer_id', 'status', 'year', 'make', 'model', 'modelnumber', 'trim', 'series', 'body',
                'vin', 'stocknumber', 'condition', 'exteriorcolorstandard', 'drivetrainstandard',
                'created', 'city', 'state', 'views', 'pct', 'title', 'url',
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
                    $v->series ?? '', // Assuming series might be a dynamic attribute or exists in some contexts
                    $v->bodyType?->name,
                    $v->vin,
                    $v->stock_number,
                    $v->vehicle_condition,
                    $v->exteriorColor?->name,
                    $v->specs?->drivetrain_standard ?? $v->drivetrainType?->name,
                    $v->created_at->format('Y-m-d H:i:s'),
                    $v->dealer?->city,
                    $v->dealer?->state,
                    $v->total_views,
                    number_format($v->popularity, 2) . '%',
                    $v->display_title,
                    route('dealer.inventory.vdp.show', $v),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    /**
     * Cold Vehicles Report
     */
    public function coldVehicles(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;

        // Date range
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        // Total views for popularity calculation
        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        // Views per vehicle
        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel'])
            ->forDealer($dealerId)
            ->active()
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortBy('total_views')
            ->take(100); // Top 100 coldest

        return view('dealer.pages.website.reports.cold-vehicles', compact('vehicles', 'from', 'to'));
    }

    /**
     * Export Cold Vehicles to CSV
     */
    public function exportColdVehicles(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        // Total views for popularity calculation
        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel', 'bodyType', 'exteriorColor', 'drivetrainType', 'dealer', 'specs'])
            ->forDealer($dealerId)
            ->active()
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortBy('total_views');

        $filename = 'cold-vehicles-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($vehicles) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'id', 'dealer_id', 'status', 'year', 'make', 'model', 'modelnumber', 'trim', 'series', 'body',
                'vin', 'stocknumber', 'condition', 'exteriorcolorstandard', 'drivetrainstandard',
                'created', 'city', 'state', 'views', 'pct', 'title', 'url',
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
                    $v->series ?? '',
                    $v->bodyType?->name,
                    $v->vin,
                    $v->stock_number,
                    $v->vehicle_condition,
                    $v->exteriorColor?->name,
                    $v->specs?->drivetrain_standard ?? $v->drivetrainType?->name,
                    $v->created_at->format('Y-m-d H:i:s'),
                    $v->dealer?->city,
                    $v->dealer?->state,
                    $v->total_views,
                    number_format($v->popularity, 2) . '%',
                    $v->display_title,
                    route('dealer.inventory.vdp.show', $v),
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
