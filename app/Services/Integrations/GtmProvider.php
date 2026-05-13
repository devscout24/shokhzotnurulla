<?php

namespace App\Services\Integrations;

class GtmProvider extends IntegrationProvider
{
    public function testConnection(): bool
    {
        // Google Tag Manager is a frontend tracking snippet.
        // If they provided a correctly formatted GTM- container ID, we consider it connected.
        return !empty($this->settings['container_id']);
    }

    public function fetchData(array $params = []): mixed
    {
        return null;
    }
}
