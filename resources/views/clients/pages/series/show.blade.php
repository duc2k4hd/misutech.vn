@extends('clients.layouts.master')

@php
    $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
    $rawSeriesTitle = $series->meta_title ?: ($series->name . ' Chính Hãng & Báo Giá');
    $cleanSeriesTitle = preg_replace('/(\s*[\-\|]\s*(MISUTECH|Công ty cổ phần Misutech)).*$/iu', '', $rawSeriesTitle);
    $finalSeriesTitle = $cleanSeriesTitle . ' - ' . $companyName;
@endphp

@section('title', $finalSeriesTitle)

@push('meta')
    @php
        $pageTitle = $finalSeriesTitle;
        $pageDesc = $series->meta_description ?: ($series->description ?: ('Khám phá dòng sản phẩm ' . $series->name . ' chính hãng tại MISUTECH. Đầy đủ các model, thông số kỹ thuật, tài liệu Catalog PDF và báo giá tốt nhất.'));
        $pageUrl = route('series.show', $series->slug);

        // Schema CollectionPage & ItemList
        $itemList = [];
        foreach ($products as $idx => $prod) {
            $item = [
                '@type' => 'Product',
                'name' => $prod->name,
                'url' => route('product.show', $prod->slug),
                'image' => $prod->thumbnailMedia->first()?->url ?? $seriesThumbnail,
                'offers' => [
                    '@type' => 'Offer',
                    'priceCurrency' => 'VND',
                    'price' => (string) ($prod->sale_price && $prod->sale_price < $prod->price ? $prod->sale_price : $prod->price),
                    'availability' => $prod->status === 'active' ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                ]
            ];
            if ($prod->sku) {
                $item['sku'] = $prod->sku;
            }
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'item' => $item,
            ];
        }

        $collectionSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $series->name,
            'headline' => $series->name,
            'description' => $pageDesc,
            'url' => $pageUrl,
            'image' => $seriesThumbnail,
            'mainEntity' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($products),
                'itemListElement' => $itemList,
            ]
        ];
        if ($series->brand) {
            $collectionSchema['brand'] = [
                '@type' => 'Brand',
                'name' => $series->brand->name,
            ];
        }

        // Schema Breadcrumbs
        $breadcrumbItems = [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Trang chủ',
                'item' => route('home.index'),
            ]
        ];
        $pos = 2;
        if ($categoryBreadcrumbs->isNotEmpty()) {
            foreach ($categoryBreadcrumbs as $catItem) {
                $breadcrumbItems[] = [
                    '@type' => 'ListItem',
                    'position' => $pos++,
                    'name' => $catItem->name,
                    'item' => route('categories.show', $catItem->slug),
                ];
            }
        }
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos,
            'name' => $series->name,
            'item' => $pageUrl,
        ];

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbItems,
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
    <meta property="og:image" content="{{ $seriesThumbnail }}">
    <meta property="og:image:alt" content="{{ $series->name }}">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $seriesThumbnail }}">

    {{-- JSON-LD Schema Scripts --}}
    <script type="application/ld+json">
    {!! json_encode($collectionSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/series.css') }}">
@endpush

@section('content')
    <div class="misutech_series_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_series_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_series_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    @if($categoryBreadcrumbs->isNotEmpty())
                        @foreach($categoryBreadcrumbs as $cat)
                            <li class="separator">»</li>
                            <li><a href="{{ route('categories.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                        @endforeach
                    @endif
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">{{ $series->name }}</li>
                </ol>
            </div>
        </nav>

        {{-- Header Series Tinh Gọn --}}
        <section class="misutech_series_header_section">
            <div class="misutech_home_container">
                <div class="misutech_series_header_card">
                    <div class="misutech_series_header_left">
                        <div class="misutech_series_tags">
                            @if($series->brand)
                                <a href="{{ route('shop.index', ['brands' => $series->brand->slug]) }}" class="misutech_series_tag_item">
                                    Thương hiệu: <b>{{ $series->brand->name }}</b>
                                </a>
                            @endif
                            @if($series->category)
                                <a href="{{ route('categories.show', $series->category->slug) }}" class="misutech_series_tag_item">
                                    Danh mục: <b>{{ $series->category->name }}</b>
                                </a>
                            @endif
                            <span class="misutech_series_tag_item count_tag">
                                <b>{{ $products->count() }}</b> Model
                            </span>
                        </div>

                        <h1 class="misutech_series_main_title">{{ $series->name }}</h1>

                        @if($series->description)
                            <p class="misutech_series_desc_text">{{ $series->description }}</p>
                        @endif

                        <div class="misutech_series_price_info">
                            <span class="price_label">Khoảng giá dòng sản phẩm:</span>
                            <strong class="price_range">
                                @if($minPrice && $maxPrice)
                                    @if($minPrice == $maxPrice)
                                        {{ number_format($minPrice, 0, ',', '.') }}đ
                                    @else
                                        {{ number_format($minPrice, 0, ',', '.') }}đ ~ {{ number_format($maxPrice, 0, ',', '.') }}đ
                                    @endif
                                @else
                                    Liên hệ báo giá
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="misutech_series_header_right">
                        <div class="misutech_series_thumb_box">
                            <img src="{{ $seriesThumbnail }}" alt="{{ $series->name }}" class="misutech_series_thumb_img" loading="eager">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Danh sách Model thuộc Series --}}
        <section class="misutech_series_models_section" id="series_models">
            <div class="misutech_home_container">
                <div class="misutech_series_toolbar">
                    <div class="misutech_series_toolbar_title">
                        <h2>Danh sách Model thuộc dòng {{ $series->name }} <span class="models_badge">({{ $products->count() }})</span></h2>
                    </div>

                    {{-- Sort Dropdown --}}
                    <div class="misutech_series_sort_filter">
                        <label for="series_sort_select">Sắp xếp:</label>
                        <select id="series_sort_select" onchange="location = this.value;">
                            <option value="{{ route('series.show', ['slug' => $series->slug, 'sort' => 'name_asc']) }}" {{ $sort === 'name_asc' ? 'selected' : '' }}>Tên Model: A → Z</option>
                            <option value="{{ route('series.show', ['slug' => $series->slug, 'sort' => 'name_desc']) }}" {{ $sort === 'name_desc' ? 'selected' : '' }}>Tên Model: Z → A</option>
                            <option value="{{ route('series.show', ['slug' => $series->slug, 'sort' => 'price_asc']) }}" {{ $sort === 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                            <option value="{{ route('series.show', ['slug' => $series->slug, 'sort' => 'price_desc']) }}" {{ $sort === 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                @if($products->isEmpty())
                    <div class="misutech_series_empty_box">
                        <p>Hiện chưa có model sản phẩm nào trong dòng này.</p>
                    </div>
                @else
                    <div class="misutech_series_grid">
                        @foreach($products as $prod)
                            @php
                                $thumb = $prod->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png');
                                $currentPrice = $prod->sale_price && $prod->sale_price < $prod->price ? $prod->sale_price : $prod->price;
                                $hasSale = $prod->sale_price && $prod->sale_price < $prod->price;
                            @endphp
                            <article class="misutech_series_card" 
                                     data-name="{{ $prod->name }}" 
                                     data-product-name="{{ $prod->name }}" 
                                     data-price="{{ $currentPrice }}">
                                <div class="misutech_series_card_media">
                                    @if($hasSale && $prod->price > 0)
                                        <span class="misutech_series_card_sale">-{{ round((1 - $prod->sale_price / $prod->price) * 100) }}%</span>
                                    @endif
                                    <a href="{{ route('product.show', $prod->slug) }}" class="misutech_series_card_link" title="{{ $prod->name }}">
                                        <img src="{{ $thumb }}" alt="{{ $prod->name }}" loading="lazy" decoding="async">
                                    </a>
                                    <div class="misutech_series_card_actions">
                                        <button class="misutech_series_card_btn_cart" type="button"
                                                data-cart data-product-id="{{ $prod->id }}"
                                                aria-label="Thêm vào giỏ">＋</button>
                                    </div>
                                </div>

                                <div class="misutech_series_card_body">
                                    @if($prod->sku)
                                        <span class="misutech_series_card_sku">{{ $prod->sku }}</span>
                                    @endif
                                    <h3 class="misutech_series_card_name" title="{{ $prod->name }}">
                                        <a href="{{ route('product.show', $prod->slug) }}">{{ $prod->name }}</a>
                                    </h3>

                                    <div class="misutech_series_card_price">
                                        @if($prod->price > 0)
                                            @if($hasSale)
                                                <strong class="price_current">{{ number_format($prod->sale_price, 0, ',', '.') }}đ</strong>
                                                <span class="price_old">{{ number_format($prod->price, 0, ',', '.') }}đ</span>
                                            @else
                                                <strong class="price_current">{{ number_format($prod->price, 0, ',', '.') }}đ</strong>
                                            @endif
                                        @else
                                            <strong class="price_contact">Liên hệ</strong>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        {{-- Nội dung giới thiệu chi tiết (Rich Content) --}}
        @if($series->content)
            <section class="misutech_series_article_section">
                <div class="misutech_home_container">
                    <div class="misutech_series_article_card">
                        <div class="misutech_series_article_header">
                            <h2>Tổng quan & Đặc điểm nổi bật dòng {{ $series->name }}</h2>
                        </div>
                        <div class="misutech_series_article_body">
                            {!! $series->content !!}
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Tài liệu kỹ thuật / Catalog --}}
        @if($allCatalogs->isNotEmpty())
            <section class="misutech_series_docs_section" id="series_catalogs">
                <div class="misutech_home_container">
                    <div class="misutech_series_docs_card">
                        <div class="misutech_series_docs_header">
                            <h2>Tài liệu kỹ thuật & Catalog ({{ $allCatalogs->count() }})</h2>
                        </div>
                        <div class="misutech_series_docs_list">
                            @foreach($allCatalogs as $catDoc)
                                <div class="misutech_series_doc_item">
                                    <span class="doc_icon">📄</span>
                                    <div class="doc_meta">
                                        <strong class="doc_title">{{ $catDoc->filename }}</strong>
                                        <span class="doc_ref">Model: {{ $catDoc->product_name }}</span>
                                    </div>
                                    <div class="doc_btns">
                                        <a href="{{ $catDoc->url }}" target="_blank" rel="noopener noreferrer" class="doc_btn view_btn">
                                            Xem PDF ↗
                                        </a>
                                        <a href="{{ $catDoc->url }}" download class="doc_btn download_btn">
                                            Tải về ⤓
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Dòng sản phẩm liên quan --}}
        @if($relatedSeries->isNotEmpty())
            <section class="misutech_series_related_section">
                <div class="misutech_home_container">
                    <div class="misutech_series_related_header">
                        <h2>Dòng sản phẩm cùng nhóm</h2>
                    </div>
                    <div class="misutech_series_related_grid">
                        @foreach($relatedSeries as $rel)
                            <a href="{{ route('series.show', $rel->slug) }}" class="misutech_series_related_item">
                                <div class="rel_info">
                                    <strong class="rel_name">{{ $rel->name }}</strong>
                                    <span class="rel_desc">{{ $rel->brand?->name ?? 'MISUTECH' }} • {{ $rel->products_count }} Model</span>
                                </div>
                                <span class="rel_arrow">→</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection
