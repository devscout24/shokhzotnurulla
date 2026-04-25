@extends('layouts.dealer.app')

@section('title', __('Manage Menus') . ' | '. __(config('app.name')))

@push('page-assets')
    @vite([
        'resources/css/dealer/pages/website-menus.css',
        'resources/js/dealer/pages/website-menus.js',
    ])
@endpush

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="page-header" style="display:flex;align-items:center;justify-content:space-between;">
            <h2 class="view-title">Menus</h2>
            <button type="button" class="mn-btn-add-link" id="btnAddLink"
                style="background:#c0392b;color:#fff;border:none;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;">
                + Add Link
            </button>
        </div>

        <div class="view-content" data-view="menus">
            <div class="mn-page">
                <hr class="mn-divider">
                <div class="mn-layout">

                    {{-- Sidebar --}}
                    <aside class="mn-sidebar">
                        <button class="mn-menu-tab active" data-menu="main" type="button">
                            <i class="bi bi-layout-text-sidebar"></i> Main Menu
                        </button>
                        <button class="mn-menu-tab" data-menu="footer" type="button">
                            <i class="bi bi-list-ul"></i> Footer Menu
                        </button>
                    </aside>

                    {{-- Center --}}
                    <div class="mn-center">
                        <div class="mn-info-box">
                            Drag each menu item into the order you prefer. The <strong>Main Menu</strong>
                            can have nested links for dropdowns, however the <strong>Footer Menu</strong>
                            is a plain, non-nestable list.
                        </div>

                        {{-- MAIN MENU --}}
                        <ul class="mn-list" id="mainMenuList">

                            {{-- <li class="mn-item">
                                <div class="mn-item-row">
                                    <span class="mn-drag-handle">⠿</span>
                                    <span class="mn-item-label">Inventory</span>
                                    <div class="mn-item-actions">
                                        <button class="mn-btn-edit btn-edit-link" type="button" data-label="Inventory" data-url="/inventory" data-target="_self" data-parent=""><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </div>
                                </div>
                                <ul class="mn-children">
                                    @foreach([['All inventory','/inventory/all'],['Cars','/cars'],['Trucks','/trucks'],['SUVs','/suvs'],['Vans','/vans'],['Convertibles','/convertibles'],['Hatchbacks','/hatchbacks']] as $c)
                                    <li class="mn-child-item">
                                        <div class="mn-item-row">
                                            <span class="mn-drag-handle">⠿</span>
                                            <span class="mn-item-label">{{ $c[0] }}</span>
                                            <div class="mn-item-actions">
                                                <button class="mn-btn-edit btn-edit-link" type="button" data-label="{{ $c[0] }}" data-url="{{ $c[1] }}" data-target="_self" data-parent="Inventory"><i class="bi bi-pencil"></i> Edit</button>
                                                <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>

                            <li class="mn-item">
                                <div class="mn-item-row">
                                    <span class="mn-drag-handle">⠿</span>
                                    <span class="mn-item-label">Finance</span>
                                    <div class="mn-item-actions">
                                        <button class="mn-btn-edit btn-edit-link" type="button" data-label="Finance" data-url="/finance" data-target="_self" data-parent=""><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </div>
                                </div>
                                <ul class="mn-children">
                                    @foreach([['Get approved','/get-approved'],['Get pre-qualified with Capital One','/capital-one'],['Car loan calculator','/calculator']] as $c)
                                    <li class="mn-child-item">
                                        <div class="mn-item-row">
                                            <span class="mn-drag-handle">⠿</span>
                                            <span class="mn-item-label">{{ $c[0] }}</span>
                                            <div class="mn-item-actions">
                                                <button class="mn-btn-edit btn-edit-link" type="button" data-label="{{ $c[0] }}" data-url="{{ $c[1] }}" data-target="_self" data-parent="Finance"><i class="bi bi-pencil"></i> Edit</button>
                                                <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>

                            @foreach([['Service','/service'],['Detailing','/detailing']] as $t)
                            <li class="mn-item">
                                <div class="mn-item-row">
                                    <span class="mn-drag-handle">⠿</span>
                                    <span class="mn-item-label">{{ $t[0] }}</span>
                                    <div class="mn-item-actions">
                                        <button class="mn-btn-edit btn-edit-link" type="button" data-label="{{ $t[0] }}" data-url="{{ $t[1] }}" data-target="_self" data-parent=""><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </div>
                                </div>
                            </li>
                            @endforeach

                            <li class="mn-item">
                                <div class="mn-item-row">
                                    <span class="mn-drag-handle">⠿</span>
                                    <span class="mn-item-label">About</span>
                                    <div class="mn-item-actions">
                                        <button class="mn-btn-edit btn-edit-link" type="button" data-label="About" data-url="/about" data-target="_self" data-parent=""><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </div>
                                </div>
                                <ul class="mn-children">
                                    @foreach([['About us','/about-us'],['Contact us','/contact'],['3 Month / 3,000 mile certified warranty','/warranty'],['Careers','/careers'],['Customer reviews','/reviews']] as $c)
                                    <li class="mn-child-item">
                                        <div class="mn-item-row">
                                            <span class="mn-drag-handle">⠿</span>
                                            <span class="mn-item-label">{{ $c[0] }}</span>
                                            <div class="mn-item-actions">
                                                <button class="mn-btn-edit btn-edit-link" type="button" data-label="{{ $c[0] }}" data-url="{{ $c[1] }}" data-target="_self" data-parent="About"><i class="bi bi-pencil"></i> Edit</button>
                                                <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </li> --}}

                        </ul>

                        {{-- FOOTER MENU --}}
                        <ul class="mn-list" id="footerMenuList" style="display:none;">
                            {{-- @foreach([['View inventory','/inventory'],['Directions','/directions'],['About us','/about-us'],['Get approved','/get-approved'],['Car loan calculator','/calculator'],['Privacy policy','/privacy'],['Terms of use','/terms'],['Contact us','/contact']] as $item)
                            <li class="mn-item">
                                <div class="mn-item-row">
                                    <span class="mn-drag-handle">⠿</span>
                                    <span class="mn-item-label">{{ $item[0] }}</span>
                                    <div class="mn-item-actions">
                                        <button class="mn-btn-edit btn-edit-link" type="button" data-label="{{ $item[0] }}" data-url="{{ $item[1] }}" data-target="_self" data-parent=""><i class="bi bi-pencil"></i> Edit</button>
                                        <button class="mn-btn-remove btn-remove-item" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </div>
                                </div>
                            </li>
                            @endforeach --}}
                        </ul>

                    </div>

                    {{-- SEO Panel --}}
                    <aside class="mn-seo-panel">
                        <div class="mn-seo-title">SEO-Friendly URLs</div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">New Franchise URLs:</div>
                            <ul><li>/new-acura</li><li>/new-honda</li></ul>
                        </div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">Used Franchise URLs:</div>
                            <ul><li>/used-inventory</li><li>/used-acura</li><li>/used-honda</li></ul>
                        </div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">OEM-specific URLs (new and used)</div>
                            <ul><li>/acura</li><li>/honda</li></ul>
                        </div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">Body Types</div>
                            <ul><li>/new-trucks</li><li>/used-trucks</li><li>/trucks</li></ul>
                        </div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">Filtering URLs (price, miles)</div>
                            <ul>
                                <li>/cars-under-{:miles}-miles-for-sale</li>
                                <li>/cars-under-10000-for-sale</li>
                                <li>/used-cars-under-{:price}-for-sale</li>
                                <li>/new-cars-under-25000-for-sale</li>
                            </ul>
                        </div>
                        <div class="mn-seo-group">
                            <div class="mn-seo-group-title">Geo-specific URLs</div>
                            <ul>
                                <li>/new-cars-indianapolis-in</li>
                                <li>/used-cars-for-sale-indianapolis-in</li>
                                <li>/cars-for-sale-{:city}-{:state}</li>
                                <li>/certified-cars-for-sale-{:city}-{:state}</li>
                            </ul>
                        </div>
                    </aside>

                </div>
            </div>
        </div>
    </main>
@endsection

@push('page-modals')
    @include('dealer.modals.website-add-edit-menu-link')
@endpush

@push('page-scripts')
    <script>
        window.menuRoutes = {
            data:    '{{ $routes['data'] }}',
            store:   '{{ $routes['store'] }}',
            update:  '{{ $routes['update'] }}',
            destroy: '{{ $routes['destroy'] }}',
            reorder: '{{ $routes['reorder'] }}',
        };
    </script>
@endpush
