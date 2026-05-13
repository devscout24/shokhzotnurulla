<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Exception;

class CarfaxProvider extends IntegrationProvider
{
    public function testConnection(): bool
    {
        $username = $this->settings['username'] ?? null;
        $password = $this->settings['password'] ?? null;

        if (!$username || !$password) {
            throw new Exception("Missing Carfax credentials.");
        }

        try {
            // Simulated Carfax API ping
            $response = Http::withBasicAuth($username, $password)
                ->get('https://api.carfax.com/v1/ping');

            if ($response->successful()) {
                return true;
            }

            throw new Exception("Carfax API returned: " . $response->status());
        } catch (Exception $e) {
            return $this->handleError($e, 'testConnection');
        }
    }

    public function fetchData(array $params = []): mixed
    {
        $vin = $params['vin'] ?? null;
        if (!$vin) throw new Exception("VIN is required for Carfax lookup.");

        try {
            $response = Http::withBasicAuth($this->settings['username'], $this->settings['password'])
                ->get("https://api.carfax.com/v1/reports/{$vin}");

            return $response->json();
        } catch (Exception $e) {
            return $this->handleError($e, 'fetchData');
        }
    }
}
