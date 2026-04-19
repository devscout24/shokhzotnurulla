{{-- location offcanvas --}}
<div id="locationMenu" class="offcanvas offcanvas-start" tabindex="-1" aria-labelledby="locationMenuLabel">
    <div class="m-0 border-bottom py-3 offcanvas-header">
        <div class="w-100 offcanvas-title h5">
            <span class="d-inline-block pt-1">{{ $dealerName ?: config('app.name') }}</span>
            <span class="d-inline-block text-muted float-end mb-0 me-1 cursor-pointer" data-bs-dismiss="offcanvas" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" fill="#a3a4a6" viewBox="0 0 16 16">
                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                </svg>
            </span>
        </div>
    </div>

    <div class="p-2 bg-light offcanvas-body">
        @php
            $loc          = $locationMenuData[0] ?? null;
            $salesHours   = $loc['hours_by_department']['sales']   ?? [];
            $serviceHours = $loc['hours_by_department']['service'] ?? [];
            $phones       = $loc['phones'] ?? [];
            $primaryPhone = collect($phones)->firstWhere('type', 'sales')
                         ?? collect($phones)->firstWhere('type', 'main')
                         ?? collect($phones)->first();
            $mapUrl = !empty($loc['map_override'])
                ? $loc['map_override']
                : 'https://www.google.com/maps/search/' . urlencode(
                    implode(', ', array_filter([
                        $loc['street1'] ?? '',
                        $loc['city']    ?? '',
                        $loc['state']   ?? '',
                    ]))
                  );
        @endphp

        @if ($loc)
            <div class="p-1 bg-white rounded border p-3 text-center">
                {{-- Address --}}
                @if (!empty($loc['street1']))   {{ $loc['street1'] }}<br> @endif
                @if (!empty($loc['street2']))   {{ $loc['street2'] }}<br> @endif
                @if (!empty($loc['city']))
                    {{ implode(', ', array_filter([$loc['city'], trim(($loc['state'] ?? '') . ' ' . ($loc['postalcode'] ?? ''))])) }}<br>
                @endif
                @if ($primaryPhone) {{ $primaryPhone['number'] }} @endif

                <div class="mt-3 row g-2">
                    <!-- Call Button -->
                    <div class="col-6">
                        <a href="tel:{{ $primaryPhone ? preg_replace('/\D/', '', $primaryPhone['number']) : '6152670590' }}"
                           class="btn btn-primary w-100 text-start d-flex align-items-center"
                           title="Call {{ $dealerName ?? config('app.name') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="me-2" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 1 .737-.168l2.522 1.01c.329.131.542.447.52.79l-.117 1.879a.678.678 0 0 1-.578.63l-1.12.163a11.72 11.72 0 0 0 5.516 5.516l.163-1.12a.678.678 0 0 1 .63-.578l1.879-.117c.343-.022.659.191.79.52l1.01 2.522a.678.678 0 0 1-.168.737l-1.272 1.272c-.329.329-.823.445-1.27.317C5.482 14.276 1.724 10.518.083 4.873a1.125 1.125 0 0 1 .317-1.27L1.672 2.33z" />
                            </svg>
                            Call
                        </a>
                    </div>

                    <!-- Directions Button -->
                    <div class="col-6">
                        <a href="{{ $mapUrl }}" target="_blank" rel="noopener"
                           class="btn btn-default w-100 text-start d-flex align-items-center"
                           title="Get Directions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#166B87" class="me-2" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 0-.538-.11l-14 5a.5.5 0 0 0 .033.949l5.518 1.72 1.72 5.518a.5.5 0 0 0 .949.033l5-14a.5.5 0 0 0-.11-.538z" />
                            </svg>
                            Directions
                        </a>
                    </div>
                </div>

                <div class="small text-uppercase font-weight-bold text-muted mt-3 py-3 border-top">Hours</div>

                <div class="w-100 mb-3 btn-group">
                    <button class="active btn btn-default" data-tab="sales">Sales</button>
                    <button class="btn btn-default" data-tab="service">Service</button>
                </div>

                <div class="tab-container">
                    {{-- Sales --}}
                    <div class="tab-content" data-tab="sales">
                        <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                            <thead class="thead-light small bg-lighter">
                                <tr>
                                    <th width="40%">Day</th>
                                    <th>Sales Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($salesHours as $day)
                                    <tr>
                                        <td>{{ $day['day_name'] }}</td>
                                        <td>
                                            @if ($day['is_closed'])          Closed
                                            @elseif ($day['appointment_only']) By Appointment
                                            @else                             {{ $day['open_time'] }} - {{ $day['close_time'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>Monday</td>
                                        <td>9:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Tuesday</td>
                                        <td>9:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Wednesday</td>
                                        <td>9:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Thursday</td>
                                        <td>9:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Friday</td>
                                        <td>9:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Saturday</td>
                                        <td>10:00 AM - 6:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Sunday</td>
                                        <td>Closed</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Service --}}
                    <div class="tab-content d-none" data-tab="service">
                        <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                            <thead class="thead-light small bg-lighter">
                                <tr>
                                    <th width="40%">Day</th>
                                    <th>Service Hours</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($serviceHours as $day)
                                    <tr>
                                        <td>{{ $day['day_name'] }}</td>
                                        <td>
                                            @if ($day['is_closed'])          Closed
                                            @elseif ($day['appointment_only']) By Appointment
                                            @else                             {{ $day['open_time'] }} - {{ $day['close_time'] }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td>Monday</td>
                                        <td>8:00 AM - 5:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Tuesday</td>
                                        <td>8:00 AM - 5:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Wednesday</td>
                                        <td>8:00 AM - 5:00 PM</td>
                                        </tr>
                                    <tr>
                                        <td>Thursday</td>
                                        <td>8:00 AM - 5:00 PM</td>
                                        </tr>
                                    <tr>
                                        <td>Friday</td>
                                        <td>8:00 AM - 5:00 PM</td>
                                        </tr>
                                    <tr>
                                        <td>Saturday</td>
                                        <td>9:00 AM - 2:00 PM</td>
                                    </tr>
                                    <tr>
                                        <td>Sunday</td>
                                        <td>Closed</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            {{-- Fallback --}}
            <div class="p-1 bg-white rounded border p-3 text-center">
                1339 South Lowry Street <br>
                Smyrna, TN 37167 <br>
                (615) 267-0590
                <div class="mt-3 row g-2">
                    <div class="col-6">
                        <a href="tel:+16152670590" class="btn btn-primary w-100 text-start d-flex align-items-center" title="Call {{ $dealerName ?? config('app.name') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" class="me-2" viewBox="0 0 16 16">
                                <path d="M3.654 1.328a.678.678 0 0 1 .737-.168l2.522 1.01c.329.131.542.447.52.79l-.117 1.879a.678.678 0 0 1-.578.63l-1.12.163a11.72 11.72 0 0 0 5.516 5.516l.163-1.12a.678.678 0 0 1 .63-.578l1.879-.117c.343-.022.659.191.79.52l1.01 2.522a.678.678 0 0 1-.168.737l-1.272 1.272c-.329.329-.823.445-1.27.317C5.482 14.276 1.724 10.518.083 4.873a1.125 1.125 0 0 1 .317-1.27L1.672 2.33z" />
                            </svg>
                            Call
                        </a>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-default w-100 text-start d-flex align-items-center" aria-label="Get Directions" title="Get Directions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#166B87" class="me-2" viewBox="0 0 16 16">
                                <path d="M15.854.146a.5.5 0 0 0-.538-.11l-14 5a.5.5 0 0 0 .033.949l5.518 1.72 1.72 5.518a.5.5 0 0 0 .949.033l5-14a.5.5 0 0 0-.11-.538z" />
                            </svg>
                            Directions
                        </button>
                    </div>
                </div>
                <div class="small text-uppercase font-weight-bold text-muted mt-3 py-3 border-top">Hours</div>
                <div class="w-100 mb-3 btn-group">
                    <button class="active btn btn-default" data-tab="sales">Sales</button>
                    <button class="btn btn-default" data-tab="service">Service</button>
                </div>
                <div class="tab-container">
                    <div class="tab-content" data-tab="sales">
                        <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                            <thead class="thead-light small bg-lighter">
                                <tr><th width="40%">Day</th><th>Sales Hours</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Monday</td><td>9:00 AM - 6:00 PM</td></tr>
                                <tr><td>Tuesday</td><td>9:00 AM - 6:00 PM</td></tr>
                                <tr><td>Wednesday</td><td>9:00 AM - 6:00 PM</td></tr>
                                <tr><td>Thursday</td><td>9:00 AM - 6:00 PM</td></tr>
                                <tr><td>Friday</td><td>9:00 AM - 6:00 PM</td></tr>
                                <tr><td>Saturday</td><td>10:00 AM - 6:00 PM</td></tr>
                                <tr><td>Sunday</td><td>Closed</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-content d-none" data-tab="service">
                        <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                            <thead class="thead-light small bg-lighter">
                                <tr><th width="40%">Day</th><th>Service Hours</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Monday</td><td>8:00 AM - 5:00 PM</td></tr>
                                <tr><td>Tuesday</td><td>8:00 AM - 5:00 PM</td></tr>
                                <tr><td>Wednesday</td><td>8:00 AM - 5:00 PM</td></tr>
                                <tr><td>Thursday</td><td>8:00 AM - 5:00 PM</td></tr>
                                <tr><td>Friday</td><td>8:00 AM - 5:00 PM</td></tr>
                                <tr><td>Saturday</td><td>9:00 AM - 2:00 PM</td></tr>
                                <tr><td>Sunday</td><td>Closed</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>