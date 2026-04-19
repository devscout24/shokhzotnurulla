{{-- Search Inventory Modal --}}
<div class="modal fade" id="modalSearch" tabindex="-1" aria-labelledby="modalSearchLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header border-bottom m-0 p-0">
                <div class="w-100 p-3 modal-title h4">
                    <div class="d-flex align-items-center w-100">
                        <div class="position-relative w-100">
                            <span class="position-absolute top-left-offset">
                                <svg height="15" width="15" fill="#166B87" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                    <path d="M284.924 97.404l-26.16-53.33-26.159 53.33-58.643 8.528 42.454 41.354-10.03 58.46 52.388-27.558 52.388 27.558-10.03-58.46 42.454-41.354-58.643-8.528zM432 288c0-8.837-7.163-16-16-16h-80v-80c0-8.837-7.163-16-16-16h-64c-8.837 0-16 7.163-16 16v80h-80c-8.837 0-16 7.163-16 16v64c0 8.837 7.163 16 16 16h80v80c0 8.837 7.163 16 16 16h64c8.837 0 16-7.163 16-16v-80h80c8.837 0 16-7.163 16-16v-64z" />
                                </svg>
                            </span>
                            <input type="text" id="modalSearchInput" class="form-control form-control-lg ps-5" placeholder="Search inventory..." autocomplete="off">
                        </div>
                        <button type="button" class="btn btn-link text-small py-2 ps-3" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-0 bg-light modal-body">

                {{-- ── Trending Brands ───────────────────────────────────────────────── --}}
                <div class="px-3 py-2 pb-lg-3 border-bottom">
                    <div class="mb-2"><small><strong>Trending Brands</strong></small></div>
                    <div class="sc-3c5deaa6-0 bUbZDx">

                        @php
                            $trendingMakes = [
                                ['name' => 'Nissan',        'label' => 'NISSAN',        'img' => asset('assets/frontend/img/nissan.webp'), 'local' => true],
                                ['name' => 'Ford',          'label' => 'FORD',          'img' => 'https://static.overfuel.com/images/logos/makes/ford.webp'],
                                ['name' => 'Toyota',        'label' => 'TOYOTA',        'img' => 'https://static.overfuel.com/images/logos/makes/toyota.webp'],
                                ['name' => 'Chevrolet',     'label' => 'Chevrolet',     'img' => 'https://static.overfuel.com/images/logos/makes/chevrolet.webp'],
                                ['name' => 'Honda',         'label' => 'HONDA',         'img' => 'https://static.overfuel.com/images/logos/makes/honda.webp'],
                                ['name' => 'Ram',           'label' => 'RAM',           'img' => 'https://static.overfuel.com/images/logos/makes/ram.webp'],
                                ['name' => 'Subaru',        'label' => 'SUBARU',        'img' => 'https://static.overfuel.com/images/logos/makes/subaru.webp'],
                                ['name' => 'Volkswagen',    'label' => 'VOLKSWAGEN',    'img' => 'https://static.overfuel.com/images/logos/makes/volkswagen.webp'],
                                ['name' => 'Audi',          'label' => 'Audi',          'img' => 'https://static.overfuel.com/images/logos/makes/audi.webp'],
                                ['name' => 'BMW',           'label' => 'BMW',           'img' => 'https://static.overfuel.com/images/logos/makes/bmw.webp'],
                                ['name' => 'Land Rover',    'label' => 'LAND ROVER',    'img' => 'https://static.overfuel.com/images/logos/makes/land-rover.webp'],
                                ['name' => 'Mazda',         'label' => 'MAZDA',         'img' => 'https://static.overfuel.com/images/logos/makes/mazda.webp'],
                                ['name' => 'Mercedes-Benz', 'label' => 'MERCEDES-BENZ', 'img' => 'https://static.overfuel.com/images/logos/makes/mercedes-benz.webp'],
                                ['name' => 'Dodge',         'label' => 'DODGE',         'img' => 'https://static.overfuel.com/images/logos/makes/dodge.webp'],
                                ['name' => 'Genesis',       'label' => 'GENESIS',       'img' => 'https://static.overfuel.com/images/logos/makes/genesis.webp'],
                                ['name' => 'GMC',           'label' => 'GMC',           'img' => 'https://static.overfuel.com/images/logos/makes/gmc.webp'],
                                ['name' => 'INFINITI',      'label' => 'INFINITI',      'img' => 'https://static.overfuel.com/images/logos/makes/infiniti.webp'],
                                ['name' => 'Lexus',         'label' => 'LEXUS',         'img' => 'https://static.overfuel.com/images/logos/makes/lexus.webp'],
                            ];
                        @endphp

                        @foreach ($trendingMakes as $make)
                            <a class="makeLink border-end d-block text-nowrap py-3 px-3 text-center mb-0 make-{{ Str::slug($make['name']) }}"
                               href="{{ route('frontend.inventory') }}?make[]={{ urlencode($make['name']) }}"
                               title="Browse {{ $make['label'] }} inventory"
                               >
                                <img class="d-block mx-auto" height="50"
                                     alt="Browse {{ $make['label'] }} inventory"
                                     src="{{ $make['img'] }}">
                                <small class="d-none">{{ $make['label'] }}</small>
                            </a>
                        @endforeach

                    </div>
                </div>

                {{-- ── Styles (Body Types) ────────────────────────────────────────────── --}}
                <div class="px-3 pt-2 pb-2 pb-lg-1">
                    <div class="mb-2"><small><strong>Styles</strong></small></div>

                    @php
                        $styles = [
                            'Cargo van',
                            'Convertible',
                            'Coupe',
                            'Hatchback',
                            'Pickup truck',
                            'Sedan',
                            'SUV',
                            'Van',
                            'Wagon',
                        ];
                    @endphp

                    @foreach ($styles as $style)
                        <a class="d-inline-block me-2 mb-2 rounded border bg-white py-2 px-3 border-dark"
                           href="{{ route('frontend.inventory') }}?body_type[]={{ urlencode($style) }}"
                           title="Browse {{ $style }} inventory"
                           >
                            {{ $style }}
                        </a>
                    @endforeach
                </div>

                {{-- ── Features ───────────────────────────────────────────────────────── --}}
                <div class="px-3 pt-2 pb-2 pb-lg-1 border-bottom">
                    <div class="mb-2"><small><strong>Features</strong></small></div>

                    @php
                        $features = [
                            ['name' => 'Adaptive cruise control',  'icon' => asset('assets/frontend/img/adaptive-cruise-control.png')],
                            ['name' => 'Android Auto',             'icon' => asset('assets/frontend/img/android-auto.png')],
                            ['name' => 'Apple CarPlay',            'icon' => asset('assets/frontend/img/apple-carplay.png')],
                            ['name' => 'Automatic climate control','icon' => asset('assets/frontend/img/automatic-climate-control.png')],
                            ['name' => 'Backup camera',            'icon' => 'https://static.overfuel.com/images/features/backup-camera.png?w=32&q=80'],
                            ['name' => 'Blind spot monitor',       'icon' => 'https://static.overfuel.com/images/features/blind-spot-monitor.png?w=32&q=80'],
                            ['name' => 'Bluetooth',                'icon' => 'https://static.overfuel.com/images/features/bluetooth.png?w=32&q=80'],
                            ['name' => 'Captain seats',            'icon' => 'https://static.overfuel.com/images/features/captain-seats.png?w=32&q=80'],
                            ['name' => 'Collision warning',        'icon' => 'https://static.overfuel.com/images/features/collision-warning.png?w=32&q=80'],
                            ['name' => 'Cooled seats',             'icon' => 'https://static.overfuel.com/images/features/cooled-seats.png?w=32&q=80'],
                            ['name' => 'Cross traffic alert',      'icon' => 'https://static.overfuel.com/images/features/cross-traffic-alert.png?w=32&q=80'],
                            ['name' => 'Fog lights',               'icon' => 'https://static.overfuel.com/images/features/fog-lights.png?w=32&q=80'],
                            ['name' => 'Hands-free liftgate',      'icon' => 'https://static.overfuel.com/images/features/hands-free-liftgate.png?w=32&q=80'],
                            ['name' => 'Heated seats',             'icon' => 'https://static.overfuel.com/images/features/heated-seats.png?w=32&q=80'],
                            ['name' => 'Heated steering wheel',    'icon' => 'https://static.overfuel.com/images/features/heated-steering-wheel.png?w=32&q=80'],
                            ['name' => 'Keyless entry',            'icon' => 'https://static.overfuel.com/images/features/keyless-entry.png?w=32&q=80'],
                            ['name' => 'Lane departure warning',   'icon' => 'https://static.overfuel.com/images/features/lane-departure-warning.png?w=32&q=80'],
                            ['name' => 'Lane keep assist',         'icon' => 'https://static.overfuel.com/images/features/lane-keep-assist.png?w=32&q=80'],
                            ['name' => 'Leather seats',            'icon' => 'https://static.overfuel.com/images/features/leather-seats.png?w=32&q=80'],
                            ['name' => 'Memory seats',             'icon' => 'https://static.overfuel.com/images/features/memory-seats.png?w=32&q=80'],
                            ['name' => 'Navigation',               'icon' => 'https://static.overfuel.com/images/features/navigation.png?w=32&q=80'],
                            ['name' => 'Parking sensors/assist',   'icon' => 'https://static.overfuel.com/images/features/parking-sensors-assist.png?w=32&q=80'],
                            ['name' => 'Power seats',              'icon' => 'https://static.overfuel.com/images/features/power-seats.png?w=32&q=80'],
                            ['name' => 'Premium audio',            'icon' => 'https://static.overfuel.com/images/features/premium-audio.png?w=32&q=80'],
                            ['name' => 'Push start',               'icon' => 'https://static.overfuel.com/images/features/push-start.png?w=32&q=80'],
                            ['name' => 'Rain sensing wipers',      'icon' => 'https://static.overfuel.com/images/features/rain-sensing-wipers.png?w=32&q=80'],
                            ['name' => 'Rear A/C',                 'icon' => 'https://static.overfuel.com/images/features/rear-a-c.png?w=32&q=80'],
                            ['name' => 'Rear heated seats',        'icon' => 'https://static.overfuel.com/images/features/rear-heated-seats.png?w=32&q=80'],
                            ['name' => 'Remote engine start',      'icon' => 'https://static.overfuel.com/images/features/remote-engine-start.png?w=32&q=80'],
                            ['name' => 'Satellite radio ready',    'icon' => 'https://static.overfuel.com/images/features/satellite-radio-ready.png?w=32&q=80'],
                            ['name' => 'Side impact airbags',      'icon' => 'https://static.overfuel.com/images/features/side-impact-airbags.png?w=32&q=80'],
                            ['name' => 'Smart device remote start', 'icon' => 'https://static.overfuel.com/images/features/smart-device-remote-start.png?w=32&q=80'],
                            ['name' => 'Sunroof/moonroof',         'icon' => 'https://static.overfuel.com/images/features/sunroof-moonroof.png?w=32&q=80'],
                            ['name' => 'Third row seat',           'icon' => 'https://static.overfuel.com/images/features/third-row-seat.png?w=32&q=80'],
                            ['name' => 'Tow package',              'icon' => 'https://static.overfuel.com/images/features/tow-package.png?w=32&q=80'],
                            ['name' => 'Wifi hotspot',             'icon' => 'https://static.overfuel.com/images/features/wifi-hotspot.png?w=32&q=80'],
                            ['name' => 'Wireless phone charging',  'icon' => 'https://static.overfuel.com/images/features/wireless-phone-charging.png?w=32&q=80'],
                            ['name' => 'Xenon headlights',         'icon' => 'https://static.overfuel.com/images/features/xenon-headlights.png?w=32&q=80'],
                        ];
                    @endphp

                    <div class="sc-3c5deaa6-0 bUbZDx">
                        @foreach ($features as $feature)
                            <a class="d-inline-block me-2 mb-2 rounded border bg-white py-2 px-3 border-dark"
                               href="{{ route('frontend.inventory') }}?feature[]={{ urlencode($feature['name']) }}"
                               title="Browse {{ $feature['name'] }} inventory"
                               >
                                <img alt="{{ $feature['name'] }}" loading="lazy" width="15" height="15"
                                     class="opacity-50 tile-img d-inline-block me-2"
                                     src="{{ $feature['icon'] }}">
                                {{ $feature['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('page-scripts')
    <script>
        (function () {
            const modal = document.getElementById('modalSearch');
            if (!modal) return;

            const inventoryUrl = '{{ route('frontend.inventory') }}';

            modal.addEventListener('shown.bs.modal', function () {
                const input = document.getElementById('modalSearchInput');
                if (input) input.focus();
            });

            modal.addEventListener('keydown', function (e) {
                if (e.key !== 'Enter') return;

                const input = document.getElementById('modalSearchInput');
                const query = input ? input.value.trim() : '';

                if (!query) return;

                e.preventDefault();
                window.location.href = inventoryUrl + '?search=' + encodeURIComponent(query);
            });

            // Reset input when modal closes
            modal.addEventListener('hidden.bs.modal', function () {
                const input = document.getElementById('modalSearchInput');
                if (input) input.value = '';
            });
        }());
    </script>
@endpush
