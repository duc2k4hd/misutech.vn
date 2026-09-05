@extends('clients.layouts.master')

@section('title', 'Dòng Sản Phẩm (Series) - ' . ($settings->company ?? ($settings->name ?? 'MISUTECH')))

@push('meta')
    @php
        $pageTitle = 'Dòng Sản Phẩm (Series) - ' . ($settings->company ?? ($settings->name ?? 'MISUTECH'));
        $pageUrl = route('series.index');
        $pageDesc = 'Tổng hợp các dòng sản phẩm (Series) thiết bị tự động hóa công nghiệp chính hãng: Biến tần, PLC, HMI, Cảm biến tại ' . ($settings->company ?? ($settings->name ?? 'MISUTECH')) . '.';
        $pageImage = !empty($settings->og_image) 
            ? (Str::startsWith($settings->og_image, ['http://', 'https://']) ? $settings->og_image : asset('storage/clients/imgs/settings/' . $settings->og_image)) 
            : asset('storage/clients/imgs/settings/banner-seo-misutech.jpg');

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang chủ',
                    'item' => route('home.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Series',
                    'item' => $pageUrl,
                ]
            ],
        ];
    @endphp

    <meta name="description" content="{{ $pageDesc }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:site_name" content="MISUTECH">
    <meta property="og:image" content="{{ $pageImage }}">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $pageImage }}">

    {{-- JSON-LD Schema --}}
    <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    @php
        $seriesCssVersion = file_exists(public_path('clients/css/series.css')) ? filemtime(public_path('clients/css/series.css')) : '1.0';
    @endphp
    <link rel="stylesheet" href="{{ asset('clients/css/series.css?v=' . $seriesCssVersion) }}">
@endpush

@section('content')
    <div class="misutech_series_hub_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_series_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_series_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">Series (Dòng sản phẩm)</li>
                </ol>
            </div>
        </nav>

        {{-- Hub Header --}}
        <div class="misutech_series_hub_header">
            <div class="misutech_home_container">
                <h1 class="misutech_series_hub_title">DÒNG SẢN PHẨM (SERIES)</h1>
                <p class="misutech_series_hub_subtitle">Tra cứu các dòng sản phẩm tự động hóa, thông số kỹ thuật và tài liệu catalog chi tiết.</p>
            </div>
        </div>

        <div class="misutech_home_container">
            {{-- Search & Filter Bar --}}
            <div class="misutech_series_hub_filter_wrap">
                <form action="{{ route('series.index') }}" method="GET" class="misutech_series_hub_form">
                    <input type="text" name="q" value="{{ $search }}" class="misutech_series_hub_input" placeholder="Tìm kiếm tên dòng sản phẩm (Series)...">
                    
                    <select name="brand" class="misutech_series_hub_select" onchange="this.form.submit()">
                        <option value="">-- Tất cả Thương hiệu --</option>
                        @if(isset($brands) && is_iterable($brands))
                            @foreach($brands as $b)
                                @php
                                    $bSlug = is_object($b) ? ($b->slug ?? '') : ($b['slug'] ?? '');
                                    $bName = is_object($b) ? ($b->name ?? '') : ($b['name'] ?? '');
                                @endphp
                                @if($bSlug)
                                    <option value="{{ $bSlug }}" {{ $brandSlug == $bSlug ? 'selected' : '' }}>{{ $bName }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>

                    <select name="category" class="misutech_series_hub_select" onchange="this.form.submit()">
                        <option value="">-- Tất cả Danh mục --</option>
                        @if(isset($categories) && is_iterable($categories))
                            @foreach($categories as $c)
                                @php
                                    $cSlug = is_object($c) ? ($c->slug ?? '') : ($c['slug'] ?? '');
                                    $cName = is_object($c) ? ($c->name ?? '') : ($c['name'] ?? '');
                                @endphp
                                @if($cSlug)
                                    <option value="{{ $cSlug }}" {{ $catSlug == $cSlug ? 'selected' : '' }}>{{ $cName }}</option>
                                @endif
                            @endforeach
                        @endif
                    </select>

                    <button type="submit" class="misutech_series_hub_btn">
                        Lọc
                    </button>

                    @if(!empty($search) || !empty($brandSlug) || !empty($catSlug))
                        <a href="{{ route('series.index') }}" class="misutech_series_hub_reset_btn" title="Xóa bộ lọc">
                            ✕ Xóa lọc
                        </a>
                    @endif
                </form>
            </div>

            {{-- Series Grid --}}
            @if($seriesList->isNotEmpty())
                <div class="misutech_series_hub_grid">
                    @foreach($seriesList as $ser)
                        <a href="{{ route('series.show', $ser->slug) }}" class="misutech_series_hub_card">
                            <div>
                                <div class="misutech_series_hub_card_head">
                                    <span class="misutech_series_hub_card_brand">{{ $ser->brand?->name ?? 'CHÍNH HÃNG' }}</span>
                                    <span class="misutech_series_hub_card_count">{{ $ser->products_count }} Model</span>
                                </div>
                                <h2 class="misutech_series_hub_card_name">{{ $ser->name }}</h2>
                                @if($ser->description)
                                    <p class="misutech_series_hub_card_desc">{{ $ser->description }}</p>
                                @endif
                            </div>
                            <div class="misutech_series_hub_card_foot">
                                <span>Xem chi tiết dòng SP</span>
                                <span>›</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="misutech_pagination_nav">
                    {{ $seriesList->links('clients.components.pagination') }}
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; background: #ffffff; border: 1px solid var(--misutech_home_line, #dbe7ed); margin-top: 10px;">
                    <p style="color: var(--misutech_home_muted, #6d7d87); font-size: 13.5px; margin: 0 0 12px;">Không tìm thấy dòng sản phẩm (Series) nào phù hợp với điều kiện tìm kiếm.</p>
                    <a href="{{ route('series.index') }}" class="misutech_series_hub_btn" style="text-decoration: none; display: inline-flex;">Xem tất cả Series</a>
                </div>
            @endif
        </div>
    </div>
@endsection
