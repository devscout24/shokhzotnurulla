<aside class="sidebar" id="sidebar">
    <a class="sidebar-item-link" href="{{ route('admin.dashboard') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>
            <span>{{ __('Dashboard') }}</span>
        </div>
    </a>
    <a class="sidebar-item-link" href="{{ route('admin.dealers.index') }}">
        <div class="sidebar-item {{ request()->routeIs('admin.dealers.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i>
            <span>{{ __('Dealers') }}</span>
        </div>
    </a>
</aside>
