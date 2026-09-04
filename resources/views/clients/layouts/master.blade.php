<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    @include('clients.components.head')
    <title>@yield('title', 'Thiết Bị Tự Động Hóa Công Nghiệp - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))</title>
    @stack('meta')
    @yield('styles')
    @stack('styles')
</head>

<body>
    {{-- Google Tag Manager Body --}}
    @if (!empty($settings->google_analytics))
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $settings->google_analytics }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif
    @include('clients.components.header')
        <main>
            @yield('content')
        </main>
        @include('clients.components.footer')
        @include('clients.components.foot')
        @yield('scripts')
        @stack('scripts')
</body>

</html>
