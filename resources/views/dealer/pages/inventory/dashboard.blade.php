@extends('layouts.dealer.app')

@section('title', __('Inventory Dashbaord') . ' | '. __(config('app.name')))

@section('page-content')
    <main class="main-content" id="mainContent">
        <div class="view-content inventory-view" data-view="inventory">
            {{-- Inventory Top Bar --}}
            @include('dealer.partials.inventory-topbar')

            <div class="subview" data-subview="dashboard">
                <div class="inv-main-content">
                    <div class="inv-table-section">
                        <!-- filters and summary cards moved here -->
                        <div class="inv-top-bar">
                            <div class="inv-location-dropdown">
                                <i class="bi bi-geo-alt"></i>
                                <span>All Locations</span>
                                <i class="bi bi-chevron-down"></i>
                            </div>
                            <div class="inv-date-range">
                                <i class="bi bi-calendar3"></i>
                                <input type="text" id="inventoryDateRange" placeholder="Date range" class="flatpickr-input" readonly="readonly">
                            </div>
                        </div>
                        <div class="inv-cards-and-chart">
                            <div class="inv-cards-grid">
                                <div class="inv-card">
                                    <div class="inv-card-value">103 <span class="inv-card-label">in stock</span>
                                    </div>
                                    <div class="inv-card-amount">$1,367,635</div>
                                    <div class="inv-card-title">Inventory</div>
                                    <div class="inv-card-arrow"><i class="bi bi-arrow-left"></i></div>
                                </div>
                                <div class="inv-card">
                                    <div class="inv-card-value">15 <span class="inv-card-label">sold</span></div>
                                    <div class="inv-card-amount">$276,895</div>
                                    <div class="inv-card-title">Sold Report</div>
                                    <div class="inv-card-arrow"><i class="bi bi-arrow-left"></i></div>
                                </div>
                                <div class="inv-card">
                                    <div class="inv-card-value">21</div>
                                    <div class="inv-card-amount inv-warning">No Photos</div>
                                    <div class="inv-card-title">Fix</div>
                                    <div class="inv-card-arrow"><i class="bi bi-arrow-right"></i></div>
                                </div>
                                <div class="inv-card">
                                    <div class="inv-card-value">18</div>
                                    <div class="inv-card-amount inv-warning">No Price</div>
                                    <div class="inv-card-title">Fix</div>
                                    <div class="inv-card-arrow"><i class="bi bi-arrow-right"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="inv-table-header">
                            <h3>Inventory: Units Sold</h3>
                            <button class="btn-export-all">
                                <i class="bi bi-download"></i> Export All
                            </button>
                        </div>
                        <div class="inv-table-wrapper">
                            <table class="inv-table">
                                <thead>
                                    <tr>
                                        <th>Make</th>
                                        <th>Units Sold</th>
                                        <th>Avg. Days</th>
                                        <th>Est. Sales</th>
                                        <th>Avg. Price</th>
                                        <th># Changes</th>
                                        <th>Avg. Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>FORD</td>
                                        <td>5</td>
                                        <td>88</td>
                                        <td class="text-success">$78,997</td>
                                        <td class="text-success">$15,799</td>
                                        <td>1</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>SUBARU</td>
                                        <td>2</td>
                                        <td>278</td>
                                        <td class="text-success">$43,499</td>
                                        <td class="text-success">$21,750</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>TOYOTA</td>
                                        <td>1</td>
                                        <td>175</td>
                                        <td class="text-success">$25,700</td>
                                        <td class="text-success">$25,700</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>HONDA</td>
                                        <td>1</td>
                                        <td>179</td>
                                        <td class="text-success">$15,200</td>
                                        <td class="text-success">$15,200</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>CADILLAC</td>
                                        <td>1</td>
                                        <td>147</td>
                                        <td class="text-success">$13,700</td>
                                        <td class="text-success">$13,700</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>MAZDA</td>
                                        <td>1</td>
                                        <td>61</td>
                                        <td class="text-success">$16,900</td>
                                        <td class="text-success">$16,900</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>RAM</td>
                                        <td>1</td>
                                        <td>43</td>
                                        <td class="text-success">$29,500</td>
                                        <td class="text-success">$29,500</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>NISSAN</td>
                                        <td>1</td>
                                        <td>42</td>
                                        <td class="text-success">$20,500</td>
                                        <td class="text-success">$20,500</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>GENESIS</td>
                                        <td>1</td>
                                        <td>44</td>
                                        <td class="text-success">$15,999</td>
                                        <td class="text-success">$15,999</td>
                                        <td>1</td>
                                        <td class="text-danger">-5.6% <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                    <tr>
                                        <td>CHEVROLET</td>
                                        <td>1</td>
                                        <td>14</td>
                                        <td class="text-success">$16,800</td>
                                        <td class="text-success">$16,800</td>
                                        <td>--</td>
                                        <td>-- <span class="table-expand"><i class="bi bi-plus-lg"></i></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <aside class="inv-sidebar" style="display: block;">
                        <!-- main inventory chart moved here -->
                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Inventory Activity</div>
                            <div class="inv-card-box-content">
                                <img src="{{ asset('assets/panels/common/images/logos/graph-1.png') }}" alt="Inventory activity">
                            </div>
                        </div>

                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Days in Inventory</div>
                            <div class="inv-card-box-content">
                                <img src="{{ asset('assets/panels/common/images/logos/graph-2.png') }}" alt="Days in inventory">
                                <table class="inv-mini-table">
                                    <tbody>
                                        <tr>
                                            <td>Days</td>
                                            <td>Units</td>
                                            <td>Total</td>
                                            <td>Average</td>
                                        </tr>
                                        <tr>
                                            <td>0-30</td>
                                            <td>10</td>
                                            <td>$137,098</td>
                                            <td>$15,233</td>
                                        </tr>
                                        <tr>
                                            <td>31-60</td>
                                            <td>18</td>
                                            <td>$311,900</td>
                                            <td>$17,328</td>
                                        </tr>
                                        <tr>
                                            <td>61-90</td>
                                            <td>3</td>
                                            <td>$48,300</td>
                                            <td>$16,100</td>
                                        </tr>
                                        <tr>
                                            <td>91-120</td>
                                            <td>3</td>
                                            <td>$62,799</td>
                                            <td>$20,933</td>
                                        </tr>
                                        <tr>
                                            <td>120+</td>
                                            <td>46</td>
                                            <td>$807,538</td>
                                            <td>$17,555</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="inv-card-box">
                            <div class="inv-card-box-title">Inventory by Location</div>
                            <div class="inv-card-box-content">
                                <table class="inv-mini-table">
                                    <tbody>
                                        <tr>
                                            <td>Units</td>
                                            <td>Total</td>
                                            <td>Average</td>
                                        </tr>
                                        <tr class="company-row">
                                            <td colspan="3">Angel Motors Inc</td>
                                        </tr>
                                        <tr class="numbers-row">
                                            <td>103</td>
                                            <td>$1,480,334</td>
                                            <td>$17,416</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </main>
@endsection