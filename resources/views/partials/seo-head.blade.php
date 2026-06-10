@php
    $seo = $seoMeta ?? \App\Support\SeoMeta::defaults();
@endphp

<title>{{ $seo->fullTitle() }}</title>
<meta name="description" content="{{ Str::limit($seo->description, 160) }}">
@if ($seo->keywords)
    <meta name="keywords" content="{{ $seo->keywords }}">
@endif
<meta name="robots" content="{{ $seo->robotsContent() }}">
<meta name="author" content="{{ config('seo.site_name') }}">
<link rel="canonical" href="{{ $seo->canonical ?? url()->current() }}">

<meta property="og:locale" content="{{ config('seo.locale') }}">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:site_name" content="{{ config('seo.site_name') }}">
<meta property="og:title" content="{{ $seo->title ?? config('seo.default_title') }}">
<meta property="og:description" content="{{ Str::limit($seo->description, 200) }}">
<meta property="og:url" content="{{ $seo->canonical ?? url()->current() }}">
@if ($seo->image)
    <meta property="og:image" content="{{ $seo->image }}">
    <meta property="og:image:alt" content="{{ $seo->title ?? config('seo.site_name') }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seo->title ?? config('seo.default_title') }}">
<meta name="twitter:description" content="{{ Str::limit($seo->description, 200) }}">
@if ($seo->image)
    <meta name="twitter:image" content="{{ $seo->image }}">
@endif
@if (config('seo.twitter_handle'))
    <meta name="twitter:site" content="@{{ ltrim(config('seo.twitter_handle'), '@') }}">
@endif

<meta name="theme-color" content="#2563eb">
<meta name="application-name" content="{{ config('seo.site_name') }}">

@if (!empty($seo->jsonLd))
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => $seo->jsonLd,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endif
