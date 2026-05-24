<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealership\Dealer;
use Illuminate\Http\Request;

class DealerExportController extends Controller
{
    public function exportCsv(Request $request)
    {
        $fileName = 'dealers_export_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID', 'Company Name', 'Domain', 'Email', 'Phone', 'Status', 'Created At'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            Dealer::chunk(100, function ($dealers) use ($file) {
                foreach ($dealers as $dealer) {
                    $row = [
                        $dealer->id,
                        $dealer->company_name,
                        $dealer->domain,
                        $dealer->email,
                        $dealer->phone,
                        $dealer->status->value,
                        $dealer->created_at ? $dealer->created_at->format('Y-m-d H:i:s') : ''
                    ];

                    fputcsv($file, $row);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
