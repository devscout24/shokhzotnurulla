<aside class="sidebar" id="sidebar">
    <a class="sidebar-item-link" href="{{ route('dealer.website.dashboard') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.dashboard') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i>
            <span>{{ __('Dashboard') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.form-entries.index') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.form-entries.*') ? 'active' : '' }}">
            <i class="bi bi-list"></i>
            <span>{{ __('Form Entries') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.media') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.media') ? 'active' : '' }}">
            <i class="bi bi-image"></i>
            <span>{{ __('Media Library') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.menus') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.menus') ? 'active' : '' }}">
            <i class="bi bi-link-45deg"></i>
            <span>{{ __('Menus') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.pages.index') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.pages.*') ? 'active' : '' }}">
            <i class="bi bi-file-text"></i>
            <span>{{ __('Pages') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.faqs.index') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.faqs.*', 'dealer.website.srp-content.*', 'dealer.website.static-page-content.*', 'dealer.website.promo-banners.*', 'dealer.website.customer-reviews.*') ? 'active' : '' }}">
            <i class="bi bi-layers"></i>
            <span>{{ __('Reusable Content') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('dealer.website.settings.general') }}">
        <div class="sidebar-item {{ request()->routeIs('dealer.website.settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>{{ __('Settings') }}</span>
        </div>
    </a>
</aside>