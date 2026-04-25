<aside class="vd-left">
    <img class="vd-vehicle-img" src="{{ $vehicleImgSrc }}" alt="{{ $vehicleTitle }}">

    <nav class="vd-nav">
        <a href="#" class="vd-nav-item active" data-nav="overview">
            <i class="bi bi-info-circle"></i> Overview
        </a>
        <a href="{{ route('dealer.inventory.vdp.gallery.show', $vehicle) }}" class="vd-nav-item" data-nav="photos">
            <i class="bi bi-images"></i> Photos
            @if($vehicle->photos->count() > 0)
                <span class="vd-nav-badge">{{ $vehicle->photos->count() }}</span>
            @endif
        </a>
        <a href="#" class="vd-nav-item" data-nav="videos">
            <i class="bi bi-camera-video"></i> Videos
        </a>
        <a href="#" class="vd-nav-item" data-nav="notes">
            <i class="bi bi-pencil"></i> Notes
        </a>
        <a href="#" class="vd-nav-item" data-nav="factory-options">
            <i class="bi bi-check2-square"></i> Installed Factory Options
        </a>
        <a href="#" class="vd-nav-item" data-nav="premium-build">
            <i class="bi bi-gear"></i> Premium Build Options
        </a>
        <a href="#" class="vd-nav-item" data-nav="incentives">
            <i class="bi bi-gift"></i> Incentives
        </a>
        <a href="#" class="vd-nav-item" data-nav="printables">
            <i class="bi bi-printer"></i> Printables
        </a>
        {{-- <a href="#" class="vd-nav-item" data-nav="analytics">
            <i class="bi bi-bar-chart-line"></i> Analytics
        </a> --}}
        <a href="#" class="vd-nav-item" data-nav="syndication">
            <i class="bi bi-broadcast"></i> Syndication
        </a>
    </nav>
</aside>
