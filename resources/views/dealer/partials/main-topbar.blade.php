<nav class="topbar">
    <div class="mobile-top">
        <div class="mobile-row-1">
            <div class="logo-wrapper">
                <svg style="height:44px;width:auto;" viewBox="0 0 164.24 163.51" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="lg1" gradientUnits="userSpaceOnUse" x1="26.335" y1="8.3599" x2="160.376" y2="99.8003">
                            <stop offset="0" style="stop-color:#B379FD"/>
                            <stop offset="1" style="stop-color:#0088EA"/>
                        </linearGradient>
                        <linearGradient id="lg2" gradientUnits="userSpaceOnUse" x1="29.9617" y1="78.4065" x2="107.2338" y2="167.6826">
                            <stop offset="0" style="stop-color:#A6A3F8"/>
                            <stop offset="1" style="stop-color:#0F92FA"/>
                        </linearGradient>
                        <linearGradient id="lg3" gradientUnits="userSpaceOnUse" x1="127.5493" y1="142.9003" x2="89.232" y2="104.5831">
                            <stop offset="0.1869" style="stop-color:#51BFF5"/>
                            <stop offset="1" style="stop-color:#ABCFFC"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#lg1)" d="M163.3,81.76c0,16.59-4.95,32.03-13.47,44.9L118.1,73.61l-0.8-2.08c-1.54-4.13-3.68-8.04-6.41-11.5c-7.35-9.28-19.43-15.69-31.45-14.01c-2.32,0.31-4.61,0.91-6.81,1.69C57,53.23,49.18,68.18,40.95,81.45c-0.39-0.68-0.78-1.35-1.17-2.03c-1.06-1.83-2.11-3.66-3.17-5.49c-1.55-2.69-3.11-5.38-4.66-8.07c-1.88-3.26-3.76-6.51-5.64-9.77c-2.04-3.53-4.08-7.06-6.11-10.59c-4.09-7.08-8.54-14.26-12.81-21.35c-3.19-5.3-9.53-13.66-4.42-19.75C4.57,2.47,6.8,1.1,9.24,0.53C9.62,0.44,10,0.38,10.39,0.33c0.49-0.07,0.99-0.1,1.5-0.1h69.88C126.8,0.23,163.3,36.74,163.3,81.76z"/>
                    <path fill="url(#lg2)" d="M61.77,118.35l26.1,44.71c-2.01,0.15-4.05,0.23-6.1,0.23c0,0-69.86,0-69.88,0c-3,0-5.93-1.18-8.09-3.26c-2.1-2.02-3.62-4.81-3.11-7.85c0.55-3.29,3.18-6.49,4.82-9.33c2.59-4.49,5.18-8.98,7.78-13.47c3.94-6.83,7.88-13.66,11.83-20.49c0.8-1.38,1.6-2.76,2.39-4.15c0,0,13.41-23.24,13.42-23.25c0.01-0.02,0.02-0.03,0.03-0.05C49.18,68.18,57,53.23,72.63,47.71c2.2-0.78,4.49-1.38,6.81-1.69c12.02-1.68,24.1,4.73,31.45,14.01c2.73,3.46,4.87,7.37,6.41,11.5l0.8,2.08C125.25,125.81,61.77,118.35,61.77,118.35z"/>
                    <path fill="url(#lg3)" d="M149.83,126.66c-2.28,3.45-4.81,6.72-7.59,9.78c-0.91,1.01-1.85,2-2.82,2.97c-13.4,13.4-31.47,22.17-51.55,23.65l-26.1-44.71c0,0,63.48,7.46,56.33-44.74L149.83,126.66z"/>
                </svg>
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
                <svg style="height:44px;width:auto;" viewBox="0 0 164.24 163.51" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="lg1d" gradientUnits="userSpaceOnUse" x1="26.335" y1="8.3599" x2="160.376" y2="99.8003">
                            <stop offset="0" style="stop-color:#B379FD"/>
                            <stop offset="1" style="stop-color:#0088EA"/>
                        </linearGradient>
                        <linearGradient id="lg2d" gradientUnits="userSpaceOnUse" x1="29.9617" y1="78.4065" x2="107.2338" y2="167.6826">
                            <stop offset="0" style="stop-color:#A6A3F8"/>
                            <stop offset="1" style="stop-color:#0F92FA"/>
                        </linearGradient>
                        <linearGradient id="lg3d" gradientUnits="userSpaceOnUse" x1="127.5493" y1="142.9003" x2="89.232" y2="104.5831">
                            <stop offset="0.1869" style="stop-color:#51BFF5"/>
                            <stop offset="1" style="stop-color:#ABCFFC"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#lg1d)" d="M163.3,81.76c0,16.59-4.95,32.03-13.47,44.9L118.1,73.61l-0.8-2.08c-1.54-4.13-3.68-8.04-6.41-11.5c-7.35-9.28-19.43-15.69-31.45-14.01c-2.32,0.31-4.61,0.91-6.81,1.69C57,53.23,49.18,68.18,40.95,81.45c-0.39-0.68-0.78-1.35-1.17-2.03c-1.06-1.83-2.11-3.66-3.17-5.49c-1.55-2.69-3.11-5.38-4.66-8.07c-1.88-3.26-3.76-6.51-5.64-9.77c-2.04-3.53-4.08-7.06-6.11-10.59c-4.09-7.08-8.54-14.26-12.81-21.35c-3.19-5.3-9.53-13.66-4.42-19.75C4.57,2.47,6.8,1.1,9.24,0.53C9.62,0.44,10,0.38,10.39,0.33c0.49-0.07,0.99-0.1,1.5-0.1h69.88C126.8,0.23,163.3,36.74,163.3,81.76z"/>
                    <path fill="url(#lg2d)" d="M61.77,118.35l26.1,44.71c-2.01,0.15-4.05,0.23-6.1,0.23c0,0-69.86,0-69.88,0c-3,0-5.93-1.18-8.09-3.26c-2.1-2.02-3.62-4.81-3.11-7.85c0.55-3.29,3.18-6.49,4.82-9.33c2.59-4.49,5.18-8.98,7.78-13.47c3.94-6.83,7.88-13.66,11.83-20.49c0.8-1.38,1.6-2.76,2.39-4.15c0,0,13.41-23.24,13.42-23.25c0.01-0.02,0.02-0.03,0.03-0.05C49.18,68.18,57,53.23,72.63,47.71c2.2-0.78,4.49-1.38,6.81-1.69c12.02-1.68,24.1,4.73,31.45,14.01c2.73,3.46,4.87,7.37,6.41,11.5l0.8,2.08C125.25,125.81,61.77,118.35,61.77,118.35z"/>
                    <path fill="url(#lg3d)" d="M149.83,126.66c-2.28,3.45-4.81,6.72-7.59,9.78c-0.91,1.01-1.85,2-2.82,2.97c-13.4,13.4-31.47,22.17-51.55,23.65l-26.1-44.71c0,0,63.48,7.46,56.33-44.74L149.83,126.66z"/>
                </svg>
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
