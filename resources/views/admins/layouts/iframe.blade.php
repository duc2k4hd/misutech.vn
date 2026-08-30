<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', 'Media Picker')</title>
    <!-- Favicon icon -->
    @php
        $faviconUrl = !empty($settings->site_favicon) 
            ? (Str::startsWith($settings->site_favicon, ['http://', 'https://']) ? $settings->site_favicon : asset('storage/clients/imgs/settings/' . $settings->site_favicon))
            : asset('storage/clients/imgs/settings/favicon.png');
    @endphp
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link href="{{ asset('admins/css/style.css') }}" rel="stylesheet">

    <!-- Common Admin Vendor Styles -->
    <link rel="stylesheet" href="{{ asset('admins/vendor/toastr/css/toastr.min.css') }}">
    <link href="{{ asset('admins/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/2.8.2/slimselect.min.css" rel="stylesheet">

    @yield('styles')
    @stack('styles')
    <style>
        /* Override default admin body styles for iframe */
        body {
            margin: 0;
            padding: 0;
            background-color: var(--mm-bg, #f1f5f9);
            font-family: 'Inter', system-ui, sans-serif;
        }
        /* Hide preloader or any layout remnants */
        #preloader { display: none !important; }
        .mm-root { height: 100vh !important; }
    </style>
</head>
<body>

    @yield('content')

    <!-- Required vendors -->
    <script src="{{ asset('admins/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admins/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('admins/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
