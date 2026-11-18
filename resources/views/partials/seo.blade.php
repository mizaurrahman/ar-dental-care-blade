@php
    $appName = config('app.name', 'AR Dental Care');
    $decode = static fn (string $value): string => html_entity_decode(trim($value), ENT_QUOTES, 'UTF-8');
    $seoTitle = $decode($__env->yieldContent('title', $appName));
    $seoDescription = $decode($__env->yieldContent('meta_description', $appName.' — book dental appointments and manage clinic care online.'));
    $seoRobots = $decode($__env->yieldContent('meta_robots', 'index,follow'));
    $seoCanonical = $decode($__env->yieldContent('canonical', url()->current()));
    $seoImage = $decode($__env->yieldContent('og_image', asset('images/ar-dental-logo.svg')));
    $seoType = $decode($__env->yieldContent('og_type', 'website'));
@endphp

<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">
<meta name="theme-color" content="#2f6fed">
<meta name="application-name" content="{{ $appName }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $appName }}">
<meta name="format-detection" content="telephone=yes">

<meta property="og:site_name" content="{{ $appName }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
<meta property="og:locale" content="en_US">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">
<meta name="twitter:image" content="{{ $seoImage }}">

<link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ $seoImage }}">

@hasSection('json_ld')
    <script type="application/ld+json">{!! trim($__env->yieldContent('json_ld')) !!}</script>
@endif
