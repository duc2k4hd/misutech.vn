<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
<meta name="author" content="{{ $settings->company ?? ($settings->name ?? 'MISUTECH') }}">

{{-- Security Headers --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

{{-- Favicons & App Icons --}}
@php
    $faviconUrl = !empty($settings->site_favicon)
        ? (Str::startsWith($settings->site_favicon, ['http://', 'https://'])
            ? $settings->site_favicon
            : asset('storage/clients/imgs/settings/' . $settings->site_favicon))
        : asset('storage/clients/imgs/settings/favicon.png');
@endphp
<link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
<link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
<meta name="theme-color" content="#003b70">

{{-- DNS Prefetch & Preconnect for High Performance --}}
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://fonts.gstatic.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" media="print" onload="this.media='all'">
<noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap">
</noscript>

{{-- Core Style CSS Preload with Filemtime Caching --}}
@php
    $styleVersion = file_exists(public_path('clients/css/style.css')) ? filemtime(public_path('clients/css/style.css')) : '1.0';
@endphp
<link rel="preload" href="{{ asset('clients/css/style.css?v=' . $styleVersion) }}" as="style">
<link rel="stylesheet" href="{{ asset('clients/css/style.css?v=' . $styleVersion) }}">

{{-- Google Analytics & Tag Manager (Deferred Non-blocking Loader for 100/100 PageSpeed) --}}
@if (!empty($settings->google_analytics))
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        function loadGTM() {
            if (window._gtmLoaded) return;
            window._gtmLoaded = true;
            var f = document.getElementsByTagName('script')[0],
                j = document.createElement('script');
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id={{ $settings->google_analytics }}';
            f.parentNode.insertBefore(j, f);
            dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
        }
        if ('requestIdleCallback' in window) {
            window.addEventListener('load', function() {
                requestIdleCallback(loadGTM, { timeout: 2000 });
            });
        } else {
            window.addEventListener('load', function() {
                setTimeout(loadGTM, 1500);
            });
        }
        ['touchstart', 'scroll', 'mousemove', 'keydown'].forEach(function(evt) {
            window.addEventListener(evt, loadGTM, { once: true, passive: true });
        });
    </script>
@endif

