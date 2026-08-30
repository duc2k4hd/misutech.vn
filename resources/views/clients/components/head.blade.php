<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="author" content="{{ $settings->company ?? ($settings->name ?? 'MISUTECH') }}">

{{-- Security Headers --}}
<meta http-equiv="X-Content-Type-Options" content="nosniff">
<meta http-equiv="X-XSS-Protection" content="1; mode=block">
<meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">

{{-- Favicons & App Icons --}}
@php
    $faviconUrl = !empty($settings->site_favicon) 
        ? (Str::startsWith($settings->site_favicon, ['http://', 'https://']) ? $settings->site_favicon : asset('storage/clients/imgs/settings/' . $settings->site_favicon))
        : asset('storage/clients/imgs/settings/favicon.png');
@endphp
<link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
<link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
<meta name="theme-color" content="#003b70">

{{-- DNS Preconnect Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

{{-- Core Style CSS Preload --}}
<link rel="preload" href="{{ asset('clients/css/style.css') }}" as="style">
<link rel="stylesheet" href="{{ asset('clients/css/style.css') }}">

{{-- Google Analytics (Tự động nạp khi nhập mã trong Admin Settings) --}}
@if(!empty($settings->google_analytics))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->google_analytics }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $settings->google_analytics }}');
    </script>
@endif
