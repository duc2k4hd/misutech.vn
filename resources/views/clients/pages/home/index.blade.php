@extends('clients.layouts.master')

@php
    $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
    $homeTitle = !empty($settings->meta_title) 
        ? (str_contains($settings->meta_title, $companyName) ? $settings->meta_title : ($settings->meta_title . ' - ' . $companyName)) 
        : ('Nhà Phân Phối Thiết Bị Tự Động Hóa Chính Hãng - ' . $companyName);
@endphp

@section('title', $homeTitle)

@push('meta')
    @php
        $siteTitle = $homeTitle;
        $siteDesc = $settings->meta_description ?? ' – Đơn vị cung cấp giải pháp và thiết bị tự động hóa công nghiệp hàng đầu Việt Nam: Biến tần, PLC, Cảm biến, Màn hình HMI, Khởi động mềm, Servo chính hãng.';
        $siteKeywords = $settings->meta_keywords ?? 'MISUTECH, thiết bị tự động hóa, biến tần Mitsubishi, biến tần Schneider, PLC Panasonic, PLC Mitsubishi, cảm biến Omron, màn hình HMI, khởi động mềm, tự động hóa Hải Phòng';
        $siteUrl = $settings->url ?? route('home.index');
        $ogImageUrl = !empty($settings->og_image) 
            ? (Str::startsWith($settings->og_image, ['http://', 'https://']) ? $settings->og_image : asset('storage/clients/imgs/settings/' . $settings->og_image))
            : asset('storage/clients/imgs/settings/banner-seo-misutech.jpg');
        $logoUrl = !empty($settings->site_logo)
            ? (Str::startsWith($settings->site_logo, ['http://', 'https://']) ? $settings->site_logo : asset('storage/clients/imgs/settings/' . $settings->site_logo))
            : asset('storage/clients/imgs/settings/logo-misutech.png');
        $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
        $hotline = $settings->hotline ?? ($settings->phone ?? '0866555212');
        $email = $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn';
        $address = $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam';
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $siteUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $siteUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $siteUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large">
    <meta name="description" content="{{ $siteDesc }}">
    <meta name="keywords" content="{{ $siteKeywords }}">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $siteTitle }}">
    <meta property="og:description" content="{{ $siteDesc }}">
    <meta property="og:url" content="{{ $siteUrl }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $companyName }}">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $settings->name ?? 'MISUTECH' }}">
    <meta name="twitter:title" content="{{ $siteTitle }}">
    <meta name="twitter:description" content="{{ $siteDesc }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

    {{-- Structured Data Schema JSON-LD --}}
    @php
        $schemaHome = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => $siteUrl . "#organization",
                    "name" => $companyName,
                    "url" => $siteUrl,
                    "logo" => $logoUrl,
                    "email" => $email,
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => $address,
                        "addressLocality" => "Hải Phòng",
                        "addressRegion" => "Hải Phòng",
                        "postalCode" => "180000",
                        "addressCountry" => "VN"
                    ],
                    "contactPoint" => [
                        [
                            "@type" => "ContactPoint",
                            "telephone" => $hotline,
                            "contactType" => "customer service"
                        ]
                    ]
                ],
                [
                    "@type" => "WebSite",
                    "@id" => $siteUrl . "#website",
                    "url" => $siteUrl,
                    "name" => $settings->name ?? "MISUTECH",
                    "publisher" => [
                        "@id" => $siteUrl . "#organization"
                    ],
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => route('shop.index') . "?tim-kiem={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "LocalBusiness",
                    "@id" => $siteUrl . "#localbusiness",
                    "name" => $companyName,
                    "image" => $ogImageUrl,
                    "url" => $siteUrl,
                    "telephone" => $hotline,
                    "priceRange" => "₫₫₫",
                    "address" => [
                        "@type" => "PostalAddress",
                        "streetAddress" => $address,
                        "addressLocality" => "Hải Phòng",
                        "addressRegion" => "Hải Phòng",
                        "postalCode" => "180000",
                        "addressCountry" => "VN"
                    ],
                    "openingHoursSpecification" => [
                        [
                            "@type" => "OpeningHoursSpecification",
                            "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                            "opens" => "08:00",
                            "closes" => "17:30"
                        ]
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $siteUrl . "#webpage",
                    "url" => $siteUrl,
                    "name" => $siteTitle,
                    "description" => $siteDesc,
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => $siteUrl . "#website"
                    ]
                ],
                [
                    "@type" => "BreadcrumbList",
                    "itemListElement" => [
                        [
                            "@type" => "ListItem",
                            "position" => 1,
                            "item" => [
                                "@id" => $siteUrl,
                                "name" => "Trang chủ"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schemaHome, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/home.css') }}?v={{ file_exists(public_path('clients/css/home.css')) ? filemtime(public_path('clients/css/home.css')) : time() }}">
@endpush

@push('scripts')
    <script src="{{ asset('clients/js/home.js') }}?v={{ file_exists(public_path('clients/js/home.js')) ? filemtime(public_path('clients/js/home.js')) : time() }}"></script>
@endpush

@section('content')
    <section class="misutech_home_hero">
        <div class="misutech_home_container misutech_home_hero_grid">
            <ul class="misutech_home_category_menu">
                @foreach ($mainCategories as $category)
                    <li class="misutech_home_category_item">
                        <a href="{{ route('categories.show', $category->slug) }}" class="misutech_home_category_label">
                            <span class="misutech_home_category_name_text">{{ $category->name }}</span>
                            @if ($category->children->count() > 0)
                                <span class="misutech_home_category_arrow_icon">›</span>
                            @endif
                        </a>

                        @if ($category->children->count() > 0)
                            <div class="misutech_home_mega_menu">
                                <div class="misutech_home_mega_grid">
                                    @foreach ($category->children as $child)
                                        <div class="misutech_home_mega_column {{ $child->children->count() > 0 ? 'has-subitems' : 'no-subitems' }}">
                                            <h4 class="misutech_home_mega_title">
                                                <a href="{{ route('categories.show', $child->slug) }}">
                                                    <span class="misutech_mega_title_text">{{ $child->name }}</span>
                                                    <span class="misutech_mega_title_arrow">›</span>
                                                </a>
                                            </h4>
                                            @if ($child->children->count() > 0)
                                                <ul class="misutech_home_mega_list">
                                                    @foreach ($child->children as $grandchild)
                                                        <li><a href="{{ route('categories.show', $grandchild->slug) }}">{{ $grandchild->name }}</a></li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach

                <li class="misutech_home_category_item">
                    <a href="{{ route('shop.index') }}" class="misutech_home_category_label misutech_home_category_all_link">
                        <span class="misutech_home_category_name_text">+ Xem tất cả danh mục</span>
                        <span class="misutech_home_category_arrow_icon">›</span>
                    </a>

                    <div class="misutech_home_mega_menu">
                        <div class="misutech_home_mega_grid">
                            @foreach ($allCategories as $allCat)
                                <div class="misutech_home_mega_column {{ $allCat->children->count() > 0 ? 'has-subitems' : 'no-subitems' }}">
                                    <h4 class="misutech_home_mega_title">
                                        <a href="{{ route('categories.show', $allCat->slug) }}">
                                            <span class="misutech_mega_title_text">{{ $allCat->name }}</span>
                                            <span class="misutech_mega_title_arrow">›</span>
                                        </a>
                                    </h4>
                                    @if ($allCat->children->count() > 0)
                                        <ul class="misutech_home_mega_list">
                                            @foreach ($allCat->children as $child)
                                                <li><a href="{{ route('categories.show', $child->slug) }}">{{ $child->name }}</a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </li>
            </ul>

            <div class="misutech_home_hero_center">
                <div class="misutech_home_hero_main" style="position: relative; overflow: hidden;">
                    @foreach ($banners->get(1, collect()) as $index => $banner)
                        <div class="misutech_home_hero_slide"
                            style="opacity: {{ $index === 0 ? '1' : '0' }}; transition: opacity 0.6s ease-in-out, transform 0.6s ease-in-out; transform: {{ $index === 0 ? 'scale(1)' : 'scale(1.05)' }}; pointer-events: {{ $index === 0 ? 'auto' : 'none' }}; z-index: {{ $index === 0 ? '1' : '0' }}; width: 100%; height: 100%; position: absolute; top: 0; left: 0;">
                            <img src="{{ Str::startsWith($banner->image, ['http://', 'https://']) ? $banner->image : asset('storage/clients/imgs/banners/' . $banner->image) }}"
                                alt="{{ $banner->title }}"
                                @if($index === 0) fetchpriority="high" loading="eager" @else loading="lazy" decoding="async" @endif
                                style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;" />
                            <div class="misutech_home_hero_overlay">
                                <div class="misutech_home_hero_copy">
                                    <p class="misutech_home_hero_eyebrow">
                                        {{ $banner->subtitle }}
                                    </p>
                                    <h1 class="misutech_home_hero_title">
                                        {!! nl2br(e($banner->title)) !!}
                                    </h1>
                                    <p class="misutech_home_hero_desc">
                                        {{ $banner->description }}
                                    </p>
                                    <a class="misutech_home_primary_button"
                                        href="{{ $banner->link ?? '#' }}">{{ $banner->button_text ?? 'MUA NGAY' }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="misutech_home_hero_dots" aria-label="Chọn banner"
                        style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); display: flex; gap: 8px; z-index: 10;">
                        @foreach ($banners->get(1, collect()) as $index => $banner)
                            <button class="misutech_home_hero_dot" type="button"
                                aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-label="Banner {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                </div>
                <div class="misutech_home_hero_strip">
                    <svg class="misutech_home_sprite" viewBox="94 96 198 31" role="img"
                        aria-label="Dải chương trình ưu đãi">
                        <use href="#misutech_home_sprite_source"></use>
                    </svg>
                </div>
            </div>

            <aside class="misutech_home_hero_side" aria-label="Khuyến mại nổi bật">
                @php $sideBanner1 = $banners->get(2, collect())->first(); @endphp
                @if ($sideBanner1)
                    <a class="misutech_home_side_banner" href="{{ $sideBanner1->link ?? '#' }}"
                        style="position: relative; border-radius: 4px; overflow: hidden; display: block;">
                        <img src="{{ Str::startsWith($sideBanner1->image, ['http://', 'https://']) ? $sideBanner1->image : asset('storage/clients/imgs/banners/' . $sideBanner1->image) }}"
                            alt="{{ $sideBanner1->title }}" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover;" />
                        <span class="misutech_home_side_banner_label">{{ $sideBanner1->title }}</span>
                    </a>
                @endif

                @php $sideBanner2 = $banners->get(3, collect())->first(); @endphp
                @if ($sideBanner2)
                    <a class="misutech_home_side_banner" href="{{ $sideBanner2->link ?? '#' }}"
                        style="position: relative; border-radius: 4px; overflow: hidden; display: block;">
                        <img src="{{ Str::startsWith($sideBanner2->image, ['http://', 'https://']) ? $sideBanner2->image : asset('storage/clients/imgs/banners/' . $sideBanner2->image) }}"
                            alt="{{ $sideBanner2->title }}" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover;" />
                        <span class="misutech_home_side_banner_label">{{ $sideBanner2->title }}</span>
                    </a>
                @endif
            </aside>
        </div>
    </section>

    <section class="misutech_home_quick" aria-label="Danh mục nhanh">
        <div class="misutech_home_container misutech_home_quick_grid">
            @foreach ($mainCategories->take(9) as $category)
                <a class="misutech_home_quick_item" href="{{ route('categories.show', $category->slug) }}"
                    style="text-decoration: none; color: inherit;">
                    <span class="misutech_home_quick_icon">{!! !empty($category->icon)
                        ? '<img src="' . asset('storage/clients/imgs/categories/' . $category->icon) . '" alt="' . e($category->name) . '">'
                        : '▣' !!}</span> {{ $category->name }}
                </a>
            @endforeach
            <a class="misutech_home_quick_item" href="{{ route('shop.index') }}"
                style="text-decoration: none; color: inherit;">
                <span class="misutech_home_quick_icon">＋</span> Xem thêm
            </a>
        </div>
    </section>

    <section class="misutech_home_section" id="misutech_home_flash">
        <div class="misutech_home_container misutech_home_flash_shell">
            <div class="misutech_home_flash_header">
                <h2 class="misutech_home_flash_title">
                    <span class="misutech_home_flash_bolt">ϟ</span> ƯU ĐÃI HÔM NAY
                </h2>
                @php
                    $diffSecs = max(0, now()->diffInSeconds(now()->endOfDay()));
                    $initH = floor($diffSecs / 3600);
                    $initM = floor(($diffSecs % 3600) / 60);
                    $initS = $diffSecs % 60;
                @endphp
                <div class="misutech_home_countdown">
                    <span>Kết thúc sau</span>
                    <span class="misutech_home_countdown_box" data-countdown="hours">{{ sprintf('%02d', $initH) }}</span>:
                    <span class="misutech_home_countdown_box" data-countdown="minutes">{{ sprintf('%02d', $initM) }}</span>:
                    <span class="misutech_home_countdown_box" data-countdown="seconds">{{ sprintf('%02d', $initS) }}</span>
                </div>
            </div>
            <div class="misutech_home_product_grid">
                @foreach ($flashSaleProducts as $product)
                    <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}">
                        <button class="misutech_home_product_favorite" type="button"
                            aria-label="Thêm {{ $product->name }} vào yêu thích">
                            ♡
                        </button>
                        @if ($product->sale_price && $product->price > 0)
                            <span
                                class="misutech_home_product_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                        @endif
                        <div class="misutech_home_product_image">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    decoding="async"
                                    style="width:100%;height:100%;object-fit:cover;border-radius:4px;" />
                            </a>
                        </div>
                        <h3 class="misutech_home_product_name">
                            <a href="{{ route('product.show', $product->slug) }}"
                                style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                        </h3>
                        <div class="misutech_home_product_rating"
                            aria-label="Đánh giá {{ $product->rating_average }} trên 5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($product->rating_average))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                            <span>({{ $product->reviews_count }})</span>
                        </div>
                        <div class="misutech_home_product_price_row">
                            <div>
                                <strong
                                    class="misutech_home_product_price">{{ number_format($product->sale_price ?? $product->price, 0, ',', '.') }}
                                    VNĐ</strong>
                                @if ($product->sale_price)
                                    <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }}
                                        VNĐ</del>
                                @endif
                            </div>
                            <button class="misutech_home_add_cart" type="button" data-cart
                                data-product="{{ $product->name }}" aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                +
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="misutech_home_empty" aria-hidden="true">
                Không tìm thấy sản phẩm phù hợp.
            </div>
        </div>
    </section>

    <section class="misutech_home_catalog_section" id="misutech_home_catalog_0">
        <div class="misutech_home_container">
            <header class="misutech_home_section_header">
                <h2 class="misutech_home_section_title">
                    <span class="misutech_home_section_title_mark"></span>Sản phẩm nổi bật
                </h2>
                <div class="misutech_home_section_tabs" role="tablist" aria-label="Bộ lọc Sản phẩm nổi bật">
                    <button class="misutech_home_section_tab" type="button" role="tab" aria-selected="true"
                        data-section="0">
                        Bán chạy</button><button class="misutech_home_section_tab" type="button" role="tab"
                        aria-selected="false" data-section="0">
                        Mới nhất</button><button class="misutech_home_section_tab" type="button" role="tab"
                        aria-selected="false" data-section="0">
                        Giá tốt
                    </button>
                    <a class="misutech_home_view_all" href="{{ route('shop.index') }}">Xem tất cả ›</a>
                </div>
            </header>
            <div class="misutech_home_catalog_grid">
                @foreach ($featuredProducts as $index => $product)
                    <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}">
                        <button class="misutech_home_product_favorite" type="button"
                            aria-label="Thêm {{ $product->name }} vào yêu thích">
                            ♡
                        </button>
                        @if ($product->sale_price && $product->price > 0)
                            <span
                                class="misutech_home_product_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                        @endif
                        <div class="misutech_home_product_image">
                            <a href="{{ route('product.show', $product->slug) }}">
                                <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    decoding="async"
                                    style="width:100%;height:100%;object-fit:cover;border-radius:4px;" />
                            </a>
                        </div>
                        <h3 class="misutech_home_product_name">
                            <a href="{{ route('product.show', $product->slug) }}"
                                style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                        </h3>
                        <div class="misutech_home_product_rating"
                            aria-label="Đánh giá {{ $product->rating_average }} trên 5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= round($product->rating_average))
                                    ★
                                @else
                                    ☆
                                @endif
                            @endfor
                            <span>({{ $product->reviews_count }})</span>
                        </div>
                        @if ($index === 0)
                            <div class="misutech_home_product_short_desc"
                                style="font-size:13px; color:#666; margin:5px 0;">
                                {{ Str::limit(strip_tags($product->content ?? 'Sản phẩm chất lượng cao, mang đến trải nghiệm tuyệt vời cho bạn.'), 120) }}
                            </div>
                        @endif
                        <div class="misutech_home_product_price_row">
                            <div>
                                <strong
                                    class="misutech_home_product_price">{{ number_format($product->sale_price ?? $product->price, 0, ',', '.') }}
                                    VNĐ</strong>
                                @if ($product->sale_price)
                                    <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }}
                                        VNĐ</del>
                                @endif
                            </div>
                            <button class="misutech_home_add_cart" type="button" data-cart
                                data-product="{{ $product->name }}" aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                +
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @foreach ($categorySections as $index => $section)
        <section class="misutech_home_catalog_section" id="misutech_home_catalog_{{ $index + 1 }}">
            <div class="misutech_home_container">
                <header class="misutech_home_section_header">
                    <h2 class="misutech_home_section_title">
                        <span class="misutech_home_section_title_mark"></span>{{ $section->category->name }}
                    </h2>
                    <div class="misutech_home_section_tabs" role="tablist"
                        aria-label="Bộ lọc {{ $section->category->name }}">
                        <a class="misutech_home_view_all"
                            href="{{ route('categories.show', $section->category->slug ?? $section->category->id) }}">Xem
                            tất cả ›</a>
                    </div>
                </header>
                <div class="misutech_home_catalog_grid">
                    @foreach ($section->products as $product)
                        <article class="misutech_home_product_card" data-product-name="{{ strtolower($product->name) }}">
                            <button class="misutech_home_product_favorite" type="button"
                                aria-label="Thêm {{ $product->name }} vào yêu thích">
                                ♡
                            </button>
                            @if ($product->sale_price && $product->price > 0)
                                <span
                                    class="misutech_home_product_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                            @endif
                            <div class="misutech_home_product_image">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                        alt="{{ $product->name ?? 'No image' }}"
                                        loading="lazy" />
                                </a>
                            </div>
                            <h3 class="misutech_home_product_name">
                                <a href="{{ route('product.show', $product->slug) }}"
                                    style="color:inherit;text-decoration:none;">{{ $product->name }}</a>
                            </h3>
                            <div class="misutech_home_product_rating"
                                aria-label="Đánh giá {{ $product->rating_average }} trên 5">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= round($product->rating_average))
                                        ★
                                    @else
                                        ☆
                                    @endif
                                @endfor
                                <span>({{ $product->reviews_count }})</span>
                            </div>
                            <div class="misutech_home_product_price_row">
                                <div>
                                    <strong
                                        class="misutech_home_product_price">{{ number_format($product->sale_price ?? $product->price, 0, ',', '.') }}
                                        VNĐ</strong>
                                    @if ($product->sale_price)
                                        <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }}
                                            VNĐ</del>
                                    @endif
                                </div>
                                <button class="misutech_home_add_cart" type="button" data-cart
                                    data-product="{{ $product->name }}"
                                    aria-label="Thêm {{ $product->name }} vào giỏ hàng">
                                    +
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    <section class="misutech_home_shop_categories" aria-labelledby="misutech_home_shop_categories_title">
        <div class="misutech_home_container">
            <header class="misutech_home_section_header">
                <h2 class="misutech_home_section_title" id="misutech_home_shop_categories_title">
                    <span class="misutech_home_section_title_mark"></span>Mua sắm theo danh mục
                </h2>
                <a class="misutech_home_view_all" href="{{ route('shop.index') }}">Xem tất cả ›</a>
            </header>
            <div class="misutech_home_shop_category_grid"
                style="display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 15px; padding: 20px 0;">
                @foreach ($allCategories->take(14) as $category)
                    <a href="{{ route('categories.show', $category->slug) }}" class="misutech_home_shop_category_item">
                        <span class="misutech_home_shop_category_icon_wrapper">
                            @if (!empty($category->icon))
                                <img src="{{ asset('storage/clients/imgs/categories/' . $category->icon) }}"
                                    alt="{{ $category->name }}"
                                    style="max-width: 35px; max-height: 35px; object-fit: contain;">
                            @else
                                <span style="font-size: 24px; color: #0052cc;">▣</span>
                            @endif
                        </span>
                        <span style="font-size: 13px; font-weight: 500; line-height: 1.4;">{{ $category->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if(isset($blogSections) && $blogSections->isNotEmpty())
        <section class="misutech_home_content_area" aria-label="Tin tức và cẩm nang kỹ thuật">
            <div class="misutech_home_container misutech_home_content_grid">
                @foreach($blogSections as $blogCat)
                    <div class="misutech_home_content_column">
                        <h2 class="misutech_home_content_heading">
                            <span>{{ $blogCat->name }}</span>
                            <a class="misutech_home_content_more" href="{{ route('blogs.index', ['category' => $blogCat->slug]) }}">Xem thêm ›</a>
                        </h2>
                        <div class="misutech_home_article_list">
                            @foreach($blogCat->posts as $post)
                                @php
                                    $pThumb = $post->thumbnailMedia->first();
                                    $pThumbUrl = $pThumb ? $pThumb->url : asset('clients/imgs/default-blog.jpg');
                                @endphp
                                <article class="misutech_home_article">
                                    <a href="{{ route('blogs.show', $post->slug) }}" class="misutech_home_article_image" title="{{ $post->title }}">
                                        <img src="{{ $pThumbUrl }}" alt="{{ $post->title }}" loading="lazy" style="width: 100%; height: 100%; object-fit: cover;">
                                    </a>
                                    <div class="misutech_home_article_body">
                                        <h3 class="misutech_home_article_title">
                                            <a href="{{ route('blogs.show', $post->slug) }}" title="{{ $post->title }}">
                                                {{ $post->title }}
                                            </a>
                                        </h3>
                                        @if($post->summary)
                                            <p class="misutech_home_article_excerpt">
                                                {{ Str::limit($post->summary, 85) }}
                                            </p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="misutech_home_service_bar" aria-label="Cam kết dịch vụ">
        <div class="misutech_home_container misutech_home_service_grid">
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon">▣</span><span class="misutech_home_service_copy"><strong>Giao
                        hàng toàn quốc</strong><span>Đóng gói cẩn thận, theo dõi dễ dàng</span></span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon">↺</span><span class="misutech_home_service_copy"><strong>Đổi
                        trả linh hoạt</strong><span>Hỗ trợ nhanh trong thời hạn quy định</span></span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon">✓</span><span class="misutech_home_service_copy"><strong>Sản
                        phẩm chính hãng</strong><span>Nguồn gốc rõ ràng, bảo hành an tâm</span></span>
            </div>
            <div class="misutech_home_service">
                <span class="misutech_home_service_icon">☎</span><span class="misutech_home_service_copy"><strong>Hỗ trợ
                        tận tâm</strong><span>Tư vấn lựa chọn sản phẩm phù hợp</span></span>
            </div>
        </div>
    </section>
@endsection
