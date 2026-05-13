<?php

namespace App\Services\Integrations;

use Illuminate\Support\Facades\Http;
use Exception;

class StripeProvider extends IntegrationProvider
{
    public function testConnection(): bool
    {
        $secretKey = $this->settings['secret_key'] ?? null;

        if (!$secretKey) {
            throw new Exception("Missing Stripe secret key.");
        }

        try {
            // Ping Stripe API to test credentials
            $response = Http::withToken($secretKey)
                ->get('https://api.stripe.com/v1/balance');

            if ($response->successful()) {
                return true;
            }

            throw new Exception("Stripe API returned: " . $response->json('error.message', 'Unknown error'));
        } catch (Exception $e) {
            return $this->handleError($e, 'testConnection');
        }
    }

    public function fetchData(array $params = []): mixed
    {
        return null;
    }
}
