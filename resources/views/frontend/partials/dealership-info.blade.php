@push('page-assets')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endpush

@php
    $loc          = $locationMenuData[0] ?? null;

    $salesHours   = $loc['hours_by_department']['sales']   ?? [];
    $serviceHours = $loc['hours_by_department']['service'] ?? [];
    $phones       = $loc['phones'] ?? [];

    $salesPhone   = collect($phones)->firstWhere('type', 'sales')
                 ?? collect($phones)->firstWhere('type', 'main')
                 ?? collect($phones)->first();
    $servicePhone = collect($phones)->firstWhere('type', 'service')
                 ?? $salesPhone;

    $street1    = $loc['street1']    ?? '1339 South Lowry Street';
    $street2    = $loc['street2']    ?? '';
    $city       = $loc['city']       ?? 'Smyrna';
    $state      = $loc['state']      ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';

    $fullAddress = implode(', ', array_filter([$street1, $city, $state, $postalcode]));

    $salesPhoneNumber   = $salesPhone   ? $salesPhone['number']   : '(615) 267-0590';
    $servicePhoneNumber = $servicePhone ? $servicePhone['number'] : '(615) 267-0590';

    $salesPhoneRaw   = preg_replace('/\D/', '', $salesPhoneNumber);
    $servicePhoneRaw = preg_replace('/\D/', '', $servicePhoneNumber);

    $cityLine = implode(', ', array_filter([
        $city,
        trim($state . ' ' . $postalcode),
    ]));

    $mapUrl = !empty($loc['map_override'])
        ? $loc['map_override']
        : 'https://www.google.com/maps/embed/v1/place?q='
            . urlencode(implode(',', array_filter([$street1, $city, $state, $postalcode])))
            . '&key=AIzaSyDNCy5KjYVZKZspyeNmAJ3E4rldiH28XfM';

    $dealerTitle = $dealerName ?: config('app.name');

    // Fallback hours (used when dynamic data is empty)
    $fallbackSalesHours = [
        ['day_name' => 'Monday',    'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM',  'close_time' => '6:00 PM'],
        ['day_name' => 'Tuesday',   'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM',  'close_time' => '6:00 PM'],
        ['day_name' => 'Wednesday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM',  'close_time' => '6:00 PM'],
        ['day_name' => 'Thursday',  'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM',  'close_time' => '6:00 PM'],
        ['day_name' => 'Friday',    'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM',  'close_time' => '6:00 PM'],
        ['day_name' => 'Saturday',  'is_closed' => false, 'appointment_only' => false, 'open_time' => '10:00 AM', 'close_time' => '6:00 PM'],
        ['day_name' => 'Sunday',    'is_closed' => true,  'appointment_only' => false, 'open_time' => '',         'close_time' => ''],
    ];

    $fallbackServiceHours = [
        ['day_name' => 'Monday',    'is_closed' => false, 'appointment_only' => false, 'open_time' => '8:00 AM', 'close_time' => '5:00 PM'],
        ['day_name' => 'Tuesday',   'is_closed' => false, 'appointment_only' => false, 'open_time' => '8:00 AM', 'close_time' => '5:00 PM'],
        ['day_name' => 'Wednesday', 'is_closed' => false, 'appointment_only' => false, 'open_time' => '8:00 AM', 'close_time' => '5:00 PM'],
        ['day_name' => 'Thursday',  'is_closed' => false, 'appointment_only' => false, 'open_time' => '8:00 AM', 'close_time' => '5:00 PM'],
        ['day_name' => 'Friday',    'is_closed' => false, 'appointment_only' => false, 'open_time' => '8:00 AM', 'close_time' => '5:00 PM'],
        ['day_name' => 'Saturday',  'is_closed' => false, 'appointment_only' => false, 'open_time' => '9:00 AM', 'close_time' => '2:00 PM'],
        ['day_name' => 'Sunday',    'is_closed' => true,  'appointment_only' => false, 'open_time' => '',        'close_time' => ''],
    ];

    $resolvedSalesHours   = !empty($salesHours)   ? $salesHours   : $fallbackSalesHours;
    $resolvedServiceHours = !empty($serviceHours) ? $serviceHours : $fallbackServiceHours;
