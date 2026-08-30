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
