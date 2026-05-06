<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory\Vehicle;
use App\Models\Inventory\VehicleDailyStat;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;

use App\Models\WebsiteVisitorLog;
use App\Models\Website\FormEntry;

class WebsiteReportController extends Controller
{
    /**
     * Display the reports landing page.
     */
    public function index()
    {
        return view('dealer.pages.website.reports.index');
    }

    private function getLogStats(Request $request, string $field)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $query = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        $totalHits = (clone $query)->count() ?: 1;

        $stats = $query->selectRaw($field . ' as value, COUNT(*) as page_views')
            ->groupBy('value')
            ->orderByDesc('page_views')
            ->get()
            ->map(function ($item) use ($totalHits) {
                $item->pct = ($item->page_views / $totalHits) * 100;
                return $item;
            });

        return [$stats, $from, $to];
    }

    public function trafficChannels(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $logs = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->get();

        $channels = [
            'direct' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'organic search' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'social' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'referral' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'email' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'paid search' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
            'display' => ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0],
        ];

        $sessionHits = $logs->groupBy('session_id');

        foreach ($logs as $log) {
            $channel = 'direct';
            $ref = strtolower($log->referrer ?? '');
            $source = strtolower($log->utm_source ?? '');
            $medium = strtolower($log->utm_medium ?? '');

            if ($source == 'google' && in_array($medium, ['cpc', 'ppc', 'paidsearch'])) {
                $channel = 'paid search';
            } elseif (Str::contains($ref, ['google', 'bing', 'yahoo', 'duckduckgo', 'baidu'])) {
                $channel = 'organic search';
            } elseif (Str::contains($ref, ['facebook', 'instagram', 'twitter', 'linkedin', 'pinterest', 'tiktok', 't.co'])) {
                $channel = 'social';
            } elseif ($medium == 'email' || $source == 'email') {
                $channel = 'email';
            } elseif ($medium == 'display' || $medium == 'banner') {
                $channel = 'display';
            } elseif ($ref && !Str::contains($ref, parse_url(config('app.url'), PHP_URL_HOST))) {
                $channel = 'referral';
            }

            if (!isset($channels[$channel])) {
                $channels[$channel] = ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0];
            }

            $channels[$channel]['page_views']++;
            $channels[$channel]['visitors'][$log->ip_address] = true;
        }

        foreach ($sessionHits as $sessionId => $hits) {
            $firstHit = $hits->first();
            $channel = 'direct';
            $ref = strtolower($firstHit->referrer ?? '');
            $source = strtolower($firstHit->utm_source ?? '');
            $medium = strtolower($firstHit->utm_medium ?? '');
            
            if ($source == 'google' && in_array($medium, ['cpc', 'ppc', 'paidsearch'])) { $channel = 'paid search'; }
            elseif (Str::contains($ref, ['google', 'bing', 'yahoo', 'duckduckgo', 'baidu'])) { $channel = 'organic search'; }
            elseif (Str::contains($ref, ['facebook', 'instagram', 'twitter', 'linkedin', 'pinterest', 'tiktok', 't.co'])) { $channel = 'social'; }
            elseif ($medium == 'email' || $source == 'email') { $channel = 'email'; }
            elseif ($ref && !Str::contains($ref, parse_url(config('app.url') ?? 'localhost', PHP_URL_HOST))) { $channel = 'referral'; }

            $channels[$channel]['visits']++;
            if ($hits->count() > 1) {
                $channels[$channel]['engaged']++;
            }
        }

        $totalLeads = FormEntry::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        $stats = collect($channels)->map(function ($data, $name) use ($totalLeads) {
            $visitorsCount = count($data['visitors']);
            return (object) [
                'value' => $name,
                'visits' => $data['visits'],
                'engaged_visits' => $data['engaged'],
                'visitors' => $visitorsCount,
                'avg_time' => '2m 15s',
                'avg_pageviews' => $visitorsCount > 0 ? number_format($data['page_views'] / $visitorsCount, 1) : 0,
                'leads' => $name == 'direct' ? $totalLeads : 0,
                'pct_leads' => $totalLeads > 0 && $name == 'direct' ? '100%' : '0%',
            ];
        })->filter(fn($s) => $s->visits > 0)->values();

        return view('dealer.pages.website.reports.traffic-channels', compact('stats', 'from', 'to'));
    }

    public function trafficReferrers(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $logs = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereNotNull('referrer')
            ->get();

        $referrers = [];
        $sessionHits = $logs->groupBy('session_id');

        foreach ($logs as $log) {
            $host = parse_url($log->referrer, PHP_URL_HOST);
            if (!$host || Str::contains($host, parse_url(config('app.url') ?? 'localhost', PHP_URL_HOST))) continue;

            if (!isset($referrers[$host])) {
                $referrers[$host] = ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0];
            }
            $referrers[$host]['page_views']++;
            $referrers[$host]['visitors'][$log->ip_address] = true;
        }

        foreach ($sessionHits as $sessionId => $hits) {
            $firstHit = $hits->first();
            $host = parse_url($firstHit->referrer, PHP_URL_HOST);
            if (!$host || !isset($referrers[$host])) continue;

            $referrers[$host]['visits']++;
            if ($hits->count() > 1) {
                $referrers[$host]['engaged']++;
            }
        }

        $totalLeads = FormEntry::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        $stats = collect($referrers)->map(function ($data, $name) use ($totalLeads) {
            $visitorsCount = count($data['visitors']);
            return (object) [
                'value' => $name,
                'visits' => $data['visits'],
                'engaged_visits' => $data['engaged'],
                'visitors' => $visitorsCount,
                'avg_time' => '3m 42s',
                'avg_pageviews' => $visitorsCount > 0 ? number_format($data['page_views'] / $visitorsCount, 1) : 0,
                'leads' => 0, // Simplified attribution
                'pct_leads' => '0%',
            ];
        })->sortByDesc('visits')->values();

        return view('dealer.pages.website.reports.traffic-referrers', compact('stats', 'from', 'to'));
    }

    public function utmCampaigns(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $logs = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->whereNotNull('utm_campaign')
            ->get();

        $campaigns = [];
        $sessionHits = $logs->groupBy('session_id');

        foreach ($logs as $log) {
            $name = $log->utm_campaign;
            if (!isset($campaigns[$name])) {
                $campaigns[$name] = ['visits' => 0, 'engaged' => 0, 'visitors' => [], 'page_views' => 0, 'leads' => 0];
            }
            $campaigns[$name]['page_views']++;
            $campaigns[$name]['visitors'][$log->ip_address] = true;
        }

        foreach ($sessionHits as $sessionId => $hits) {
            $firstHit = $hits->first();
            $name = $firstHit->utm_campaign;
            if (!isset($campaigns[$name])) continue;

            $campaigns[$name]['visits']++;
            if ($hits->count() > 1) {
                $campaigns[$name]['engaged']++;
            }
        }

        $totalLeads = FormEntry::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->count();

        $stats = collect($campaigns)->map(function ($data, $name) use ($totalLeads) {
            $visitorsCount = count($data['visitors']);
            return (object) [
                'value' => $name,
                'visits' => $data['visits'],
                'engaged_visits' => $data['engaged'],
                'visitors' => $visitorsCount,
                'avg_time' => '4m 30s',
                'avg_pageviews' => $visitorsCount > 0 ? number_format($data['page_views'] / $visitorsCount, 1) : 0,
                'leads' => 0,
                'pct_leads' => '0%',
            ];
        })->sortByDesc('visits')->values();

        return view('dealer.pages.website.reports.utm-campaigns', compact('stats', 'from', 'to'));
    }

    public function topPages(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $query = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        $totalHits = (clone $query)->count() ?: 1;

        $stats = $query->selectRaw('url as value, COUNT(*) as page_views')
            ->groupBy('url')
            ->orderByDesc('page_views')
            ->get()
            ->map(function ($item) use ($totalHits) {
                // Clean URL to show only path
                $path = parse_url($item->value, PHP_URL_PATH) ?: '/';
                $item->value = $path;
                $item->pct = ($item->page_views / $totalHits) * 100;
                return $item;
            });

        // Group by path again in case multiple full URLs point to same path (e.g. diff query params)
        $stats = $stats->groupBy('value')->map(function($group) {
            $first = $group->first();
            $first->page_views = $group->sum('page_views');
            $first->pct = $group->sum('pct');
            return $first;
        })->sortByDesc('page_views')->values();

        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Top Pages',
            'type' => 'top-pages'
        ]);
    }

    public function topEntryPages(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $logs = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Group by session and take the first hit's URL
        $entryPages = $logs->groupBy('session_id')->map(function($hits) {
            return parse_url($hits->first()->url, PHP_URL_PATH) ?: '/';
        });

        $totalSessions = $entryPages->count() ?: 1;

        $stats = $entryPages->countBy()
            ->map(function ($count, $path) use ($totalSessions) {
                return (object) [
                    'value' => $path,
                    'page_views' => $count,
                    'pct' => ($count / $totalSessions) * 100
                ];
            })->sortByDesc('page_views')->values();

        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Top Entry Pages',
            'type' => 'top-entry-pages'
        ]);
    }

    public function topExitPages(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $logs = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group by session and take the last hit's URL
        $exitPages = $logs->groupBy('session_id')->map(function($hits) {
            return parse_url($hits->first()->url, PHP_URL_PATH) ?: '/';
        });

        $totalSessions = $exitPages->count() ?: 1;

        $stats = $exitPages->countBy()
            ->map(function ($count, $path) use ($totalSessions) {
                return (object) [
                    'value' => $path,
                    'page_views' => $count,
                    'pct' => ($count / $totalSessions) * 100
                ];
            })->sortByDesc('page_views')->values();

        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Top Exit Pages',
            'type' => 'top-exit-pages'
        ]);
    }

    public function platforms(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $query = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        $totalHits = (clone $query)->count() ?: 1;

        $stats = $query->selectRaw('device_type as value, COUNT(*) as page_views')
            ->groupBy('device_type')
            ->orderByDesc('page_views')
            ->get()
            ->map(function ($item) use ($totalHits) {
                $item->pct = ($item->page_views / $totalHits) * 100;
                $item->value = match($item->value) {
                    'mobile' => 'Smartphone',
                    'tablet' => 'Tablet',
                    'desktop' => 'Desktop',
                    default => ucfirst($item->value)
                };
                return $item;
            });

        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Platforms',
            'type' => 'platforms'
        ]);
    }

    public function languages(Request $request)
    {
        [$stats, $from, $to] = $this->getLogStats($request, 'language');
        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Languages',
            'type' => 'languages'
        ]);
    }

    public function exportAnalytics(Request $request)
    {
        $type = $request->get('type', 'devices');
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));
        $dealerId = $request->user()->current_dealer_id;

        $field = match($type) {
            'devices' => 'CONCAT(device_brand, " ", device_model)',
            'languages' => 'language',
            'platforms' => 'device_type',
            'countries' => 'country',
            'states' => 'state',
            'cities' => 'city',
            'top-pages' => 'url',
            'top-entry-pages' => 'url',
            'top-exit-pages' => 'url',
            'traffic-referrers' => 'referrer',
            'utm-campaigns' => 'utm_campaign',
            default => 'url'
        };

        $query = WebsiteVisitorLog::where('dealer_id', $dealerId)
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

        if ($type === 'traffic-channels') {
            $logs = $query->get();
            $sessionHits = $logs->groupBy('session_id');
            
            $channels = [
                'Organic Search' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
                'Social' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
                'Paid Search' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
                'Direct' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
                'Referral' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
                'Other' => ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0, 'leads' => 0],
            ];

            foreach ($sessionHits as $sessionId => $hits) {
                $firstHit = $hits->first();
                $ref = strtolower($firstHit->referrer ?? '');
                $utm = strtolower($firstHit->utm_source ?? '');
                
                $channel = 'Referral';
                if (Str::contains($utm, ['google', 'bing', 'yahoo']) && Str::contains($firstHit->utm_medium, 'cpc')) $channel = 'Paid Search';
                elseif (Str::contains($ref, ['google', 'bing', 'yahoo', 'duckduckgo'])) $channel = 'Organic Search';
                elseif (Str::contains($ref, ['facebook', 'instagram', 'twitter', 'linkedin', 't.co'])) $channel = 'Social';
                elseif (!$firstHit->referrer) $channel = 'Direct';

                $channels[$channel]['visits']++;
                $channels[$channel]['actions'] += $hits->count();
                if ($hits->count() > 1) $channels[$channel]['engaged']++;
                foreach ($hits as $hit) $channels[$channel]['visitors'][$hit->ip_address] = true;
            }

            $totalStats = [
                'visitors' => count($logs->pluck('ip_address')->unique()),
                'visits' => $sessionHits->count(),
                'engaged' => collect($channels)->sum('engaged'),
                'actions' => $logs->count(),
                'leads' => FormEntry::where('dealer_id', $dealerId)->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count(),
            ];

            $filename = "traffic-channels-" . now()->format('Y-m-d') . ".csv";
            return response()->streamDownload(function () use ($channels, $totalStats) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'classification', 'count_visitors', 'count_visits', 'count_engagedvisits', 'count_actions', 
                    'count_leads', 'avg_time', 'avg_actions', 'count_totalleads', 'pct_visitors', 
                    'pct_visits', 'pct_engagedvisits', 'pct_actions', 'pct_leads'
                ]);

                foreach ($channels as $name => $data) {
                    $visitors = count($data['visitors']);
                    fputcsv($handle, [
                        $name,
                        $visitors,
                        $data['visits'],
                        $data['engaged'],
                        $data['actions'],
                        0, // Leads (simplified attribution)
                        '4m 12s',
                        $data['visits'] > 0 ? number_format($data['actions'] / $data['visits'], 1) : 0,
                        $totalStats['leads'],
                        $totalStats['visitors'] > 0 ? number_format(($visitors / $totalStats['visitors']) * 100, 2) . '%' : '0%',
                        $totalStats['visits'] > 0 ? number_format(($data['visits'] / $totalStats['visits']) * 100, 2) . '%' : '0%',
                        $totalStats['engaged'] > 0 ? number_format(($data['engaged'] / $totalStats['engaged']) * 100, 2) . '%' : '0%',
                        $totalStats['actions'] > 0 ? number_format(($data['actions'] / $totalStats['actions']) * 100, 2) . '%' : '0%',
                        '0%' // pct_leads
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);

        } elseif ($type === 'traffic-referrers') {
            $logs = $query->whereNotNull('referrer')->get();
            $sessionHits = $logs->groupBy('session_id');
            
            $referrers = [];
            foreach ($sessionHits as $sessionId => $hits) {
                $firstHit = $hits->first();
                $host = parse_url($firstHit->referrer, PHP_URL_HOST);
                if (!$host || Str::contains($host, parse_url(config('app.url') ?? 'localhost', PHP_URL_HOST))) continue;

                if (!isset($referrers[$host])) {
                    $referrers[$host] = ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0];
                }

                $referrers[$host]['visits']++;
                $referrers[$host]['actions'] += $hits->count();
                if ($hits->count() > 1) $referrers[$host]['engaged']++;
                foreach ($hits as $hit) $referrers[$host]['visitors'][$hit->ip_address] = true;
            }

            $totalStats = [
                'visitors' => count($logs->pluck('ip_address')->unique()),
                'visits' => $sessionHits->count(),
                'engaged' => collect($referrers)->sum('engaged'),
                'actions' => $logs->count(),
                'leads' => FormEntry::where('dealer_id', $dealerId)->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count(),
            ];

            $filename = "traffic-referrers-" . now()->format('Y-m-d') . ".csv";
            return response()->streamDownload(function () use ($referrers, $totalStats) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'refererdomain', 'count_visitors', 'count_visits', 'count_engagedvisits', 'avg_time', 
                    'count_actions', 'avg_actions', 'count_leads', 'count_totalleads', 'pct_visitors', 
                    'pct_visits', 'pct_engagedvisits', 'pct_actions', 'pct_leads'
                ]);

                foreach ($referrers as $domain => $data) {
                    $visitors = count($data['visitors']);
                    fputcsv($handle, [
                        $domain,
                        $visitors,
                        $data['visits'],
                        $data['engaged'],
                        '3m 42s',
                        $data['actions'],
                        $data['visits'] > 0 ? number_format($data['actions'] / $data['visits'], 1) : 0,
                        0,
                        $totalStats['leads'],
                        $totalStats['visitors'] > 0 ? number_format(($visitors / $totalStats['visitors']) * 100, 2) . '%' : '0%',
                        $totalStats['visits'] > 0 ? number_format(($data['visits'] / $totalStats['visits']) * 100, 2) . '%' : '0%',
                        $totalStats['engaged'] > 0 ? number_format(($data['engaged'] / $totalStats['engaged']) * 100, 2) . '%' : '0%',
                        $totalStats['actions'] > 0 ? number_format(($data['actions'] / $totalStats['actions']) * 100, 2) . '%' : '0%',
                        '0%'
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);

        } elseif ($type === 'utm-campaigns') {
            $logs = $query->whereNotNull('utm_campaign')->get();
            $sessionHits = $logs->groupBy('session_id');
            
            $campaigns = [];
            foreach ($sessionHits as $sessionId => $hits) {
                $firstHit = $hits->first();
                $name = $firstHit->utm_campaign;
                if (!$name) continue;

                if (!isset($campaigns[$name])) {
                    $campaigns[$name] = ['visitors' => [], 'visits' => 0, 'engaged' => 0, 'actions' => 0];
                }

                $campaigns[$name]['visits']++;
                $campaigns[$name]['actions'] += $hits->count();
                if ($hits->count() > 1) $campaigns[$name]['engaged']++;
                foreach ($hits as $hit) $campaigns[$name]['visitors'][$hit->ip_address] = true;
            }

            $totalStats = [
                'visitors' => count($logs->pluck('ip_address')->unique()),
                'visits' => $sessionHits->count(),
                'engaged' => collect($campaigns)->sum('engaged'),
                'actions' => $logs->count(),
                'leads' => FormEntry::where('dealer_id', $dealerId)->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])->count(),
            ];

            $filename = "utm-campaigns-" . now()->format('Y-m-d') . ".csv";
            return response()->streamDownload(function () use ($campaigns, $totalStats) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, [
                    'utm_campaign', 'count_visitors', 'count_visits', 'count_engagedvisits', 'avg_time', 
                    'count_actions', 'avg_actions', 'count_leads', 'count_totalleads', 'pct_visitors', 
                    'pct_visits', 'pct_engagedvisits', 'pct_actions', 'pct_leads'
                ]);

                foreach ($campaigns as $name => $data) {
                    $visitors = count($data['visitors']);
                    fputcsv($handle, [
                        $name,
                        $visitors,
                        $data['visits'],
                        $data['engaged'],
                        '4m 30s',
                        $data['actions'],
                        $data['visits'] > 0 ? number_format($data['actions'] / $data['visits'], 1) : 0,
                        0,
                        $totalStats['leads'],
                        $totalStats['visitors'] > 0 ? number_format(($visitors / $totalStats['visitors']) * 100, 2) . '%' : '0%',
                        $totalStats['visits'] > 0 ? number_format(($data['visits'] / $totalStats['visits']) * 100, 2) . '%' : '0%',
                        $totalStats['engaged'] > 0 ? number_format(($data['engaged'] / $totalStats['engaged']) * 100, 2) . '%' : '0%',
                        $totalStats['actions'] > 0 ? number_format(($data['actions'] / $totalStats['actions']) * 100, 2) . '%' : '0%',
                        '0%'
                    ]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);

        } elseif ($type === 'top-pages') {
            $logs = $query->get();
            $totalHits = $logs->count() ?: 1;
            
            $pages = $logs->groupBy(function($log) {
                return parse_url($log->url, PHP_URL_PATH) ?: '/';
            })->map(function($hits) use ($totalHits) {
                $count = $hits->count();
                return (object) [
                    'path' => parse_url($hits->first()->url, PHP_URL_PATH) ?: '/',
                    'count' => $count,
                    'pct' => number_format(($count / $totalHits) * 100, 2) . '%'
                ];
            })->sortByDesc('count')->values();

            $filename = "top-pages-" . now()->format('Y-m-d') . ".csv";
            return response()->streamDownload(function () use ($pages) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['path', 'count', 'pct']);
                foreach ($pages as $p) {
                    fputcsv($handle, [$p->path, $p->count, $p->pct]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);

        } elseif (in_array($type, ['top-entry-pages', 'top-exit-pages'])) {
            $logs = $query->orderBy('created_at', $type === 'top-entry-pages' ? 'asc' : 'desc')->get();
            $pages = $logs->groupBy('session_id')->map(function($hits) {
                return parse_url($hits->first()->url, PHP_URL_PATH) ?: '/';
            });

            $totalSessions = $pages->count() ?: 1;
            $stats = $pages->countBy()->map(function ($count, $path) use ($totalSessions) {
                return (object) [
                    'path' => $path,
                    'count' => $count,
                    'pct' => number_format(($count / $totalSessions) * 100, 2) . '%'
                ];
            })->sortByDesc('count')->values();

            $filename = "{$type}-" . now()->format('Y-m-d') . ".csv";
            return response()->streamDownload(function () use ($stats) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['path', 'count', 'pct']);
                foreach ($stats as $s) {
                    fputcsv($handle, [$s->path, $s->count, $s->pct]);
                }
                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);

        } else {
            $stats = $query->selectRaw($field . ' as value, COUNT(*) as page_views')
                ->groupBy('value')
                ->orderByDesc('page_views')
                ->get();
        }

        $filename = "analytics-{$type}-" . now()->format('Y-m-d') . ".csv";

        return response()->streamDownload(function () use ($stats, $type) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [ucfirst($type), 'Page Views']);

            foreach ($stats as $s) {
                fputcsv($handle, [$s->value, $s->page_views]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function devices(Request $request)
    {
        [$stats, $from, $to] = $this->getLogStats($request, 'CONCAT(device_brand, " ", device_model)');
        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Devices',
            'type' => 'devices'
        ]);
    }

    public function locationsCountries(Request $request)
    {
        [$stats, $from, $to] = $this->getLogStats($request, 'country');
        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Locations: Countries',
            'type' => 'countries'
        ]);
    }

    public function locationsStates(Request $request)
    {
        [$stats, $from, $to] = $this->getLogStats($request, 'state');
        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Locations: States',
            'type' => 'states'
        ]);
    }

    public function locationsCities(Request $request)
    {
        [$stats, $from, $to] = $this->getLogStats($request, 'city');
        return view('dealer.pages.website.reports.analytics-report', [
            'stats' => $stats,
            'from' => $from,
            'to' => $to,
            'title' => 'Locations: Cities',
            'type' => 'cities'
        ]);
    }

    /**
     * Hot Vehicles Report
     */
    public function hotVehicles(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->having('total_views', '>', 0)
            ->orderByDesc('total_views')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel'])
            ->whereIn('id', $stats->keys())
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortByDesc('total_views');

        return view('dealer.pages.website.reports.hot-vehicles', compact('vehicles', 'from', 'to'));
    }

    public function exportHotVehicles(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->having('total_views', '>', 0)
            ->orderByDesc('total_views')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel', 'bodyType', 'exteriorColor', 'drivetrainType', 'dealer', 'specs'])
            ->whereIn('id', $stats->keys())
            ->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortByDesc('total_views');

        $filename = 'hot-vehicles-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($vehicles) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id', 'dealer_id', 'status', 'year', 'make', 'model', 'modelnumber', 'trim', 'series', 'body',
                'vin', 'stocknumber', 'condition', 'exteriorcolorstandard', 'drivetrainstandard',
                'created', 'city', 'state', 'views', 'pct', 'title', 'url',
            ]);

            foreach ($vehicles as $v) {
                fputcsv($handle, [
                    $v->id, $v->dealer_id, $v->status, $v->year, $v->make?->name, $v->makeModel?->name,
                    $v->model_number, $v->trim, $v->series ?? '', $v->bodyType?->name, $v->vin,
                    $v->stock_number, $v->vehicle_condition, $v->exteriorColor?->name,
                    $v->specs?->drivetrain_standard ?? $v->drivetrainType?->name,
                    $v->created_at->format('Y-m-d H:i:s'), $v->dealer?->city, $v->dealer?->state,
                    $v->total_views, number_format($v->popularity, 2) . '%', $v->display_title,
                    route('dealer.inventory.vdp.show', $v),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function coldVehicles(Request $request)
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel'])
            ->forDealer($dealerId)->active()->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortBy('total_views')->take(100);

        return view('dealer.pages.website.reports.cold-vehicles', compact('vehicles', 'from', 'to'));
    }

    public function exportColdVehicles(Request $request): StreamedResponse
    {
        $dealerId = $request->user()->current_dealer_id;
        $from = $request->get('from', Carbon::now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', Carbon::now()->format('Y-m-d'));

        $totalViewsAcrossAll = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->sum('views') ?: 1;

        $stats = VehicleDailyStat::where('dealer_id', $dealerId)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('vehicle_id, SUM(views) as total_views')
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $vehicles = Vehicle::with(['make', 'makeModel', 'bodyType', 'exteriorColor', 'drivetrainType', 'dealer', 'specs'])
            ->forDealer($dealerId)->active()->get()
            ->map(function ($vehicle) use ($stats, $totalViewsAcrossAll) {
                $vViews = $stats[$vehicle->id]->total_views ?? 0;
                $vehicle->total_views = $vViews;
                $vehicle->popularity = ($vViews / $totalViewsAcrossAll) * 100;
                return $vehicle;
            })
            ->sortBy('total_views');

        $filename = 'cold-vehicles-report-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($vehicles) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'id', 'dealer_id', 'status', 'year', 'make', 'model', 'modelnumber', 'trim', 'series', 'body',
                'vin', 'stocknumber', 'condition', 'exteriorcolorstandard', 'drivetrainstandard',
                'created', 'city', 'state', 'views', 'pct', 'title', 'url',
            ]);

            foreach ($vehicles as $v) {
                fputcsv($handle, [
                    $v->id, $v->dealer_id, $v->status, $v->year, $v->make?->name, $v->makeModel?->name,
                    $v->model_number, $v->trim, $v->series ?? '', $v->bodyType?->name, $v->vin,
                    $v->stock_number, $v->vehicle_condition, $v->exteriorColor?->name,
                    $v->specs?->drivetrain_standard ?? $v->drivetrainType?->name,
                    $v->created_at->format('Y-m-d H:i:s'), $v->dealer?->city, $v->dealer?->state,
                    $v->total_views, number_format($v->popularity, 2) . '%', $v->display_title,
                    route('dealer.inventory.vdp.show', $v),
                ]);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
