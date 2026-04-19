@php
    /*
    |--------------------------------------------------------------------------
    | Safe SEO Initialization (Config Driven)
    |--------------------------------------------------------------------------
    */

    $seo = (isset($seo) && is_array($seo)) ? $seo : [];

    $defaults = config('seo.defaults');

    $pageTitle = View::getSection('title');

    $title = !empty($seo['title'])
        ? $seo['title']
        : ($pageTitle ?: $defaults['title']);

    $description = !empty($seo['description'])
        ? $seo['description']
        : $defaults['description'];

    $keywords = $seo['keywords'] ?? $defaults['keywords'];

    $image = !empty($seo['image'])
        ? $seo['image']
        : asset($defaults['image']);

    $url = !empty($seo['url'])
        ? $seo['url']
        : url()->current();

    $robots = !empty($seo['robots'])
        ? $seo['robots']
        : $defaults['robots'];

    $type = !empty($seo['type'])
        ? $seo['type']
        : $defaults['type'];

    /*
    |--------------------------------------------------------------------------
    | Safe Schema Handling
    |--------------------------------------------------------------------------
    */

    $schemaJson = null;

    if (!empty($seo['schema']) && (is_array($seo['schema']) || is_object($seo['schema']))) {
        $schemaJson = json_encode(
            $seo['schema'],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
@endphp


<title>{{ $title }}</title>

<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="robots" content="{{ $robots }}">

<link rel="canonical" href="{{ $url }}">

<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:type" content="{{ $type }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

@if($schemaJson)
<script type="application/ld+json">
{!! $schemaJson !!}
</script>
@endif