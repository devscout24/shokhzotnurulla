<div class="topbar-secondary" style="display: flex;">
    <div class="nav-links">
        <a href="{{ route('dealer.inventory.dashboard') }}">
            <div class="nav-item {{ request()->routeIs('dealer.inventory.dashboard') ? 'active' : '' }}" data-view="dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
                <span class="badge bg-danger ms-1">39</span>
            </div>
        </a>
        <a href="{{ route('dealer.inventory.index') }}">
            <div class="nav-item {{ (request()->routeIs('dealer.inventory.index') || request()->routeIs('dealer.inventory.vdp.*')) ? 'active' : '' }}" data-view="inventory">
                <i class="bi bi-car-front"></i>
                <span>Inventory</span>
            </div>
        </a>
        <a href="{{ route('dealer.inventory.incentives.index') }}">
            <div class="nav-item {{ request()->routeIs('dealer.inventory.incentives.index') ? 'active' : '' }}" data-view="incentives">
                <i class="bi bi-gift"></i>
                <span>Incentives</span>
            </div>
        </a>
        <a href="{{ route('dealer.inventory.pricing-specials.index') }}">
            <div class="nav-item {{ request()->routeIs('dealer.inventory.pricing-specials.index') ? 'active' : '' }}" data-view="pricing">
                <i class="bi bi-tags"></i>
                <span>Pricing Specials</span>
            </div>
        </a>
        <a href="{{ route('dealer.inventory.settings.rates.index') }}">
            <div class="nav-item {{ request()->routeIs('dealer.inventory.settings.*') ? 'active' : '' }}" data-view="settings">
                <i class="bi bi-gear"></i>
                <span>Settings</span>
            </div>
        </a>
        <a href="{{ route('dealer.inventory.reports.index') }}">
            <div class="nav-item {{ request()->routeIs('dealer.inventory.reports.*') ? 'active' : '' }}" data-view="reports">
                <i class="bi bi-bar-chart"></i>
                <span>Reports</span>
            </div>
        </a>
    </div>
    <div class="top-search">
        <input type="text" placeholder="Search by make, model, feature">
        <i class="bi bi-search search-icon"></i>
    </div>
</div>
