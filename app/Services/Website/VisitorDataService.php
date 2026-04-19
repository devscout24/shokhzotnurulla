<?php

namespace App\Services\Website;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class VisitorDataService
{
    public function collect(Request $request): array
    {
        $agent = new Agent();
        $agent->setUserAgent($request->userAgent());

        return [
            'ip'       => $request->ip(),
            'location' => null, // future: GeoIP lookup
            'device'   => $this->resolveDevice($agent),
            'browser'  => $this->resolveBrowser($agent, $request),
            'traffic'  => $this->resolveTraffic($request),
        ];
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    private function resolveDevice(Agent $agent): array
    {
        $type = match (true) {
            $agent->isTablet()  => 'Tablet',
            $agent->isMobile()  => 'Smartphone',
            default             => 'Desktop',
        };

        return [
            'type'  => $type,
            'brand' => ($agent->isMobile() || $agent->isTablet()) ? ($agent->device() ?: null) : null,
            'model' => null, // jenssegers/agent does not provide model-level detail
        ];
    }

    private function resolveBrowser(Agent $agent, Request $request): array
    {
        $browser   = $agent->browser();
        $platform  = $agent->platform();

        return [
            'client'     => $browser  ?: null,
            'os'         => $platform ?: null,
            'os_version' => $platform ? ($agent->version($platform) ?: null) : null,
            'user_agent' => $request->userAgent(),
        ];
    }

    private function resolveTraffic(Request $request): array
    {
        $referer        = $request->headers->get('referer');
        $classification = $this->classifyTraffic($referer, $request->query('utm_source'));

        return [
            'referer'        => $referer,
            'classification' => $classification,
            'utm_source'     => $request->query('utm_source'),
            'utm_campaign'   => $request->query('utm_campaign'),
            'utm_term'       => $request->query('utm_term'),
            'utm_content'    => $request->query('utm_content'),
        ];
    }

    private function classifyTraffic(?string $referer, ?string $utmSource): ?string
    {
        if ($utmSource) {
            return 'paid search';
        }

        if (! $referer) {
            return 'direct';
        }

        $host = parse_url($referer, PHP_URL_HOST) ?? '';
        $host = strtolower($host);

        $organicDomains = [
            'google', 'bing', 'yahoo', 'duckduckgo', 'baidu',
            'yandex', 'ask', 'aol', 'ecosia',
        ];

        foreach ($organicDomains as $engine) {
            if (str_contains($host, $engine)) {
                return 'organic search';
            }
        }

        $socialDomains = [
            'facebook', 'instagram', 'twitter', 'x.com', 'linkedin',
            'tiktok', 'youtube', 'pinterest', 'reddit', 'snapchat',
        ];

        foreach ($socialDomains as $social) {
            if (str_contains($host, $social)) {
                return 'social';
            }
        }

        return 'referral';
    }
}
