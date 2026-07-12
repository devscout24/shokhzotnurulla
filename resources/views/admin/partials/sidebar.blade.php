<aside class="sidebar" id="sidebar">
    {{-- <a class="sidebar-item-link" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>{{ __('Dashboard') }}</span>
        </div>
    </a> --}}
    <a class="sidebar-item-link" href="{{ route('admin.dealers.index') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.dealers.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>{{ __('Dealers') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('admin.restricted-sites.index') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.restricted-sites.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock"></i>
            <span>{{ __('Restricted Sites') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('admin.restricted-credits.index') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.restricted-credits.*') ? 'active' : '' }}">
            <i class="bi bi-shield-lock"></i>
            <span>{{ __('Restricted Credits') }}</span>
        </div>
    </a>
    {{-- <div style="margin: 20px 15px 10px; font-size: 11px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px;">Integrations</div> --}}
    {{-- <a class="sidebar-item-link" href="{{ route('admin.dealers.index') }}">
        <div class="sidebar-item">
            <i class="bi bi-cpu"></i>
            <span>{{ __('Connected Apps') }}</span>
        </div>
    </a> --}}
</aside>
