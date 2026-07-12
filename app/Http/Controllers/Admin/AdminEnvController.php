<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminEnvController extends Controller
{
    public function index()
    {
        $apiKey = config('services.vehicle_databases.api_key', env('VEHICLE_DATABASES_API_KEY', ''));

        return view('admin.pages.restriced-credits', [
            'apiKey' => $apiKey,
            'isConfigured' => !empty($apiKey),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'vehicle_databases_api_key' => ['required', 'string', 'min:1'],
        ]);

        $value = $request->input('vehicle_databases_api_key');

        $this->updateEnvValue('VEHICLE_DATABASES_API_KEY', $value);

        config(['services.vehicle_databases.api_key' => $value]);

        return redirect()
            ->route('admin.restricted-credits.index')
            ->with('success', 'VEHICLE_DATABASES_API_KEY has been updated successfully.');
    }

    private function updateEnvValue(string $key, string $value): void
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
