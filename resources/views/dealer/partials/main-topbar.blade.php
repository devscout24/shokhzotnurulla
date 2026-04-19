<nav class="topbar">
    <div class="mobile-top">
        <div class="mobile-row-1">
            <div class="logo-wrapper">
                <img src="{{ asset('assets/panels/common/images/logos/Dash_logo.png') }}" alt="Dashboard Logo">
            </div>
            <i class="bi bi-list mobile-toggle" id="mobileToggle"></i>
        </div>
        <div class="mobile-row-2">
            <div class="mobile-admin" id="mobileAdminToggle">
                <i class="bi bi-building"></i>
                <span>{{ __('Admin') }}</span>
            </div>
            <div class="mobile-settings" id="mobileSettingsToggle">
                <i class="bi bi-gear"></i>
                <span>{{ __('Settings') }}</span>
            </div>
            <div class="mobile-admin-menu" id="mobileAdminMenu" role="menu" aria-hidden="true">
                <a href="javascript:void(0)" class="settings-item" role="menuitem">{{ __('Admin Motors Inc') }}</a>
            </div>
            <div class="mobile-settings-menu" id="mobileSettingsMenu" role="menu" aria-hidden="true">
                <a href="{{ route('dealer.settings.profile') }}" class="settings-item" role="menuitem">{{ __('My profile') }}</a>
                {{-- <a href="{{ route('dealer.settings.authentication') }}" class="settings-item" role="menuitem">{{ __('2FA') }}</a> --}}
                <a href="{{ route('dealer.settings.security') }}" class="settings-item" role="menuitem">{{ __('Account security') }}</a>
                <div class="settings-divider" aria-hidden="true"></div>
                <a href="javascript:void(0)" class="settings-item logout" role="menuitem" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i
                        class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}</a>
            </div>
        </div>
    </div>

    <div class="topbar-inner">
        <div class="topbar-left">
            <div class="logo-wrapper">
                <img src="{{ asset('assets/panels/common/images/logos/Dash_logo.png') }}" alt="Dashboard Logo">
            </div>

            <div class="nav-links">
                <a href="{{ route('dealer.website.dashboard') }}">
                    <div class="nav-item {{ request()->routeIs('dealer.website.dashboard') ? 'active' : '' }}" data-view="dashboard">
                        <i class="bi bi-display"></i>
                            <span>{{ __('Website') }}</span>
                    </div>
                </a>
                <a href="{{ route('dealer.inventory.dashboard') }}">
                    <div class="nav-item {{ request()->routeIs('dealer.inventory.*') ? 'active' : '' }}" data-view="inventory">
                        <i class="bi bi-car-front"></i>
                            <span>{{ __('Inventory') }}</span>
                    </div>
                </a>

                <a href="{{ route('dealer.connections.apps') }}">
                    <div class="nav-item {{ request()->routeIs('dealer.connections.*') ? 'active' : '' }}" data-view="connections">
                        <i class="bi bi-box"></i>
                            <span>{{ __('Connections') }}</span>
                    </div>
                </a>
                {{-- <a href="{{ route('dealer.changelog') }}">
                    <div class="nav-item {{ request()->routeIs('dealer.changelog') ? 'active' : '' }}" data-view="whatsnew">
                        <i class="bi bi-stars"></i>
                            <span>{{ __("What's New") }}</span>
                            <span class="badge bg-danger ms-1">4</span>
                    </div>
                </a> --}}
            </div>
        </div>

        <div class="topbar-right">
            <div class="admin-dropdown">
                <i class="bi bi-building"></i>
                <span>{{ __('Admin Motors Inc') }}</span>
                <i class="bi bi-chevron-down"></i>
            </div>

            <div class="settings-dropdown" id="settingsDropdown">
                <button class="settings-toggle" id="settingsToggle" aria-expanded="false"
                    aria-controls="settingsMenu">
                    <i class="bi bi-gear"></i>
                    <span>{{ __('Settings') }}</span>
                    <i class="bi bi-chevron-down"></i>
                </button>

                <div class="settings-menu" id="settingsMenu" role="menu" aria-hidden="true">
                    <a href="{{ route('dealer.settings.profile') }}" class="settings-item" role="menuitem">{{ __('My profile') }}</a>
                    {{-- <a href="{{ route('dealer.settings.authentication') }}" class="settings-item" role="menuitem">{{ __('2FA') }}</a> --}}
                    <a href="{{ route('dealer.settings.security') }}" class="settings-item" role="menuitem">{{ __('Account security') }}</a>
                    <div class="settings-divider" aria-hidden="true"></div>
                    <a href="javascript:void(0)" class="settings-item logout" role="menuitem" onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i
                            class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}</a>
                </div>
            </div>
        </div>
    </div>
</nav>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>