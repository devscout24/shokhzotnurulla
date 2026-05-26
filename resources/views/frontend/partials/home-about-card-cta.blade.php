@php
    $homeAbout = $homeAboutCta ?? [];
    $about = data_get($homeAbout, 'about', []);
    $stats = data_get($homeAbout, 'stats', []);
    $card = data_get($homeAbout, 'card', []);
    $ctas = data_get($homeAbout, 'ctas', []);

    $aboutImageUrl = data_get($about, 'image_url');
    $aboutImageSrc = $aboutImageUrl
        ? (preg_match('/^https?:\\/\\//', $aboutImageUrl) ? $aboutImageUrl : url('/' . ltrim($aboutImageUrl, '/')))
        : asset('assets/frontend/img/angel-motors-top-rated-dealer-2.webp');

    $cardImageUrl = data_get($card, 'image_url');
    $cardImageSrc = $cardImageUrl
        ? (preg_match('/^https?:\\/\\//', $cardImageUrl) ? $cardImageUrl : url('/' . ltrim($cardImageUrl, '/')))
        : asset('assets/frontend/img/car-inspection.webp');

    $cta1 = $ctas[0] ?? [];
    $cta2 = $ctas[1] ?? [];

    $cta1Link = data_get($cta1, 'link_url') ?: '#';
    $cta2Link = data_get($cta2, 'link_url') ?: '#';
    $cta1Href = $cta1Link === '#' ? '#' : (preg_match('/^https?:\\/\\//', $cta1Link) ? $cta1Link : url($cta1Link));
    $cta2Href = $cta2Link === '#' ? '#' : (preg_match('/^https?:\\/\\//', $cta2Link) ? $cta2Link : url($cta2Link));
@endphp

<!-- about us section  -->
<div class="sc-1a7ba87f-0 cElement cContainer  container ">
    <div class="sc-24764b04-0 kdwMZF"> </div>
    <div class="cElement cColumnLayout  d-flex align-items-center row">
        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <img width="1200" height="772" src="{{ $aboutImageSrc }}" alt="{{ data_get($about, 'image_alt') }}"
                loading="lazy" fetchpriority="auto"
                class="cElement rounded-2xl cImage mb-2 mx-auto d-block  img-fluid">
            <div class="sc-24764b04-0 kdvwXG"></div>
        </div>

        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
            <div class="sc-1a7ba87f-0 cElement cContainer  container ">
                <div class="cElement cText eyebrow">
                    <p>{{ data_get($about, 'eyebrow') }}</p>
                </div>
                <h2 class="h2 text-start font-weight-bold" id="B8x2iRjmKT">{{ data_get($about, 'heading') }}</h2>
                <div class="sc-24764b04-0 kdvwXJ"></div>
                <p>{!! data_get($about, 'paragraphs.0') !!}</p>

                <p>{!! data_get($about, 'paragraphs.1') !!}</p>
            </div>
        </div>
    </div>

    <div class="sc-24764b04-0 kdvjHq"></div>
    <div class="cElement cColumnLayout row-bordered  row">
        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <div class="cElement cColumnLayout row-bordered  row">
                @for ($i = 0; $i < 2; $i++)
                    @php $stat = $stats[$i] ?? []; @endphp
                    <div class="cElement bottomBorder border-end  cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="{{ data_get($stat, 'icon') }} blue-large-text"></i>
                            </span>

                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center"><strong>{{ data_get($stat, 'title') }}</strong></p>
                        </div>
                        <p class="text-center">{{ data_get($stat, 'text') }}</p>
                    </div>
                @endfor
            </div>
        </div>

        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <div class="cElement cColumnLayout row-bordered  row">
                @for ($i = 2; $i < 4; $i++)
                    @php $stat = $stats[$i] ?? []; @endphp
                    <div class="cElement {{ $i === 2 ? 'bottomBorder border-end' : 'border-end' }} cColumn col-sm-6 col-12 order-sm-0 order-1">
                        <div class="sc-24764b04-0 kdvjHq"></div>
                        <div class="cElement cIcon text-center">
                            <span class="d-inline-block fa-fw h1 mt-0 mb-3">
                                <i class="{{ data_get($stat, 'icon') }} blue-large-text"></i>
                            </span>

                        </div>
                        <div class="cElement cText h4 font-weight-bold mb-n2" bis_skin_checked="1">
                            <p class="text-center"><strong>{{ data_get($stat, 'title') }}</strong></p>
                        </div>
                        <p class="text-center">{{ data_get($stat, 'text') }}</p>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="sc-24764b04-0 kdtJAz"></div>
