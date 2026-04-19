@php
    $loc        = $locationMenuData[0] ?? null;
    $phones     = $loc['phones'] ?? [];

    $mainPhone    = collect($phones)->firstWhere('type', 'sales')
                 ?? collect($phones)->firstWhere('type', 'main')
                 ?? collect($phones)->first();
    $servicePhone = collect($phones)->firstWhere('type', 'service')
                 ?? $mainPhone;

    $mainPhoneNumber    = $mainPhone    ? $mainPhone['number']    : '(615) 267-0590';
    $servicePhoneNumber = $servicePhone ? $servicePhone['number'] : '(615) 267-0590';
    $mainPhoneRaw       = preg_replace('/\D/', '', $mainPhoneNumber);
    $servicePhoneRaw    = preg_replace('/\D/', '', $servicePhoneNumber);

    $street1    = $loc['street1']    ?? '1339 South Lowry Street';
    $city       = $loc['city']       ?? 'Smyrna';
    $state      = $loc['state']      ?? 'TN';
    $postalcode = $loc['postalcode'] ?? '37167';
    $mapUrl     = !empty($loc['map_override'])
        ? $loc['map_override']
        : 'https://www.google.com/maps/search/' . urlencode(
            implode(',', array_filter([$dealerName ?: 'Angel Motors Inc', $street1, $city, $state . ' ' . $postalcode]))
          );

    $name = $dealerName ?: config('app.name', 'Angel Motors Inc');

    // Social links — only show platforms that have a value
    $socials = $dealerSocialLinks ?? [];
    $socialPlatforms = [
        'facebook'  => ['label' => 'Facebook',  'icon' => 'facebook.png'],
        'youtube'   => ['label' => 'Youtube',   'icon' => 'youtube.png'],
        'instagram' => ['label' => 'Instagram', 'icon' => 'instagram.png'],
        'tiktok'    => ['label' => 'Tiktok',    'icon' => 'tiktok.png'],
        'twitter'   => ['label' => 'Twitter',   'icon' => 'twitter.png'],
        'pinterest' => ['label' => 'Pinterest', 'icon' => 'pinterest.png'],
        'linkedin'  => ['label' => 'LinkedIn',  'icon' => 'linkedin.png'],
    ];
@endphp

<footer class="bg-footer">
    <div class="bg-secondary-light text-center text-sm-start text-white py-3">
        <div class="container">
            <div class="row">
                <div class="row">
                    <div class="align-middle text-large mb-3 mb-sm-0 notranslate col-sm-6">
                        {{ $name }}
                    </div>
                    <div class="text-center text-sm-end text-muted text-large col-sm-6">
                        @foreach ($socialPlatforms as $platform => $meta)
                            @if (!empty($socials[$platform]))
                                <a href="{{ $socials[$platform] }}" class="text-white" target="_blank"
                                   title="{{ $name }} on {{ $meta['label'] }}">
                                    <img alt="{{ $name }} on {{ $meta['label'] }}"
                                         loading="lazy" width="30" height="30"
                                         decoding="async"
                                         class="ms-3 fill-white"
                                         src="{{ asset('assets/frontend/img/' . $meta['icon']) }}">
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center text-sm-start text-white py-4">
        <div class="container">
            <div class="row">

                {{-- Location --}}
                <div class="mb-4 mb-sm-0 col-sm-4">
                    <div class="h5 border-bottom border-theme border-thick d-inline-block pb-3">Location</div>
                    <div>
                        <div class="row">
                            <div class="col-lg-12 col-12">
                                <div class="vcard mb-4">
                                    <a class="adr text-white" target="_blank"
                                       title="View {{ $street1 }} on Google Maps"
                                       href="{{ $mapUrl }}">
                                        <b class="notranslate">{{ $name }}</b><br>
                                        <span class="street-address">{{ $street1 }}</span><br>
                                        <span class="locality">{{ $city }}</span>,
                                        <span class="region">{{ $state }}</span>
                                        <span class="postal-code">{{ $postalcode }}</span><br>
                                    </a>

                                    <a href="tel:+1{{ $mainPhoneRaw }}"
                                       data-cy="footer-phone-sales"
                                       class="tel text-white d-block mt-1 mb-1">
                                        <span class="d-inline-block me-2 ms-1 text-white">
                                            <i class="fa-solid fa-phone"></i>
                                        </span>
                                        Main: {{ $mainPhoneNumber }}
                                    </a>

                                    <a href="tel:+1{{ $servicePhoneRaw }}"
                                       data-cy="footer-phone-service"
                                       class="tel text-white d-block mt-1 mb-1">
                                        <span class="d-inline-block me-2 ms-1 text-white">
                                            <i class="fa-solid fa-screwdriver-wrench font-base"></i>
                                        </span>
                                        Service: {{ $servicePhoneNumber }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="mb-4 mb-sm-0 col-sm-4">
                    <div class="h5 border-bottom border-theme border-thick d-inline-block pb-3">
                        Quick Links
                    </div>
                    <nav class="row" role="navigation" aria-label="Footer">
                        @foreach ($footerMenu as $menu)
                            <div class="col-lg-6 col-12">
                                <a href="{{ url($menu->url) }}" target="{{ $menu->target }}"
                                   class="text-white d-block py-3 py-sm-1">
                                    {{ $menu->label }}
                                </a>
                            </div>
                        @endforeach
                    </nav>
                </div>

                {{-- Stay Updated --}}
                <div class="mb-4 mb-sm-0 col-sm-4 col-12">
                    <div class="h5 border-bottom border-theme border-thick d-inline-block pb-3">
                        Stay Updated
                    </div>
                    <p>Get special offers directly to your inbox.</p>
                    <div class="rounded p-4 bg-secondary-light">
                        <div class="mb-0 row">
                            <div class="col-sm-6">
                                <div>
                                    <label class="d-none form-label">First name</label>
                                    <input type="text" name="firstname"
                                           class="form-control-inverted mb-4 form-control"
                                           placeholder="First" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div>
                                    <label class="d-none form-label">Last name</label>
                                    <input type="text" name="lastname"
                                           class="form-control-inverted mb-4 form-control"
                                           placeholder="Last" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 mb-md-4">
                            <input type="email" id="email" name="email"
                                   class="form-control form-control-inverted"
                                   placeholder="you@email.com"
                                   aria-label="Your email"
                                   autocomplete="email"
                                   data-cy="formcontrol-email">
                        </div>
                        <button type="button" class="w-100 btn btn-primary">
                            Sign Up
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="poweredby" class="bg-secondary-light text-center p-4 text-white">
        <div class="opacity-75">
            <a href="https://www.angelmotorsinc.com" target="_blank" class="text-white" title="Visit {{ $dealerName ?? config('app.name') }}">
                Powered by <u>angelmotorsinc.com</u>, the fastest and most reliable mobile-first websites for
                dealerships.
                {{-- <br>
                <img src="{{ asset('assets/frontend/img/overfuel-webp.webp') }}"
                     alt="Powered by overfuel.com" width="135" height="30"
                     loading="lazy" class="mt-3"> --}}
            </a>
        </div>
    </div>
</footer>
