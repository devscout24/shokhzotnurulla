<?php
namespace App\Services\Analytics;

use App\Models\Dealership\Dealer;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\Metric;
use Illuminate\Support\Facades\Log;

class Ga4ReportingService
{
    private ?BetaAnalyticsDataClient $client = null;
    private ?string $propertyId              = null;

    public function __construct(Dealer $dealer)
    {
        $integration = $dealer->integrations()->operational()->where('provider', 'ga4')->first();

        if ($integration) {
            $this->propertyId   = $integration->getSetting('property_id');
            $serviceAccountJson = $integration->getSetting('service_account_json');

            if ($this->propertyId && $serviceAccountJson) {
                try {
                    $credentials = json_decode($serviceAccountJson, true);
                    if (is_array($credentials)) {
                        $this->client = new BetaAnalyticsDataClient([
                            'credentials' => $credentials,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error("GA4 Client Init Error: " . $e->getMessage());
                }
            }
        }
    }

    public function isConfigured(): bool
    {
        return $this->client !== null && $this->propertyId !== null;
    }

    /**
     * Fetch vehicle specific metrics (total views and total leads/events)
     */
    public function getVehicleMetrics(string $vehicleSlug)
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = $this->client->runReport([
                'property'        => 'properties/' . $this->propertyId,
                'dateRanges'      => [
                    new DateRange([
                        'start_date' => '2020-01-01', // Get all time data
                        'end_date'   => 'today',
                    ]),
                ],
                'dimensions'      => [
                    new Dimension(['name' => 'pagePath']),
                ],
                'metrics'         => [
                    new Metric(['name' => 'screenPageViews']),
                    new Metric(['name' => 'conversions']), // Assuming leads track as conversions
                ],
                'dimensionFilter' => new FilterExpression([
                    'filter' => new Filter([
                        'field_name'    => 'pagePath',
                        'string_filter' => new StringFilter([
                            'match_type' => MatchType::CONTAINS,
                            'value'      => $vehicleSlug,
                        ]),
                    ]),
                ]),
            ]);

            $views = 0;
            $leads = 0;

            foreach ($response->getRows() as $row) {
                $views += (int) $row->getMetricValues()[0]->getValue();
                $leads += (int) $row->getMetricValues()[1]->getValue();
            }

            return [
                'views' => $views,
                'leads' => $leads,
            ];

        } catch (\Exception $e) {
            Log::error("GA4 Report Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch vehicle views over time for line chart
     */
    public function getVehicleViewsOverTime(string $vehicleSlug, int $days = 30)
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $response = $this->client->runReport([
                'property'        => 'properties/' . $this->propertyId,
                'dateRanges'      => [
                    new DateRange([
                        'start_date' => $days . 'daysAgo',
                        'end_date'   => 'today',
                    ]),
                ],
                'dimensions'      => [
                    new Dimension(['name' => 'date']),
                ],
                'metrics'         => [
                    new Metric(['name' => 'screenPageViews']),
                ],
                'dimensionFilter' => new FilterExpression([
                    'filter' => new Filter([
                        'field_name'    => 'pagePath',
                        'string_filter' => new StringFilter([
                            'match_type' => MatchType::CONTAINS,
                            'value'      => $vehicleSlug,
                        ]),
                    ]),
                ]),
            ]);

            $chartData = [];
            foreach ($response->getRows() as $row) {
                // GA4 date format is YYYYMMDD
                $dateStr = $row->getDimensionValues()[0]->getValue();
                $date    = \Carbon\Carbon::createFromFormat('Ymd', $dateStr)->format('n/j');
                $views   = (int) $row->getMetricValues()[0]->getValue();

                $chartData[$dateStr] = [
                    'date'  => $date,
                    'views' => $views,
                ];
            }

            // Fill in missing dates with 0 views
            $filledData = [];
            $startDate  = \Carbon\Carbon::now()->subDays($days);
            for ($i = 0; $i <= $days; $i++) {
                $currentDate = $startDate->copy()->addDays($i);
                $dateKey     = $currentDate->format('Ymd');
                $dateLabel   = $currentDate->format('n/j');

                if (isset($chartData[$dateKey])) {
                    $filledData[] = $chartData[$dateKey];
                } else {
                    $filledData[] = [
                        'date'  => $dateLabel,
                        'views' => 0,
                    ];
                }
            }

            return $filledData;

        } catch (\Exception $e) {
            Log::error("GA4 Timeline Error: " . $e->getMessage());
            return null;
        }
    }
}