@endphp

<div class="min-height-800">
    <section class="bg-light border-top" id="location-maps">
        <div class="container">
            <h3 class="text-center mb-5">
                Visit our used car dealership conveniently located in {{ $city }}, {{ $state }}: Near Murfreesboro, Nashville,
                Mount Juliet, Clarksville, and Cookeville.
            </h3>
            <div class="bg-white no-gutters rounded border row">
                <div class="px-4 py-4 text-center col-sm-6">
                    <span class="d-inline-block mb-3">
                        <i class="fa-solid fa-location-dot blue-text-37"></i>
                    </span>
                    <h4 class="notranslate">{{ $dealerTitle }}</h4>
                    <hr>
                    <strong>Street address:</strong>
                    <br>
                    {{ $street1 }}
                    <br>
                    @if (!empty($street2))
                        {{ $street2 }}<br>
                    @endif
                    {{ $cityLine }}
                    <br>
                    <br>
                    <strong>Phone:</strong>
                    <br>
                    <div>Sales: <a href="tel:+1{{ $salesPhoneRaw }}">{{ $salesPhoneNumber }}</a></div>
                    <div>Service: <a href="tel:+1{{ $servicePhoneRaw }}">{{ $servicePhoneNumber }}</a></div>
                    <br>

                    <div class="w-100 mb-3 btn-group">
                        <button class="active btn btn-default" data-tab="sales">Sales</button>
                        <button class="btn btn-default" data-tab="service">Service</button>
                    </div>

                    <div class="tab-container">
                        {{-- Sales Hours --}}
                        <div class="tab-content" data-tab="sales">
                            <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                                <thead class="thead-light small bg-lighter">
                                    <tr>
                                        <th width="40%">Day</th>
                                        <th>Sales Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resolvedSalesHours as $day)
                                        <tr>
                                            <td>{{ $day['day_name'] }}</td>
                                            <td>
                                                @if ($day['is_closed'])
                                                    Closed
                                                @elseif ($day['appointment_only'])
                                                    By Appointment
                                                @else
                                                    {{ $day['open_time'] }} - {{ $day['close_time'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Service Hours --}}
                        <div class="tab-content d-none" data-tab="service">
                            <table width="100%" class="bg-white table-bordered table table-sm rounded text-center mb-0">
                                <thead class="thead-light small bg-lighter">
                                    <tr>
                                        <th width="40%">Day</th>
                                        <th>Service Hours</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resolvedServiceHours as $day)
                                        <tr>
                                            <td>{{ $day['day_name'] }}</td>
                                            <td>
                                                @if ($day['is_closed'])
                                                    Closed
                                                @elseif ($day['appointment_only'])
                                                    By Appointment
                                                @else
                                                    {{ $day['open_time'] }} - {{ $day['close_time'] }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6">
                    {{-- <iframe
                        src="{{ $mapUrl }}"
                        width="100%"
                        title="Get directions to our store"
                        loading="lazy"
                        class="border full-height-grayscale">
                    </iframe> --}}
                    <div id="map" style="width:100%; height:100%;" class="border full-height-grayscale"></div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('page-scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

            let address = @json($fullAddress);

            let defaultLat = 31.4504;
            let defaultLng = 73.1350;

            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`)
                .then(res => res.json())
                .then(data => {
                    let lat = defaultLat;
                    let lon = defaultLng;

                    if (data && data.length > 0) {
                        lat = data[0].lat;
                        lon = data[0].lon;
                    }

                    let map = L.map('map').setView([lat, lon], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(map);

                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(address)
                        .openPopup();
                });
        });
        </script>
@endpush