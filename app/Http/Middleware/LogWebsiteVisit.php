<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\WebsiteVisitorLog;
use App\Services\Website\DealerResolverService;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LogWebsiteVisit
{
    public function __construct(
        private readonly DealerResolverService $dealerResolver
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Resolve dealer
        $dealerId = $this->dealerResolver->resolve();

        if ($dealerId) {
            $agent = new Agent();
            $ip = $request->ip();

            // Simple Geolocation (Cache results to avoid API spam)
            $geo = Cache::remember("geo_{$ip}", 86400, function () use ($ip) {
                try {
                    $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        return $response->json();
                    }
                } catch (\Exception $e) {}
                return null;
            });

            WebsiteVisitorLog::create([
                'dealer_id'    => $dealerId,
                'ip_address'   => $ip,
                'device_brand' => $agent->device(),
                'device_model' => $agent->platform(),
                'device_type'  => $agent->isMobile() ? 'mobile' : ($agent->isTablet() ? 'tablet' : 'desktop'),
                'country'      => $geo['country'] ?? 'Unknown',
                'state'        => $geo['regionName'] ?? 'Unknown',
                'city'         => $geo['city'] ?? 'Unknown',
                'url'          => $request->fullUrl(),
                'referrer'     => $request->header('referer'),
                'utm_source'   => $request->get('utm_source'),
                'utm_medium'   => $request->get('utm_medium'),
                'utm_campaign' => $request->get('utm_campaign'),
                'session_id'   => session()->getId(),
                'language'     => $this->getBrowserLanguage($request),
            ]);
        }

        return $next($request);
    }

    private function getBrowserLanguage($request)
    {
        $lang = $request->header('Accept-Language');
        if (!$lang) return 'Unknown';
        
        $primary = explode(',', $lang)[0];
        $code = explode('-', $primary)[0];

        $languages = [
            'en' => 'English',
            'es' => 'Spanish',
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ru' => 'Russian',
            'zh' => 'Chinese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'vi' => 'Vietnamese',
            'fa' => 'Persian (Farsi)',
            'nl' => 'Dutch',
        ];

        return $languages[$code] ?? 'Unknown';
    }
}
