<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminEnvController extends Controller
{
    public function index()
    {
        $apiKey = config('services.vehicle_databases.api_key', env('VEHICLE_DATABASES_API_KEY', ''));
        $googleTagId = config('services.google.tag_id', env('GOOGLE_TAG_ID', ''));

        return view('admin.pages.restriced-credits', [
            'apiKey' => $apiKey,
            'isVehicleApiKeyConfigured' => !empty($apiKey),
            'googleTagId' => $googleTagId,
            'isGoogleTagConfigured' => !empty($googleTagId),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'vehicle_databases_api_key' => ['nullable', 'string', 'min:1'],
            'google_tag_id' => ['nullable', 'string', 'min:1'],
        ]);


        $value = $request->input('vehicle_databases_api_key');
        $this->updateEnvValue('VEHICLE_DATABASES_API_KEY', $value);
        config(['services.vehicle_databases.api_key' => $value]);

        $value = $request->input('google_tag_id');
        $this->updateEnvValue('GOOGLE_TAG_ID', $value);
        config(['services.google.tag_id' => $value]);


        config(['services.vehicle_databases.api_key' => $value]);
        config(['services.google.tag_id' => $value]);

        return redirect()
            ->route('admin.restricted-credits.index')
            ->with('success', 'VEHICLE_DATABASES_API_KEY has been updated successfully.');
    }

    private function updateEnvValue(string $key, ?string $value): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $contents = file_get_contents($envPath);

        $escapedKey = preg_quote($key, '/');
        $pattern = "/^{$escapedKey}=.*/m";

        if (preg_match($pattern, $contents)) {
            $contents = preg_replace($pattern, "{$key}={$value}", $contents);
        } else {
            $contents = rtrim($contents, "\r\n") . "\n{$key}={$value}\n";
        }

        file_put_contents($envPath, $contents);

        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
