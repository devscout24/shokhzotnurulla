<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Services\Exports\ExportMakerService;
use Illuminate\Http\Request;

class DealerExportController extends Controller
{
    protected ExportMakerService $exportMaker;

    public function __construct(ExportMakerService $exportMaker)
    {
        $this->exportMaker = $exportMaker;
    }

    public function exportDealerVehiclesCsv(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->defaultExport($dealer, $request);
    }

    public function exportDealerVehiclesCarsForSaleCsv(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->makeExport($dealer, $request, 'cars-for-sales');
    }

    public function exportDealerVehiclesCarFax(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->makeExport($dealer, $request, 'carfax');
    }

    public function exportDealerVehiclesTrueCars(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->makeExport($dealer, $request, 'truecars');
    }

    public function exportCsv(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->makeExport($dealer, $request, 'truecars');
    }
}
