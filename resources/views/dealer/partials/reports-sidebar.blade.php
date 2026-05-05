<div class="rc-sidebar">
    <a href="{{ route('dealer.website.reports.hot-vehicles') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.hot-vehicles') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i> Hot Vehicles
    </a>
    <a href="{{ route('dealer.website.reports.cold-vehicles') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.cold-vehicles') ? 'active' : '' }}">
        <i class="bi bi-snow"></i> Cold Vehicles
    </a>
    <a href="{{ route('dealer.website.reports.traffic-channels') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.traffic-channels') ? 'active' : '' }}">
        <i class="bi bi-diagram-3"></i> Traffic Channels
    </a>
    <a href="{{ route('dealer.website.reports.traffic-referrers') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.traffic-referrers') ? 'active' : '' }}">
        <i class="bi bi-reply-all"></i> Traffic Referrers
    </a>
    <a href="{{ route('dealer.website.reports.utm-campaigns') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.utm-campaigns') ? 'active' : '' }}">
        <i class="bi bi-bullseye"></i> UTM Campaigns
    </a>
    <a href="{{ route('dealer.website.reports.top-pages') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.top-pages') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-bar-graph"></i> Top Pages
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-box-arrow-in-right"></i> Top Entry Pages
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-box-arrow-right"></i> Top Exit Pages
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-laptop"></i> Platforms
    </a>
    <a href="{{ route('dealer.website.reports.devices') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.devices') ? 'active' : '' }}">
        <i class="bi bi-phone"></i> Devices
    </a>
    <a href="{{ route('dealer.website.reports.locations.countries') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.locations.countries') ? 'active' : '' }}">
        <i class="bi bi-geo-alt"></i> Locations: Countries
    </a>
    <a href="{{ route('dealer.website.reports.locations.states') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.locations.states') ? 'active' : '' }}">
        <i class="bi bi-map"></i> Locations: States
    </a>
    <a href="{{ route('dealer.website.reports.locations.cities') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.reports.locations.cities') ? 'active' : '' }}">
        <i class="bi bi-pin-map"></i> Locations: Cities
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-translate"></i> Languages
    </a>
</div>