</div>

<div class="sc-1a7ba87f-0 cElement cContainer  container card-section">
    <div class="cElement cColumnLayout  d-flex align-items-center row">
        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-2">
            <div class="sc-1a7ba87f-0 cElement cContainer  w-100 card2 h-100 border">
                <div class="cElement cIcon text-center d-flex justify-content-center">
                    <span class="d-inline-block fa-fw h1 primaryy">
                        <i class="{{ data_get($card, 'icon') }} font-55"></i>
                    </span>
                </div>

                <div class="sc-24764b04-0 kdvVWg"></div>

                <h2 class="text-center font-medium" id="eFw6ufiigV">{{ data_get($card, 'title') }}</h2>
                <div class="sc-24764b04-0 kdvVWg"></div>
                <p class="text-center">{{ data_get($card, 'text') }}</p>
            </div>
        </div>

        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <img width="100%" height="703" src="{{ $cardImageSrc }}" alt="{{ data_get($card, 'image_alt') }}" loading="lazy"
                fetchpriority="auto"
                class="cElement cImage mb-2 mx-auto d-block rounded-2xl img-fluid mb-0 border img-fluid">
        </div>
    </div>
</div>

<div class="sc-1a7ba87f-0 cElement cContainer  container gradient">
    <div class="sc-24764b04-0 kdvVWh"></div>
    <div class="cElement cColumnLayout  row">
        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <a href="{{ $cta1Href }}"
                class="sc-1a7ba87f-0 bwwxTl cElement cContainer  cursor-pointer  w-100 cursor-pointer text-white rounded border overflow-hidden d-block text-decoration-none">
                <div class=" cElement cColorOverlay bg-black"></div>
                <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                    <div class="sc-24764b04-0 evgXRR"></div>
                    <h3 class="h2  text-start  extrabold " id="rln4nJ9ffJ">{{ data_get($cta1, 'title') }}</h3>
                    <div class="sc-24764b04-0 kdvwXJ"></div>

                    <p>{{ data_get($cta1, 'text') }}</p>
                    <div class="sc-24764b04-0 kdvwXJ"></div>

                </div>
            </a>
            <div class="sc-24764b04-0 kdvjHt"></div>
        </div>

        <div class="cElement cColumn col-sm-6 col-12 order-sm-0 order-1">
            <a href="{{ $cta2Href }}"
                class="sc-1a7ba87f-0 cbWkvM cElement cContainer  cursor-pointer  w-100 cursor-pointer text-white rounded border overflow-hidden d-block text-decoration-none">
                <div class=" cElement cColorOverlay bg-black"></div>
                <div class="sc-1a7ba87f-0 hAgfhi cElement cContainer  container ">
                    <div class="sc-24764b04-0 evgXRR"></div>
                    <h3 class="h2 text-start extrabold " id="rln4nJ9ffJ">{{ data_get($cta2, 'title') }}</h3>
                    <div class="sc-24764b04-0 kdvwXJ"></div>

                    <p>{{ data_get($cta2, 'text') }}</p>
                    <div class="sc-24764b04-0 kdvwXJ"></div>

                </div>
            </a>
            <div class="sc-24764b04-0 kdvjHt"></div>
        </div>
    </div>
    <div class=" kduiyY"></div>
</div>

<div class=""></div>
