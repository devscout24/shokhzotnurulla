<?php

namespace App\Actions\Website;

use App\Models\Dealership\Dealer;
use App\Models\Website\Redirect;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ImportRedirectsAction
{
    public function execute(Dealer $dealer, UploadedFile $file): array
    {
        $data = $this->parseCsv($file);
        $success = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            // Convert boolean fields from string "0"/"1" to actual booleans
            $row['is_regex'] = (bool) $row['is_regex'];
            $row['is_enabled'] = (bool) $row['is_enabled'];
            $row['status_code'] = (int) $row['status_code'];

            // Validate row
            $validator = Validator::make($row, [
                'source_url'  => 'required|string|max:255',
                'target_url'  => 'required|string|max:255',
                'is_regex'    => 'required|boolean',
                'status_code' => 'required|in:301,302',
                'is_enabled'  => 'required|boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row'    => $index + 2, // +2 because header row + 1-based indexing
                    'errors' => $validator->errors()->all(),
                ];
                continue;
            }

            // Check for duplicate
            $exists = Redirect::where('dealer_id', $dealer->id)
                ->where('source_url', $row['source_url'])
                ->where('is_regex', $row['is_regex'])
                ->exists();

            if ($exists) {
                $errors[] = [
                    'row'    => $index + 2,
                    'errors' => ['Duplicate redirect (source URL with same regex setting already exists) – skipped'],
                ];
                continue;
            }

            // Create redirect
            $dealer->redirects()->create([
                'source_url'  => $row['source_url'],
                'target_url'  => $row['target_url'],
                'is_regex'    => $row['is_regex'],
                'status_code' => $row['status_code'],
                'is_enabled'  => $row['is_enabled'],
            ]);

            $success++;
        }

        return ['success' => $success, 'errors' => $errors];
    }

    private function parseCsv(UploadedFile $file): Collection
    {
        $rows = [];
        $handle = fopen($file->getRealPath(), 'r');

        // Detect and remove UTF-8 BOM if present
        $bom = "\xEF\xBB\xBF";
        $line = fgets($handle);
        if (str_starts_with($line, $bom)) {
            $line = substr($line, 3);
        }
        // Now parse headers from the first line
        $headers = str_getcsv($line);
        // Then read remaining rows
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue; // skip malformed rows
            }
            $rows[] = array_combine($headers, $row);
        }

        fclose($handle);
        return collect($rows);
    }
}