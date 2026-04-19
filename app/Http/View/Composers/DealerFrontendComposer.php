<?php

namespace App\Http\View\Composers;

use App\Helpers\TimeHelper;
use App\Models\Dealership\Dealer;
use App\Models\Website\Location;
use App\Services\Website\DealerResolverService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DealerFrontendComposer
{
    private static bool $shared = false;

    private const DAY_NAMES = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    public function __construct(
        private readonly DealerResolverService $dealerResolver,
    ) {}

    public function compose(View $view): void
    {
        if (self::$shared) {
            return;
        }

        $dealerId = $this->dealerResolver->resolve();

        view()->share('_resolvedDealerId', $dealerId);

        if (! $dealerId) {
            view()->share($this->defaults());
            return;
        }

        $cached = Cache::remember(
            "dealer_{$dealerId}_frontend_settings",
            3600,
            fn () => $this->buildSettings($dealerId),
        );

        view()->share(array_merge($cached, [
            'todayHours' => $cached['hoursByDay'][now()->isoWeekday()] ?? null,
        ]));

        self::$shared = true;
    }

    // ── Builder (only runs on cache miss) ─────────────────────────────────────

    private function buildSettings(int $dealerId): array
    {
        $dealer = Dealer::select([
                'id',
                'name',
                'legal_name',
                'corporate_address',
                'support_email',
                'abandoned_form_minutes',
                'social_links',
                // Disclaimers
                'finance_disclaimer',
                'inventory_disclaimer',
                'deposit_disclaimer',
                'pricing_disclaimer',
                'optional_disclaimer',
                // Banner
                'banner_text',
                'banner_hover_title',
                'banner_text_color',
                'banner_bg_color',
                'banner_desktop_media_id',
                'banner_mobile_media_id',
            ])
            ->with([
                'bannerDesktopMedia:id,url',
                'bannerMobileMedia:id,url',
            ])
            ->find($dealerId);

        if (! $dealer) {
            return $this->defaults();
        }

        // Single query — all locations with phones + hours eager loaded
        $allLocations = Location::with(['phones', 'hours'])
            ->where('dealer_id', $dealerId)
            ->orderBy('order')
            ->get();

        $primary = $allLocations->first();

        return [
            // ── Header / footer data ──────────────────────────────────────
            'dealerName'           => $dealer->name,
            'dealerLegalName'      => $dealer->legal_name ?: $dealer->name,
            'dealerSocialLinks'    => $this->parseSocialLinks($dealer->social_links),
            'primaryAddress'       => $this->buildAddress($primary),
            'primaryPhone'         => $this->resolvePhone($primary, ['sales', 'main']),
            'servicePhone'         => $this->resolvePhone($primary, ['service']),
            'hoursByDay'           => $this->buildHoursByDay($primary, 'Sales'),

            // ── Corporate / contact info ──────────────────────────────────
            'corporateAddress'     => $dealer->corporate_address,
            'supportEmail'         => $dealer->support_email,
            'abandonedFormMinutes' => (int) ($dealer->abandoned_form_minutes ?? 30),

            // ── Disclaimers ───────────────────────────────────────────────
            'financeDisclaimer'    => $dealer->finance_disclaimer,
            'inventoryDisclaimer'  => $dealer->inventory_disclaimer,
            'depositDisclaimer'    => $dealer->deposit_disclaimer,
            'pricingDisclaimer'    => $dealer->pricing_disclaimer,
            'optionalDisclaimer'   => $dealer->optional_disclaimer,

            // ── Banner ────────────────────────────────────────────────────
            'bannerText'           => $dealer->banner_text,
            'bannerHoverTitle'     => $dealer->banner_hover_title,
            'bannerTextColor'      => $dealer->banner_text_color,
            'bannerBgColor'        => $dealer->banner_bg_color,
            'bannerDesktopMedia'   => $dealer->bannerDesktopMedia,
            'bannerMobileMedia'    => $dealer->bannerMobileMedia,

            // ── Location offcanvas data ───────────────────────────────────
            'locationMenuData'     => $this->buildLocationMenuData($allLocations),
        ];
    }

    // ── Location menu builder ─────────────────────────────────────────────────

    private function buildLocationMenuData(Collection $locations): array
    {
        return $locations
            ->map(fn (Location $location) => [
                'id'                  => $location->id,
                'name'                => $location->name,
                'street1'             => $location->street1,
                'street2'             => $location->street2,
                'city'                => $location->city,
                'state'               => $location->state,
                'postalcode'          => $location->postalcode,
                'map_override'        => $location->map_override,
                'phones'              => $location->phones
                    ->map(fn ($p) => ['type' => $p->type, 'number' => $p->number])
                    ->values()
                    ->all(),
                'hours_by_department' => $this->buildHoursByDepartment($location),
            ])
            ->values()
            ->all();
    }

    private function buildHoursByDepartment(Location $location): array
    {
        $departments = [];

        foreach ($location->hours->groupBy('department') as $dept => $hours) {
            $byDay = [];

            foreach ($hours as $hour) {
                $byDay[(int) $hour->day_of_week] = [
                    'day_name'         => self::DAY_NAMES[$hour->day_of_week] ?? 'Day ' . $hour->day_of_week,
                    'is_closed'        => (bool) $hour->is_closed,
                    'appointment_only' => (bool) $hour->appointment_only,
                    'open_time'        => $hour->open_time ? TimeHelper::fromDatabase($hour->open_time) : null,
                    'close_time'       => $hour->close_time ? TimeHelper::fromDatabase($hour->close_time) : null,
                ];
            }

            ksort($byDay);
            $departments[$dept] = $byDay;
        }

        return $departments;
    }

    // ── Frontend settings helpers ─────────────────────────────────────────────

    private function buildAddress(?Location $location): ?array
    {
        if (! $location) {
            return null;
        }

        $cityState = implode(', ', array_filter([
            $location->city,
            trim(($location->state ?? '') . ' ' . ($location->postalcode ?? '')),
        ]));

        return [
            'street1'      => $location->street1,
            'street2'      => $location->street2,
            'city'         => $location->city,
            'state'        => $location->state,
            'postalcode'   => $location->postalcode,
            'formatted'    => implode(', ', array_filter([$location->street1, $location->street2, $cityState])),
            'map_override' => $location->map_override,
        ];
    }

    private function resolvePhone(?Location $location, array $preferredTypes): ?string
    {
        if (! $location) {
            return null;
        }

        foreach ($preferredTypes as $type) {
            $phone = $location->phones->firstWhere('type', $type);
            if ($phone) {
                return $phone->number;
            }
        }

        if (in_array('sales', $preferredTypes) || in_array('main', $preferredTypes)) {
            return $location->phones->first()?->number;
        }

        return null;
    }

    private function buildHoursByDay(?Location $location, string $department): array
    {
        if (! $location) {
            return [];
        }

        $hoursByDay = [];

        foreach ($location->hours->where('department', $department) as $hour) {
            $hoursByDay[(int) $hour->day_of_week] = [
                'is_closed'        => (bool) $hour->is_closed,
                'appointment_only' => (bool) $hour->appointment_only,
                'open_time'        => $hour->open_time ? TimeHelper::toDisplay($hour->open_time) : null,
                'close_time'       => $hour->close_time ? TimeHelper::toDisplay($hour->close_time) : null,
            ];
        }

        return $hoursByDay;
    }

    private function parseSocialLinks(mixed $socialLinks): array
    {
        // ArrayObject aur array dono handle karo
        if ($socialLinks instanceof \ArrayObject) {
            $links = $socialLinks->getArrayCopy();
        } elseif (is_array($socialLinks)) {
            $links = $socialLinks;
        } else {
            $links = [];
        }

        $platforms = ['facebook', 'instagram', 'youtube', 'tiktok', 'twitter', 'pinterest', 'linkedin'];
        $parsed    = [];

        foreach ($platforms as $platform) {
            $url = $links[$platform] ?? null;
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $parsed[$platform] = $url;
            }
        }

        return $parsed;
    }

    private function defaults(): array
    {
        return [
            // Existing
            'dealerName'           => '',
            'dealerLegalName'      => '',
            'dealerSocialLinks'    => [],
            'primaryAddress'       => null,
            'primaryPhone'         => null,
            'servicePhone'         => null,
            'hoursByDay'           => [],
            'todayHours'           => null,
            'locationMenuData'     => [],

            // Corporate / contact
            'corporateAddress'     => null,
            'supportEmail'         => null,
            'abandonedFormMinutes' => 30,

            // Disclaimers
            'financeDisclaimer'    => null,
            'inventoryDisclaimer'  => null,
            'depositDisclaimer'    => null,
            'pricingDisclaimer'    => null,
            'optionalDisclaimer'   => null,

            // Banner
            'bannerText'           => null,
            'bannerHoverTitle'     => null,
            'bannerTextColor'      => null,
            'bannerBgColor'        => null,
            'bannerDesktopMedia'   => null,
            'bannerMobileMedia'    => null,
        ];
    }
}
