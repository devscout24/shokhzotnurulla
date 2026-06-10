<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use App\Services\Exports\CarGurusService;
use App\Services\Exports\ExportMakerService;
use App\Services\Exports\TrueCarsService;
use Illuminate\Http\Request;

class DealerExportController extends Controller
{
    protected ExportMakerService $exportMaker;
    protected CarGurusService $cargursExporter;
    protected TrueCarsService $truecarsExporter;

    public function __construct(ExportMakerService $exportMaker, CarGurusService $cargursExporter, TrueCarsService $truecarsExporter)
    {
        $this->exportMaker = $exportMaker;
        $this->cargursExporter = $cargursExporter;
        $this->truecarsExporter = $truecarsExporter;
    }

    public function exportDealerVehiclesCsv(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->defaultExport($dealer, $request);
    }

    public function exportDealerVehiclesCarsForSales(Dealer $dealer, Request $request)
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

    public function exportCarGurusCsv(Request $request)
    {
        return $this->cargursExporter->bulkExport($request);
    }

    public function exportTrueCarsCsv(Request $request)
    {
        return $this->truecarsExporter->exportBulkCsv($request);
    }

    public function exportCsv(Dealer $dealer, Request $request)
    {
        return $this->exportMaker->makeExport($dealer, $request, 'truecars');
    }
}
