<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Exception;

class Credit700Provider extends IntegrationProvider
{
    public function testConnection(): bool
    {
        $apiKey = $this->settings['api_key'] ?? null;
        $dealerCode = $this->settings['dealer_code'] ?? null;

        if (!$apiKey || !$dealerCode) {
            throw new Exception("Missing 700Credit credentials.");
        }

        try {
            $response = Http::post('https://api.700credit.com/v1/validate', [
                'apiKey' => $apiKey,
                'dealerCode' => $dealerCode,
            ]);

            if ($response->successful()) {
                return true;
            }

            throw new Exception("700Credit validation failed.");
        } catch (Exception $e) {
            return $this->handleError($e, 'testConnection');
        }
    }

    public function fetchData(array $params = []): mixed
    {
        try {
            $response = Http::withHeaders(['X-API-KEY' => $this->settings['api_key']])
                ->post('https://api.700credit.com/v1/credit-pull', $params);

            return $response->json();
        } catch (Exception $e) {
            return $this->handleError($e, 'fetchData');
        }
    }
}
