<?php

namespace App\Services\Integrations;

class Ga4Provider extends IntegrationProvider
{
    public function testConnection(): bool
    {
        // Google Analytics 4 is a frontend tracking pixel.
        // If they provided a correctly formatted G- measurement ID, we consider it connected.
        return !empty($this->settings['measurement_id']);
    }

    public function fetchData(array $params = []): mixed
    {
        return null;
    }
}
