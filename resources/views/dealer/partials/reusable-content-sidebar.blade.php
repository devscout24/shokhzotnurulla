<div class="rc-sidebar">
    <a href="{{ route('dealer.website.faqs.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.faqs.*') ? 'active' : '' }}">
        <i class="bi bi-question-circle"></i> FAQs
    </a>
    <a href="{{ route('dealer.website.promo-banners.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.promo-banners.*') ? 'active' : '' }}">
        <i class="bi bi-megaphone"></i> OEM Promo Banners
    </a>
    <a href="{{ route('dealer.website.srp-content.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.srp-content.*') ? 'active' : '' }}">
        <i class="bi bi-file-earmark-text"></i> Content: Search Results (SRP)
    </a>
    <a href="{{ route('dealer.website.static-page-content.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.static-page-content.*') ? 'active' : '' }}">
        <i class="bi bi-file-text"></i> Static Page Content
    </a>
    <a href="{{ route('dealer.website.customer-reviews.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.customer-reviews.*') ? 'active' : '' }}">
        <i class="bi bi-star"></i> Customer Reviews
    </a>
    <a href="{{ route('dealer.website.staff-members.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.staff-members.*') ? 'active' : '' }}">
        <i class="bi bi-person"></i> Staff Members
    </a>
    <a href="{{ route('dealer.website.job-posts.index') }}" class="rc-sidebar-item {{ request()->routeIs('dealer.website.job-posts.*') ? 'active' : '' }}">
        <i class="bi bi-briefcase"></i> Job Posts
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-tags"></i> Service Offers
    </a>
    <a href="#" class="rc-sidebar-item">
        <i class="bi bi-calendar3"></i> Events
    </a>
</div>
