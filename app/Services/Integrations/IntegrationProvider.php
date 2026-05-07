<?php

namespace App\Services\Integrations;

use App\Models\Dealership\DealerIntegration;
use Exception;
use Illuminate\Support\Facades\Log;

abstract class IntegrationProvider
{
    protected DealerIntegration $integration;
    protected array $settings;

    public function __construct(DealerIntegration $integration)
    {
        $this->integration = $integration;
        $this->settings = $integration->settings ?? [];
    }

    /**
     * Test the connection to the third-party service.
     * Should throw an exception on failure with a user-friendly message.
     */
    abstract public function testConnection(): bool;

    /**
     * Fetch data from the third-party service.
     */
    abstract public function fetchData(array $params = []): mixed;

    /**
     * Standard error handler for API failures.
     */
    protected function handleError(Exception $e, string $method)
    {
        Log::error("Integration Error [{$this->integration->provider}]: {$method} - " . $e->getMessage());
        throw new Exception("Unable to connect to {$this->integration->provider}. Please check your credentials.");
    }
}
