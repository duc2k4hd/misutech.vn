@extends('clients.layouts.master')

@php
    $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
    $hotline = $settings->hotline ?? ($settings->phone ?? '0866.555.212');
    $email = $settings->email ?? 'contact@misutech.vn';
    $address = $settings->address ?? 'Hải Phòng, Việt Nam';
    $fbUrl = $settings->facebook ?? 'https://www.facebook.com/misutech.vn';
    $zaloUrl = $settings->zalo ?? 'https://zalo.me/0866555212';
    $logoUrl = !empty($settings->site_logo)
        ? (Str::startsWith($settings->site_logo, ['http://', 'https://'])
            ? $settings->site_logo
            : asset('storage/clients/imgs/settings/' . $settings->site_logo))
        : asset('storage/clients/imgs/settings/logo.png');

    $rawProdTitle = $product->meta_title ?? $product->name;
    $cleanProdTitle = trim(preg_replace('/(\s*[\-\|]\s*(MISUTECH|Công ty cổ phần Misutech)).*$/iu', '', $rawProdTitle));
    $finalProdTitle = $cleanProdTitle . ' – Chính Hãng, Giá Tốt | ' . $companyName;

    // Meta description chuẩn 155-160 ký tự
    $rawDesc =
        $product->meta_description ??
        ($product->short_description ?? Str::limit(strip_tags($product->content ?? ''), 160));
    $metaDescription = trim(preg_replace('/\s+/', ' ', strip_tags($rawDesc)));
    if (empty($metaDescription)) {
        $metaDescription = "Mua {$product->name} chính hãng tại {$companyName}. Đầy đủ CO/CQ, bảo hành 12 tháng, giao hàng toàn quốc, tư vấn kỹ thuật 24/7.";
    }

    $productUrl = route('product.show', $product->slug);
    $mainImage = $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png');

    // Danh sách ảnh gallery
    $galleryUrls = [$mainImage];
    foreach ($product->galleryMedia as $g) {
        if (!empty($g->url) && !in_array($g->url, $galleryUrls)) {
            $galleryUrls[] = $g->url;
        }
    }

    // Giá
    $effectivePrice =
        (float) ($product->sale_price && $product->sale_price < $product->price && $product->price > 0
            ? $product->sale_price
            : $product->price);
    if ($effectivePrice <= 0) {
        $effectivePrice = (float) $product->price;
    }

    // Breadcrumbs Schema: 1. Trang chủ -> 2. Danh mục gần sản phẩm nhất -> 3. Tên sản phẩm
    $breadcrumbItems = [
        [
            '@type' => 'ListItem',
            'position' => 1,
            'item' => [
                '@id' => route('home.index'),
                'name' => 'Trang chủ',
            ],
        ],
    ];
    $pos = 2;
    if ($product->category) {
        $breadcrumbItems[] = [
            '@type' => 'ListItem',
            'position' => $pos++,
            'item' => [
                '@id' => route('categories.show', $product->category->slug),
                'name' => $product->category->name,
            ],
        ];
    }
    $breadcrumbItems[] = [
        '@type' => 'ListItem',
        'position' => $pos,
        'item' => [
            '@id' => $productUrl,
            'name' => $product->name,
        ],
    ];

    // Keywords SEO
    $keywordsList = array_filter([
        $product->name,
        $product->sku,
        $product->brand?->name,
        $product->category?->name,
        $product->series?->name,
        'thiết bị tự động hóa',
        'chính hãng',
        'giá tốt',
    ]);
    $keywordsStr = implode(', ', $keywordsList);

    // Structured Data Schema JSON-LD Graph
    $schemaProductGraph = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                '@id' => route('home.index') . '#organization',
                'name' => $companyName,
                'url' => route('home.index'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                ],
                'email' => $email,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address,
                    'addressLocality' => 'Hải Phòng',
                    'addressRegion' => 'Hải Phòng',
                    'postalCode' => '180000',
                    'addressCountry' => 'VN',
                ],
                'contactPoint' => [
                    [
                        '@type' => 'ContactPoint',
                        'telephone' => $hotline,
                        'contactType' => 'customer service',
                        'availableLanguage' => ['Vietnamese'],
                        'areaServed' => 'VN',
                    ],
                ],
                'sameAs' => array_filter([$fbUrl, $zaloUrl]),
            ],
            [
                '@type' => 'WebPage',
                '@id' => $productUrl . '#webpage',
                'url' => $productUrl,
                'name' => $finalProdTitle,
                'description' => $metaDescription,
                'inLanguage' => 'vi',
                'isPartOf' => [
                    '@id' => route('home.index') . '#website',
                ],
            ],
            [
                '@type' => 'LocalBusiness',
                '@id' => route('home.index') . '#localbusiness',
                'name' => $companyName,
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                ],
                'image' => $mainImage,
                'url' => route('home.index'),
                'telephone' => $hotline,
                'email' => $email,
                'priceRange' => '₫₫₫',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $address,
                    'addressLocality' => 'Hải Phòng',
                    'addressRegion' => 'Hải Phòng',
                    'postalCode' => '180000',
                    'addressCountry' => 'VN',
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => 20.8449,
                    'longitude' => 106.6881,
                ],
                'openingHoursSpecification' => [
                    [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                        'opens' => '08:00',
                        'closes' => '17:30',
                    ],
                ],
                'sameAs' => array_filter([$fbUrl, $zaloUrl]),
            ],
            [
                '@type' => 'BreadcrumbList',
                'itemListElement' => $breadcrumbItems,
            ],
            [
                '@type' => 'Product',
                '@id' => $productUrl . '#product',
                'name' => $product->name,
                'image' => count($galleryUrls) === 1 ? $galleryUrls[0] : $galleryUrls,
                'description' => $metaDescription,
                'sku' => $product->sku ?? 'MISU-' . $product->id,
                'mpn' => $product->sku ?? 'MISU-' . $product->id,
                'productID' => 'sku:' . ($product->sku ?? 'MISU-' . $product->id),
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $product->brand?->name ?? 'MISUTECH',
                ],
                'manufacturer' => [
                    '@type' => 'Organization',
                    'name' => $product->brand?->name ?? $companyName,
                ],
                'category' => $product->category?->name ?? 'Thiết bị tự động hóa',
                'countryOfOrigin' => 'VN',
                'isFamilyFriendly' => true,
                'releaseDate' => $product->published_at
                    ? $product->published_at->format('Y-m-d')
                    : now()->format('Y-m-d'),
                'offers' => [
                    '@type' => 'Offer',
                    'url' => $productUrl,
                    'priceCurrency' => 'VND',
                    'price' => number_format($effectivePrice, 2, '.', ''),
                    'priceValidUntil' => now()->addYear()->format('Y-m-d'),
                    'availability' => 'https://schema.org/InStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => [
                        '@type' => 'Organization',
                        '@id' => route('home.index') . '#organization',
                        'name' => $companyName,
                    ],
                    'shippingDetails' => [
                        '@type' => 'OfferShippingDetails',
                        'shippingDestination' => [
                            '@type' => 'DefinedRegion',
                            'addressCountry' => 'VN',
                        ],
                        'shippingRate' => [
                            '@type' => 'MonetaryAmount',
                            'value' => '0',
                            'currency' => 'VND',
                        ],
                        'deliveryTime' => [
                            '@type' => 'ShippingDeliveryTime',
                            'handlingTime' => [
                                '@type' => 'QuantitativeValue',
                                'minValue' => 1,
                                'maxValue' => 2,
                                'unitCode' => 'DAY',
                            ],
                            'transitTime' => [
                                '@type' => 'QuantitativeValue',
                                'minValue' => 1,
                                'maxValue' => 3,
                                'unitCode' => 'DAY',
                            ],
                        ],
                    ],
                    'hasMerchantReturnPolicy' => [
                        '@type' => 'MerchantReturnPolicy',
                        'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                        'merchantReturnDays' => 7,
                        'returnMethod' => 'https://schema.org/ReturnByMail',
                        'returnFees' => 'https://schema.org/FreeReturn',
                        'refundType' => 'https://schema.org/FullRefund',
                        'applicableCountry' => 'VN',
                        'merchantReturnLink' => route('home.index') . '#chinh-sach-doi-tra',
                    ],
                ],
                'aggregateRating' => [
                    '@type' => 'AggregateRating',
                    'ratingValue' => $product->rating_average > 0 ? (string) $product->rating_average : '5.0',
                    'reviewCount' => $product->reviews_count > 0 ? (int) $product->reviews_count : 1,
                    'bestRating' => '5',
                    'worstRating' => '1',
                ],
            ],
        ],
    ];
@endphp

@section('title', $finalProdTitle)

@push('meta')
    {{-- Preload LCP Image --}}
    <link rel="preload" as="image" href="{{ $mainImage }}" fetchpriority="high">

    {{-- Standard SEO Meta Tags --}}
    <meta name="robots" content="follow, index, max-snippet:-1, max-video-preview:-1, max-image-preview:large">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $keywordsStr }}">
    <meta name="author" content="{{ $companyName }}">
    <meta http-equiv="date"
        content="{{ $product->published_at ? $product->published_at->format('d/m/y') : now()->format('d/m/y') }}">

    {{-- Canonical & Alternates --}}
    <link rel="canonical" href="{{ $productUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $productUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $productUrl }}">

    {{-- OpenGraph Tags (Facebook, Zalo) --}}
    <meta property="og:title" content="{{ $finalProdTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $productUrl }}">
    <meta property="og:image" content="{{ $mainImage }}">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="800">
    <meta property="og:image:alt" content="{{ $product->name }}">
    <meta property="og:type" content="product">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="product:price:amount" content="{{ $effectivePrice }}">
    <meta property="product:price:currency" content="VND">
    <meta property="product:availability" content="in stock">
    <meta property="product:brand" content="{{ $product->brand?->name ?? 'MISUTECH' }}">
    <meta property="product:retailer_item_id" content="{{ $product->sku ?? 'MISU-' . $product->id }}">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="{{ $companyName }}">
    <meta name="twitter:title" content="{{ $finalProdTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $mainImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaProductGraph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('clients/css/product.css') }}?v={{ file_exists(public_path('clients/css/product.css')) ? filemtime(public_path('clients/css/product.css')) : '1.0' }}">
@endpush

@push('scripts')
    <script
        src="{{ asset('clients/js/product.js') }}?v={{ file_exists(public_path('clients/js/product.js')) ? filemtime(public_path('clients/js/product.js')) : '1.0' }}" defer>
    </script>
@endpush

@section('content')
    <div class="misutech_home_container">
        {{-- Breadcrumb --}}
        <nav class="misutech_product_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_product_breadcrumb_inner">
                <a class="misutech_product_breadcrumb_link" href="{{ route('home.index') }}">
                    <svg class="misutech_breadcrumb_home_icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Trang chủ
                </a>
                @if ($product->category)
                    <span class="misutech_product_breadcrumb_separator">›</span>
                    <a class="misutech_product_breadcrumb_link"
                        href="{{ route('categories.show', $product->category->slug) }}">
                        {{ $product->category->name }}
                    </a>
                @endif
                <span class="misutech_product_breadcrumb_separator">›</span>
                <span id="misutech_breadcrumb_current">{{ $product->name }}</span>
            </div>
        </nav>

        {{-- Thông tin sản phẩm chính --}}
        <section class="misutech_product_product" id="misutech_product_details">
            <div class="misutech_product_product_grid">

                {{-- Thư viện ảnh --}}
                <div class="misutech_product_gallery">

                    <button class="misutech_product_main_image_button" type="button" data-open-image
                        aria-label="Mở ảnh sản phẩm lớn">
                        <img class="misutech_product_main_image" id="misutech_main_img"
                            src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                            alt="{{ $product->name }}" width="500" height="500" fetchpriority="high" loading="eager" decoding="async">
                        <span class="misutech_product_zoom_hint">⌕</span>
                    </button>

                    <div class="misutech_product_thumbnails" id="misutech_product_thumbnails_wrapper"
                        aria-label="Ảnh sản phẩm">
                        <button class="misutech_product_thumbnail misutech_product_active" type="button"
                            data-image="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                            aria-label="Ảnh chính">
                            <img class="misutech_product_thumbnail_image"
                                src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                alt="{{ $product->name }}" width="80" height="80" loading="lazy" decoding="async">
                        </button>
                        @foreach ($product->galleryMedia as $i => $img)
                            <button class="misutech_product_thumbnail" type="button" data-image="{{ $img->url }}"
                                aria-label="Ảnh {{ $i + 2 }}">
                                <img class="misutech_product_thumbnail_image" src="{{ $img->url }}"
                                    alt="{{ $img->alt ?? $product->name }}" width="80" height="80" loading="lazy" decoding="async">
                            </button>
                        @endforeach
                    </div>

                    <div class="misutech_product_direction" aria-label="Điều hướng sản phẩm">
                        <button class="misutech_product_direction_button" type="button" data-image-direction="-1"
                            aria-label="Ảnh trước">‹</button>
                        <button class="misutech_product_direction_button" type="button" data-open-image
                            aria-label="Tất cả ảnh" title="Xem toàn bộ ảnh">⊞</button>
                        <button class="misutech_product_direction_button" type="button" data-image-direction="1"
                            aria-label="Ảnh tiếp theo">›</button>
                    </div>

                    {{-- Chia sẻ mạng xã hội --}}
                    <div class="misutech_product_share">
                        <span class="misutech_product_share_title">Chia sẻ:</span>
                        <div class="misutech_product_share_list">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="misutech_product_share_btn misutech_product_share_fb" title="Chia sẻ lên Facebook"
                                aria-label="Chia sẻ sản phẩm lên Facebook">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                            <a href="https://www.facebook.com/dialog/send?link={{ urlencode(request()->fullUrl()) }}&app_id=291494419107518&redirect_uri={{ urlencode(request()->fullUrl()) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="misutech_product_share_btn misutech_product_share_messenger"
                                title="Gửi qua Messenger" aria-label="Messenger">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path
                                        d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.654V24l4.088-2.245c1.093.303 2.248.467 3.443.467 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.192 14.963l-3.056-3.26-5.963 3.26 6.559-6.963 3.13 3.26 5.889-3.26-6.559 6.963z" />
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($product->name) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="misutech_product_share_btn misutech_product_share_twitter"
                                title="Chia sẻ lên Twitter (X)" aria-label="Twitter">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path
                                        d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.936 9.936 0 0024 4.59z" />
                                </svg>
                            </a>
                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(request()->fullUrl()) }}&description={{ urlencode($product->name) }}&media={{ urlencode($product->thumbnailMedia->first()?->url ?? '') }}"
                                target="_blank" rel="noopener noreferrer"
                                class="misutech_product_share_btn misutech_product_share_pinterest"
                                title="Lưu vào Pinterest" aria-label="Pinterest">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path
                                        d="M12 0C5.373 0 0 5.373 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738.098.119.112.224.083.345-.09.375-.291 1.199-.334 1.357-.056.23-.186.279-.429.167-1.597-.741-2.596-3.07-2.596-4.939 0-4.021 2.922-7.712 8.423-7.712 4.421 0 7.859 3.15 7.859 7.362 0 4.394-2.77 7.93-6.616 7.93-1.292 0-2.507-.672-2.923-1.463l-.796 3.034c-.288 1.109-1.066 2.498-1.588 3.345C9.882 23.856 10.923 24 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0z" />
                                </svg>
                            </a>
                            <button type="button" class="misutech_product_share_btn misutech_product_share_copylink"
                                id="misutech_share_copy_btn" title="Sao chép liên kết" aria-label="Sao chép liên kết">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none"
                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Thông tin chi tiết --}}
                <article class="misutech_product_product_info">
                    <h1 class="misutech_product_title" id="misutech_product_title">{{ $product->name }}</h1>

                    {{-- Đánh giá --}}
                    <div class="misutech_product_rating_row">
                        @php
                            $rating = round($product->rating_average ?? 0, 1);
                            $fullStars = floor($rating);
                        @endphp
                        <span class="misutech_product_stars" aria-label="{{ $rating }} trên 5 sao">
                            @for ($i = 1; $i <= 5; $i++)
                                {{ $i <= $fullStars ? '★' : '☆' }}
                            @endfor
                        </span>
                        <span class="misutech_product_review_count">({{ number_format($product->reviews_count) }} đánh
                            giá)</span>
                        @if ($product->reviews_count > 0)
                            <a class="misutech_product_view_reviews" href="#misutech_product_reviews">XEM TẤT CẢ ĐÁNH
                                GIÁ</a>
                        @endif
                    </div>

                    {{-- Giá --}}
                    <div class="misutech_product_price" id="misutech_product_price_box">
                        @if ($product->sale_price && $product->sale_price < $product->price)
                            <span
                                class="misutech_product_price_sale">{{ number_format($product->sale_price, 0, ',', '.') }}đ</span>
                            <span
                                class="misutech_product_price_original">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                            @php $discountPercent = round((1 - $product->sale_price / $product->price) * 100); @endphp
                            <span class="misutech_product_price_badge">-{{ $discountPercent }}%</span>
                        @else
                            <span
                                class="misutech_product_price_sale">{{ number_format($product->price, 0, ',', '.') }}đ</span>
                        @endif
                    </div>

                    {{-- Khối chọn Model cùng Series (Nếu sản phẩm thuộc Series) --}}
                    @if ($product->series && $seriesProducts->count() > 0)
                        <div class="misutech_product_series_box" id="misutechSeriesBox">
                            <div class="misutech_product_series_header">
                                <span class="misutech_product_series_title">
                                    Dòng sản phẩm: <a href="{{ route('series.show', $product->series->slug) }}"
                                        title="Xem toàn bộ dòng {{ $product->series->name }}">{{ $product->series->name }} ↗</a>
                                </span>
                                <span class="misutech_product_series_badge" id="misutechSeriesBadge">{{ $seriesProducts->count() }} model</span>
                            </div>

                            {{-- Ô tìm kiếm nhanh Model / SKU --}}
                            @if ($seriesProducts->count() >= 4)
                                <div class="misutech_series_search_wrap">
                                    <span class="misutech_series_search_icon">⌕</span>
                                    <input type="text" id="misutechModelSearchInput" class="misutech_series_search_input"
                                        placeholder="Tìm nhanh mã model / SKU..." autocomplete="off" spellcheck="false"
                                        aria-label="Tìm kiếm model trong dòng sản phẩm">
                                    <button type="button" id="misutechModelSearchClear" class="misutech_series_search_clear"
                                        title="Xóa tìm kiếm" aria-label="Xóa tìm kiếm" hidden>✕</button>
                                </div>
                            @endif

                            <div class="misutech_product_series_models" id="misutechSeriesModelsContainer" role="group"
                                aria-label="Danh sách model thuộc dòng {{ $product->series->name }}">
                                @foreach ($seriesProducts as $item)
                                    @php $isCurrent = ($item->id === $product->id); @endphp
                                    <a href="{{ route('product.show', $item->slug) }}"
                                        class="misutech_product_model_btn {{ $isCurrent ? 'misutech_product_model_active' : '' }}"
                                        data-slug="{{ $item->slug }}"
                                        data-name="{{ $item->name }}"
                                        data-sku="{{ $item->sku }}"
                                        data-search="{{ mb_strtolower(($item->sku ?: '') . ' ' . $item->name) }}"
                                        @if ($isCurrent) aria-current="page" @endif
                                        title="{{ $item->name }} - {{ number_format($item->price, 0, ',', '.') }}đ">
                                        <span class="model_btn_label">{{ $item->sku ?: $item->name }}</span>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Thông báo khi không tìm thấy model khớp --}}
                            <div class="misutech_series_search_empty" id="misutechSeriesSearchEmpty" hidden>
                                <span>Không tìm thấy model nào phù hợp</span>
                            </div>

                            @if ($product->series->description)
                                <p class="misutech_product_series_desc">{{ $product->series->description }}</p>
                            @endif
                        </div>
                    @endif

                    {{-- Thông tin meta --}}
                    <dl class="misutech_product_meta">
                        <div class="misutech_product_meta_row">
                            <dt>Tình trạng:</dt>
                            <dd id="misutech_product_stock_wrapper">
                                <span
                                    class="misutech_product_stock_dot {{ $product->status === 'active' ? '' : 'misutech_product_out_of_stock' }}"
                                    id="misutech_product_stock_dot"></span>
                                <span
                                    id="misutech_product_stock_text">{{ $product->status === 'active' ? 'Còn hàng' : 'Hết hàng' }}</span>
                            </dd>
                        </div>
                        <div class="misutech_product_meta_row" id="misutech_sku_row"
                            style="{{ $product->sku ? '' : 'display:none;' }}">
                            <dt>Mã SP:</dt>
                            <dd id="misutech_product_sku">{{ $product->sku }}</dd>
                        </div>
                        @if ($product->brand)
                            <div class="misutech_product_meta_row">
                                <dt>Thương hiệu:</dt>
                                <dd>{{ $product->brand->name }}</dd>
                            </div>
                        @endif
                        @if ($product->series)
                            <div class="misutech_product_meta_row">
                                <dt>Dòng SP:</dt>
                                <dd><strong>{{ $product->series->name }}</strong></dd>
                            </div>
                        @endif
                        @if ($product->category)
                            <div class="misutech_product_meta_row">
                                <dt>Danh mục:</dt>
                                <dd>
                                    <a href="{{ route('categories.show', $product->category->slug) }}">
                                        {{ $product->category->name }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <p class="misutech_product_summary" id="misutech_product_summary">{!! $product->short_description !!}</p>

                    {{-- Thêm vào giỏ hàng --}}
                    <div class="misutech_product_purchase">
                        <div class="misutech_product_purchase_row">
                            <div class="misutech_product_quantity">
                                <button class="misutech_product_quantity_button" type="button" data-quantity-change="-1"
                                    aria-label="Giảm số lượng">−</button>
                                <input class="misutech_product_quantity_input" type="number" value="1"
                                    min="1" max="99" aria-label="Số lượng">
                                <button class="misutech_product_quantity_button" type="button" data-quantity-change="1"
                                    aria-label="Tăng số lượng">+</button>
                            </div>
                            <button class="misutech_product_add_button" type="button"
                                data-product-id="{{ $product->id }}">Thêm vào giỏ</button>
                            <button class="misutech_product_wishlist_button" type="button"
                                data-product-id="{{ $product->id }}" aria-label="Yêu thích">☆</button>
                        </div>
                        <button class="misutech_product_buy_now" type="button"
                            data-product-id="{{ $product->id }}">Mua ngay</button>
                    </div>

                    <div class="misutech_product_extra_actions">
                        <button class="misutech_product_extra_button" type="button"
                            data-open-modal="share"><span>↗</span> CHIA SẺ</button>
                        <button class="misutech_product_extra_button" type="button"
                            data-open-modal="question"><span>◉</span> ĐẶT CÂU HỎI</button>
                        <button class="misutech_product_extra_button" type="button" data-open-modal="faq"><span>▣</span>
                            FAQ</button>
                    </div>

                    <div class="misutech_product_shipping_notes">
                        <p><span class="misutech_product_shipping_icon">◉</span> Giao hàng từ 3 – 7 ngày làm việc.</p>
                        <p><span class="misutech_product_shipping_icon">▱</span> Sản phẩm chính hãng 100%</p>
                        <p><span class="misutech_product_shipping_icon">◉</span> Tư vấn hỗ trợ giờ hành chính các ngày
                            trong tuần, nghỉ Chủ Nhật.</p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Tab mô tả / chính sách & Cột Sidebar 70/30 --}}
        <section class="misutech_product_tabs_section">
            <div class="misutech_product_tabs_layout">
                {{-- CỘT TRÁI (70%): Nội dung chi tiết các tab --}}
                <div class="misutech_product_tabs_main">
                    <div class="misutech_product_tabs" role="tablist" aria-label="Thông tin sản phẩm">
                        <button class="misutech_product_tab_button misutech_product_active" type="button" role="tab"
                            aria-selected="true" data-tab="description">
                            <svg class="misutech_tab_icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <polyline points="10 9 9 9 8 9"></polyline>
                            </svg>
                            <span>Mô tả sản phẩm</span>
                        </button>
                        <button class="misutech_product_tab_button" type="button" role="tab" aria-selected="false"
                            data-tab="document">
                            <svg class="misutech_tab_icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                            </svg>
                            <span>Tài liệu &amp; Catalog</span>
                        </button>
                        <button class="misutech_product_tab_button misutech_product_hide_on_mobile" type="button"
                            role="tab" aria-selected="false" data-tab="delivery">
                            <svg class="misutech_tab_icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="1" y="3" width="15" height="13"></rect>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 8"></polygon>
                                <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                <circle cx="18.5" cy="18.5" r="2.5"></circle>
                            </svg>
                            <span>Chính sách giao hàng</span>
                        </button>
                        <button class="misutech_product_tab_button misutech_product_hide_on_mobile" type="button"
                            role="tab" aria-selected="false" data-tab="returns">
                            <svg class="misutech_tab_icon" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                <polyline points="9 12 11 14 15 10"></polyline>
                            </svg>
                            <span>Đổi trả &amp; Bảo hành</span>
                        </button>
                    </div>
                    <div class="misutech_product_tab_panels">
                        <div class="misutech_product_tab_panel misutech_product_active"
                            id="misutech_tab_panel_description" role="tabpanel" data-panel="description">
                            @if ($product->content)
                                <div class="misutech_product_content_body">
                                    {!! $product->content !!}
                                </div>
                            @else
                                <p class="misutech_product_no_content">Chưa có mô tả chi tiết cho sản phẩm này.</p>
                            @endif
                        </div>
                        <div class="misutech_product_tab_panel" id="misutech_tab_panel_document" role="tabpanel"
                            data-panel="document" hidden>
                            @if ($product->catalogMedia && $product->catalogMedia->count() > 0)
                                <div class="misutech_product_catalog_viewers_list">
                                    @foreach ($product->catalogMedia as $doc)
                                        <div class="misutech_product_catalog_item">
                                            <div class="misutech_product_catalog_header">
                                                <div class="misutech_product_catalog_title">
                                                    <span class="misutech_product_catalog_badge">PDF</span>
                                                    <strong>{{ $doc->original_name ?: $doc->filename ?: 'Tài liệu kỹ thuật / Catalog' }}</strong>
                                                </div>
                                                <div class="misutech_product_catalog_actions">
                                                    <a href="{{ $doc->url }}" target="_blank" rel="noopener"
                                                        class="misutech_product_catalog_btn">
                                                        Mở toàn màn hình ↗
                                                    </a>
                                                    <a href="{{ route('documents.download', $doc->id) }}"
                                                        class="misutech_product_catalog_btn misutech_product_catalog_btn_primary">
                                                        Tải xuống ⤓
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="misutech_product_catalog_frame_wrap">
                                                <iframe
                                                    src="{{ $doc->url }}#navpanes=0&pagemode=none&view=FitH&toolbar=1"
                                                    class="misutech_product_pdf_iframe"
                                                    title="{{ $doc->original_name ?: $doc->filename }}" loading="lazy">
                                                </iframe>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="misutech_product_no_content">Chưa có tài liệu catalog cho sản phẩm này.</p>
                            @endif
                        </div>
                        <div class="misutech_product_tab_panel" role="tabpanel" data-panel="delivery" hidden>
                            <div class="misutech_product_policy_box">
                                <h3 class="misutech_product_policy_heading">Chính sách vận chuyển &amp; Giao hàng</h3>
                                <ul class="misutech_product_policy_list">
                                    <li><strong>Nội thành Hải Phòng, Hà Nội, TP.HCM:</strong> Giao hàng hỏa tốc trong ngày
                                        (2 – 4 giờ).</li>
                                    <li><strong>Toàn quốc:</strong> Giao hàng từ 1 – 3 ngày làm việc qua Viettel Post /
                                        Chành xe uy tín.</li>
                                    <li><strong>Đồng kiểm tra:</strong> Khách hàng được kiểm tra hàng hóa, quy cách đóng gói
                                        trước khi thanh toán.</li>
                                    <li><strong>Miễn phí vận chuyển:</strong> Áp dụng cho các đơn hàng dự án hoặc theo hợp
                                        đồng cung ứng.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="misutech_product_tab_panel" role="tabpanel" data-panel="returns" hidden>
                            <div class="misutech_product_policy_box">
                                <h3 class="misutech_product_policy_heading">Chính sách bảo hành &amp; Đổi trả chính hãng
                                </h3>
                                <ul class="misutech_product_policy_list">
                                    <li><strong>Bảo hành chính hãng:</strong> 12 tháng theo đúng tiêu chuẩn của nhà sản
                                        xuất.</li>
                                    <li><strong>Đổi mới 1 - 1 trong 7 ngày:</strong> Đổi sản phẩm mới nếu phát sinh lỗi kỹ
                                        thuật từ nhà sản xuất.</li>
                                    <li><strong>Tư vấn kỹ thuật 24/7:</strong> Kỹ sư hỗ trợ đọc sơ đồ đấu nối, cài đặt thông
                                        số và xử lý sự cố.</li>
                                    <li><strong>Chứng nhận CO/CQ:</strong> Cam kết cung cấp đầy đủ chứng từ xuất xứ và chất
                                        lượng.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI (30%): Sticky Sidebar Tăng trải nghiệm & Giữ chân khách hàng --}}
                <aside class="misutech_product_tabs_sidebar">
                    {{-- 1. Card Hỗ trợ Kỹ thuật & Báo giá nhanh --}}
                    <div class="misutech_product_side_card misutech_product_side_support">
                        <div class="misutech_product_side_card_header">
                            <div class="misutech_product_side_icon">⚡</div>
                            <div>
                                <h3 class="misutech_product_side_title">Tư vấn kỹ thuật &amp; Báo giá</h3>
                                <p class="misutech_product_side_subtitle">Tra cứu mã thay thế, chiết khấu dự án tốt nhất
                                </p>
                            </div>
                        </div>
                        <div class="misutech_product_side_actions">
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $hotline) }}"
                                class="misutech_product_side_btn misutech_product_side_btn_call">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path
                                        d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z" />
                                </svg>
                                <span>Hotline: <strong>{{ $hotline }}</strong></span>
                            </a>
                            <a href="{{ $zaloUrl }}" target="_blank" rel="noopener"
                                class="misutech_product_side_btn misutech_product_side_btn_zalo">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path
                                        d="M12 0C5.373 0 0 4.974 0 11.111c0 3.498 1.744 6.615 4.469 8.654V24l4.088-2.245c1.093.303 2.248.467 3.443.467 6.627 0 12-4.974 12-11.111C24 4.974 18.627 0 12 0zm1.192 14.963l-3.056-3.26-5.963 3.26 6.559-6.963 3.13 3.26 5.889-3.26-6.559 6.963z" />
                                </svg>
                                <span>Chat Zalo nhận báo giá 5 phút</span>
                            </a>
                            <button type="button" class="misutech_product_side_btn misutech_product_side_btn_quote"
                                data-open-modal="question">
                                <span>📋 Yêu cầu báo giá số lượng lớn</span>
                            </button>
                        </div>
                    </div>

                    {{-- 2. Card Cam kết chất lượng Dịch vụ --}}
                    <div class="misutech_product_side_card misutech_product_side_trust">
                        <h3 class="misutech_product_side_heading">Cam kết từ MISUTECH</h3>
                        <ul class="misutech_product_trust_list">
                            <li>
                                <span class="misutech_product_trust_check">✓</span>
                                <div>
                                    <strong>100% Chính hãng</strong>
                                    <p>Đầy đủ CO, CQ và Catalog từ hãng</p>
                                </div>
                            </li>
                            <li>
                                <span class="misutech_product_trust_check">✓</span>
                                <div>
                                    <strong>Bảo hành 12 tháng</strong>
                                    <p>Hỗ trợ 1 đổi 1 trong 7 ngày</p>
                                </div>
                            </li>
                            <li>
                                <span class="misutech_product_trust_check">✓</span>
                                <div>
                                    <strong>Kỹ sư hỗ trợ 24/7</strong>
                                    <p>Tư vấn chọn mã &amp; sơ đồ đấu nối</p>
                                </div>
                            </li>
                            <li>
                                <span class="misutech_product_trust_check">✓</span>
                                <div>
                                    <strong>Hóa đơn VAT điện tử</strong>
                                    <p>Xuất hóa đơn hợp lệ theo quy định</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    {{-- 3. Card Sản phẩm cùng dòng / Gợi ý nổi bật (Tăng thời gian On-Site) --}}
                    @php
                        $suggestedProducts = $seriesProducts->isNotEmpty()
                            ? $seriesProducts->take(4)
                            : $relatedProducts->take(4);
                    @endphp
                    @if ($suggestedProducts->isNotEmpty())
                        <div class="misutech_product_side_card misutech_product_side_suggest">
                            <div class="misutech_product_side_card_header">
                                <h3 class="misutech_product_side_heading">Mã sản phẩm tương đương</h3>
                            </div>
                            <div class="misutech_product_side_products">
                                @foreach ($suggestedProducts as $sp)
                                    @php
                                        $spThumb =
                                            $sp->thumbnail_url ??
                                            ($sp->thumbnailMedia->first()?->url ??
                                                asset('storage/clients/imgs/products/no-image.png'));
                                    @endphp
                                    <a href="{{ route('product.show', $sp->slug) }}" class="misutech_product_side_pitem">
                                        <div class="misutech_product_side_pimg">
                                            <img src="{{ $spThumb }}" alt="{{ $sp->name }}" width="70" height="70" loading="lazy"
                                                decoding="async">
                                        </div>
                                        <div class="misutech_product_side_pinfo">
                                            <h4 class="misutech_product_side_pname" title="{{ $sp->name }}">
                                                {{ $sp->name }}</h4>
                                            <div class="misutech_product_side_pprice">
                                                <strong>{{ number_format($sp->sale_price ?? $sp->price, 0, ',', '.') }}đ</strong>
                                                @if ($sp->sale_price && $sp->price > $sp->sale_price)
                                                    <del>{{ number_format($sp->price, 0, ',', '.') }}đ</del>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </section>

        {{-- Lợi ích mua hàng --}}
        <section class="misutech_product_benefits" aria-label="Lợi ích mua hàng">
            <div class="misutech_product_benefit_grid">
                <article class="misutech_product_benefit">
                    <div class="misutech_product_benefit_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8M6,18.5C5.17,18.5 4.5,17.83 4.5,17C4.5,16.17 5.17,15.5 6,15.5C6.83,15.5 7.5,16.17 7.5,17C7.5,17.83 6.83,18.5 6,18.5M18,18.5C17.17,18.5 16.5,17.83 16.5,17C16.5,16.17 17.17,15.5 18,15.5C18.83,15.5 19.5,16.17 19.5,17C19.5,17.83 18.83,18.5 18,18.5M17,12V9.5H19.5L21.47,12H17Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="misutech_product_benefit_title">MIỄN PHÍ VẬN CHUYỂN</h2>
                        <p class="misutech_product_benefit_text">Miễn phí vận chuyển toàn quốc cho đơn hàng từ 500.000đ.
                        </p>
                    </div>
                </article>
                <article class="misutech_product_benefit">
                    <div class="misutech_product_benefit_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor" d="M11,15H6L13,1V9H18L11,23V15Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="misutech_product_benefit_title">GIAO HÀNG NHANH CHÓNG</h2>
                        <p class="misutech_product_benefit_text">Kho hàng phân bổ toàn quốc, giao hàng nhanh chóng đến mọi
                            tỉnh thành.</p>
                    </div>
                </article>
                <article class="misutech_product_benefit">
                    <div class="misutech_product_benefit_icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="misutech_product_benefit_title">ĐỔI TRẢ 30 NGÀY</h2>
                        <p class="misutech_product_benefit_text">Cam kết hoàn tiền hoặc đổi sản phẩm trong vòng 30 ngày nếu
                            có lỗi từ nhà sản xuất.</p>
                    </div>
                </article>
            </div>
        </section>

        {{-- Sản phẩm liên quan (5 trên + 5 dưới = 10 sản phẩm) --}}
        @if ($relatedProducts->isNotEmpty())
            <section class="misutech_product_related" id="misutech_product_related">
                <div class="">
                    <h2 class="misutech_product_section_title">Sản phẩm liên quan</h2>
                    <div class="misutech_product_related_grid">
                        @foreach ($relatedProducts as $related)
                            <article class="misutech_product_related_card" data-name="{{ $related->name }}"
                                data-product-name="{{ $related->name }}"
                                data-price="{{ $related->sale_price && $related->sale_price < $related->price ? $related->sale_price : $related->price }}">
                                <a href="{{ route('product.show', $related->slug) }}"
                                    class="misutech_product_related_link">
                                    <div class="misutech_product_related_media">
                                        <img class="misutech_product_related_image"
                                            src="{{ $related->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                            alt="{{ $related->name }}" width="220" height="220" loading="lazy" decoding="async">
                                        <div class="misutech_product_card_actions">
                                            <button class="misutech_product_card_action" type="button" data-cart
                                                data-related-cart data-product-id="{{ $related->id }}"
                                                aria-label="Thêm vào giỏ">＋</button>
                                            <button class="misutech_product_card_action" type="button" data-quick-view
                                                aria-label="Xem nhanh">⌕</button>
                                            <button class="misutech_product_card_action" type="button"
                                                data-related-wishlist data-product-id="{{ $related->id }}"
                                                aria-label="Yêu thích">♡</button>
                                        </div>
                                    </div>
                                    <h3 class="misutech_product_related_name" title="{{ $related->name }}">
                                        {{ $related->name }}</h3>
                                    <div class="misutech_product_related_stars">
                                        @for ($i = 1; $i <= 5; $i++)
                                            {{ $i <= round($related->rating_average ?? 0) ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                    <strong class="misutech_product_related_price">
                                        @if ($related->sale_price && $related->sale_price < $related->price)
                                            <span
                                                class="misutech_product_price_sale_tag">{{ number_format($related->sale_price, 0, ',', '.') }}đ</span>
                                        @else
                                            <span>{{ number_format($related->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </strong>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Đánh giá sản phẩm --}}
        <section class="misutech_product_reviews" id="misutech_product_reviews">
            <div class="">
                <div class="misutech_reviews_section_header">
                    <h2 class="misutech_product_section_title">Khách hàng đánh giá</h2>
                    <p class="misutech_reviews_section_subtitle">Đánh giá và nhận xét chân thực từ người dùng đã trải
                        nghiệm sản phẩm</p>
                </div>

                <div class="misutech_product_review_layout">
                    {{-- Cột trái: Thống kê điểm số --}}
                    <aside class="misutech_product_review_summary">
                        <div class="misutech_product_review_score_box">
                            <div class="misutech_product_score_big">
                                <span class="misutech_product_score_value"
                                    id="misutech_summary_score">{{ number_format($product->rating_average ?? 5.0, 1) }}</span>
                                <span class="misutech_product_score_star">★</span>
                            </div>
                            <div class="misutech_product_stars_visual">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span
                                        class="star-item {{ $i <= round($product->rating_average ?? 5.0) ? 'filled' : '' }}">★</span>
                                @endfor
                            </div>
                            <div class="misutech_product_score_count">
                                <span id="misutech_summary_count">{{ number_format($product->reviews_count) }}</span>
                                lượt đánh giá
                            </div>
                        </div>

                        <div class="misutech_product_review_bars">
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php
                                    $barData = $ratingBars[$star] ?? ['count' => 0, 'percent' => 0];
                                @endphp
                                <div class="misutech_product_review_bar_row">
                                    <span class="bar_star_label">{{ $star }} <span
                                            class="star_icon">★</span></span>
                                    <div class="bar_track">
                                        <div class="bar_fill" style="width: {{ $barData['percent'] }}%"></div>
                                    </div>
                                    <span class="bar_count">{{ $barData['count'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="misutech_product_review_prompt">
                            <h3 class="prompt_title">Bạn đã dùng sản phẩm này?</h3>
                            <p class="prompt_desc">Hãy để lại đánh giá để giúp khách hàng khác có thêm thông tin</p>
                            <button type="button" class="misutech_btn_open_review_form" id="misutech_btn_open_review">
                                ✍ Viết đánh giá ngay
                            </button>
                        </div>
                    </aside>

                    {{-- Cột phải: Form viết đánh giá & Danh sách nhận xét --}}
                    <div class="misutech_product_review_content">
                        {{-- Form viết đánh giá --}}
                        <div class="misutech_product_review_form_card" id="misutech_review_form_wrapper">
                            <form class="misutech_product_review_form" id="misutech_review_form"
                                action="{{ route('product.review.store', $product->slug) }}" method="POST">
                                @csrf
                                <input type="hidden" name="_review_time_token"
                                    value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) (time() - 4)) }}">
                                <input type="hidden" name="rating" id="misutech_selected_rating" value="5">

                                {{-- Honeypot chống bot --}}
                                <div style="display: none !important;">
                                    <input type="text" name="review_hp_url" value="" tabindex="-1"
                                        autocomplete="off">
                                </div>

                                <div class="form_header">
                                    <h3 class="form_title">Gửi đánh giá của bạn</h3>
                                    <div class="rating_picker">
                                        <span class="picker_label">Đánh giá chung:</span>
                                        <div class="misutech_star_picker" id="misutech_star_picker"
                                            aria-label="Chọn số sao">
                                            <button type="button" class="star_btn active" data-star="1"
                                                title="1 sao - Rất tệ">★</button>
                                            <button type="button" class="star_btn active" data-star="2"
                                                title="2 sao - Tệ">★</button>
                                            <button type="button" class="star_btn active" data-star="3"
                                                title="3 sao - Bình thường">★</button>
                                            <button type="button" class="star_btn active" data-star="4"
                                                title="4 sao - Tốt">★</button>
                                            <button type="button" class="star_btn active" data-star="5"
                                                title="5 sao - Tuyệt vời">★</button>
                                        </div>
                                        <span class="star_rating_text" id="misutech_rating_label">Tuyệt vời (5/5)</span>
                                    </div>
                                </div>

                                <div class="form_grid_fields">
                                    <div class="field_group">
                                        <label class="field_label">Họ và tên của bạn <span class="req">*</span></label>
                                        <input class="misutech_product_review_input" type="text" name="author_name"
                                            placeholder="Ví dụ: Anh Tuấn (Kỹ sư điện)" required maxlength="80">
                                    </div>
                                    <div class="field_group">
                                        <label class="field_label">Số điện thoại / Zalo <span class="opt">(Bảo mật,
                                                không hiện công khai)</span></label>
                                        <input class="misutech_product_review_input" type="tel" name="author_phone"
                                            placeholder="Ví dụ: 0987654321" maxlength="20">
                                    </div>
                                </div>

                                <div class="field_group">
                                    <label class="field_label">Nội dung đánh giá &amp; Trải nghiệm thực tế <span
                                            class="req">*</span></label>
                                    <textarea class="misutech_product_review_textarea" name="comment" rows="4"
                                        placeholder="Chia sẻ cảm nhận về chất lượng thiết bị, độ chính xác, quy cách đóng gói, tiến độ giao hàng..."
                                        required minlength="5" maxlength="1000"></textarea>
                                </div>

                                <div class="review_form_error" id="misutech_review_error" style="display: none;"></div>

                                <div class="form_actions">
                                    <button class="misutech_product_review_submit" type="submit"
                                        id="misutech_review_submit_btn">
                                        <span class="btn_text">GỬI ĐÁNH GIÁ</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Danh sách đánh giá đã có --}}
                        <div class="misutech_reviews_list" id="misutech_reviews_list">
                            @if ($product->reviews->isEmpty())
                                <div class="misutech_product_review_empty" id="misutech_review_empty_notice">
                                    <div class="empty_icon">💬</div>
                                    <p>Chưa có đánh giá nào cho sản phẩm này.<br>Hãy là người đầu tiên chia sẻ cảm nhận!</p>
                                </div>
                            @else
                                @foreach ($product->reviews as $review)
                                    @php
                                        $author =
                                            $review->author_name ?: ($review->user?->name ?: 'Khách hàng Misutech');
                                        $initial = mb_strtoupper(mb_substr($author, 0, 1));
                                    @endphp
                                    <article class="misutech_product_submitted_review">
                                        <div class="review_avatar">{{ $initial }}</div>
                                        <div class="review_body">
                                            <div class="review_header">
                                                <h4 class="review_author_name">{{ $author }}</h4>
                                                <span class="review_verified_badge">✓ Đã mua tại Misutech</span>
                                                <span
                                                    class="review_time">{{ $review->created_at ? $review->created_at->diffForHumans() : 'Gần đây' }}</span>
                                            </div>
                                            <div class="review_stars">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span
                                                        class="star-item {{ $i <= $review->rating ? 'filled' : '' }}">★</span>
                                                @endfor
                                            </div>
                                            <p class="review_comment_text">{{ $review->comment }}</p>
                                        </div>
                                    </article>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <aside class="misutech_product_cart_drawer" aria-hidden="true" hidden>
        <div class="misutech_product_cart_drawer_header">
            <h2 class="misutech_product_cart_drawer_title">Giỏ hàng</h2><button class="misutech_product_cart_close"
                type="button" aria-label="Close cart">×</button>
        </div>
        <div class="misutech_product_cart_items">
            <p class="misutech_product_cart_empty">Giỏ hàng của bạn đang trống.</p>
        </div>
        <div class="misutech_product_cart_footer"><span>Tổng cộng</span><strong
                class="misutech_product_cart_total">0đ</strong></div>
    </aside>
    <div class="misutech_product_drawer_overlay" hidden></div>

    {{-- Full-featured Luxury Product Gallery Lightbox --}}
    <div class="misutech_product_modal misutech_gallery_lightbox" data-modal="image" hidden>
        <div class="misutech_lightbox_backdrop" data-close-modal></div>
        <div class="misutech_lightbox_container" role="dialog" aria-modal="true" aria-label="Xem toàn bộ ảnh sản phẩm">
            
            {{-- Top Bar --}}
            <div class="misutech_lightbox_topbar">
                <div class="misutech_lightbox_title_wrap">
                    <span class="misutech_lightbox_pname">{{ $product->name }}</span>
                    <span class="misutech_lightbox_counter">
                        <strong id="misutech_lb_current">1</strong> / <span id="misutech_lb_total">{{ max(1, 1 + $product->galleryMedia->count()) }}</span>
                    </span>
                </div>
                <div class="misutech_lightbox_actions">
                    <button class="misutech_lb_btn" id="misutech_lb_zoom_out" type="button" title="Thu nhỏ (-)" aria-label="Thu nhỏ">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </button>
                    <button class="misutech_lb_btn" id="misutech_lb_zoom_in" type="button" title="Phóng to (+)" aria-label="Phóng to">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    </button>
                    <button class="misutech_lb_btn" id="misutech_lb_zoom_reset" type="button" title="Xoay ảnh 90° (R)" aria-label="Xoay ảnh 90°">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    </button>
                    <button class="misutech_lb_btn" id="misutech_lb_fullscreen" type="button" title="Toàn màn hình" aria-label="Toàn màn hình">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
                    </button>
                    <button class="misutech_lb_btn misutech_lb_btn_close" type="button" data-close-modal title="Đóng (Esc)" aria-label="Đóng">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Main Stage & Navigation --}}
            <div class="misutech_lightbox_stage" id="misutech_lightbox_stage">
                <button class="misutech_lb_nav misutech_lb_nav_prev" id="misutech_lb_prev" type="button" aria-label="Ảnh trước">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                </button>

                <div class="misutech_lightbox_image_wrapper" id="misutech_lb_wrapper">
                    <img class="misutech_product_modal_image misutech_lb_img" id="misutech_lb_main_img"
                        src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                        alt="{{ $product->name }}" width="800" height="800" decoding="async" draggable="false">
                </div>

                <button class="misutech_lb_nav misutech_lb_nav_next" id="misutech_lb_next" type="button" aria-label="Ảnh tiếp theo">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            {{-- Bottom Thumbnail Strip --}}
            <div class="misutech_lightbox_thumbs_bar" id="misutech_lb_thumbs_bar">
                <div class="misutech_lightbox_thumbs_track" id="misutech_lb_thumbs_track">
                    <button class="misutech_lb_thumb_item misutech_lb_active" type="button"
                        data-index="0" data-src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                        aria-label="Ảnh 1">
                        <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                            alt="{{ $product->name }}" width="60" height="60" loading="lazy" decoding="async" draggable="false">
                    </button>
                    @foreach ($product->galleryMedia as $i => $img)
                        <button class="misutech_lb_thumb_item" type="button"
                            data-index="{{ $i + 1 }}" data-src="{{ $img->url }}"
                            aria-label="Ảnh {{ $i + 2 }}">
                            <img src="{{ $img->url }}" alt="{{ $img->alt ?? $product->name }}" width="60" height="60" loading="lazy" decoding="async" draggable="false">
                        </button>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Hỏi đáp & Báo giá --}}
    <div class="misutech_product_modal" data-modal="question" hidden>
        <div class="misutech_product_modal_dialog" role="dialog" aria-modal="true"
            aria-labelledby="misutech_product_question_title">
            <button class="misutech_product_modal_close" type="button" data-close-modal aria-label="Đóng">✕</button>
            <div class="misutech_modal_header">
                <div class="misutech_modal_icon">💬</div>
                <div>
                    <h3 class="misutech_product_modal_title" id="misutech_product_question_title">Tư vấn kỹ thuật &amp;
                        Báo giá</h3>
                    <p class="misutech_product_modal_text">Đội ngũ kỹ sư MISUTECH sẽ phản hồi qua điện thoại / Zalo trong
                        vòng 15 phút.</p>
                </div>
            </div>

            <div class="misutech_modal_product_preview">
                <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                    alt="{{ $product->name }}" width="60" height="60" loading="lazy" decoding="async">
                <div class="misutech_modal_product_meta">
                    <h4 class="misutech_modal_pname">{{ $product->name }}</h4>
                    <p class="misutech_modal_psku">
                        <span>Mã SP: <strong>{{ $product->sku ?: 'Theo model' }}</strong></span>
                        @if ($product->brand)
                            <span>Hãng: <strong>{{ $product->brand->name }}</strong></span>
                        @endif
                    </p>
                </div>
            </div>

            <form class="misutech_product_question_form" action="{{ route('contact.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="subject"
                    value="[Tư vấn SP] {{ $product->name }} (Mã: {{ $product->sku }})">
                <input type="hidden" name="_form_time_token"
                    value="{{ \Illuminate\Support\Facades\Crypt::encryptString((string) (time() - 5)) }}">

                <div class="misutech_modal_form_grid">
                    <div class="misutech_modal_form_group">
                        <label class="misutech_modal_label">Họ và tên <span class="required">*</span></label>
                        <input class="misutech_product_modal_input" type="text" name="fullname"
                            placeholder="Ví dụ: Nguyễn Văn A" required>
                    </div>
                    <div class="misutech_modal_form_group">
                        <label class="misutech_modal_label">Số điện thoại / Zalo <span class="required">*</span></label>
                        <input class="misutech_product_modal_input" type="tel" name="phone"
                            placeholder="Ví dụ: 0912345678" required>
                    </div>
                </div>

                <div class="misutech_modal_form_group">
                    <label class="misutech_modal_label">Email nhận báo giá (Tùy chọn)</label>
                    <input class="misutech_product_modal_input" type="email" name="email"
                        placeholder="example@company.com">
                </div>

                <div class="misutech_modal_form_group">
                    <label class="misutech_modal_label">Nội dung câu hỏi / Số lượng cần mua <span
                            class="required">*</span></label>
                    <textarea class="misutech_product_modal_textarea" name="message" rows="3"
                        placeholder="Ví dụ: Tôi cần tư vấn thông số kỹ thuật và báo giá cho 10 sản phẩm..." required></textarea>
                </div>

                <div class="misutech_modal_error_msg"
                    style="display: none; color: #dc2626; font-size: 12.5px; margin-bottom: 10px;"></div>

                <button class="misutech_product_modal_submit" type="submit">
                    <span class="btn-text">GỬI YÊU CẦU CHO KỸ SƯ</span>
                </button>
            </form>
        </div>
    </div>

    {{-- Modal FAQ --}}
    <div class="misutech_product_modal" data-modal="faq" hidden>
        <div class="misutech_product_modal_dialog" role="dialog" aria-modal="true"
            aria-labelledby="misutech_product_faq_title">
            <button class="misutech_product_modal_close" type="button" data-close-modal aria-label="Đóng">✕</button>
            <div class="misutech_modal_header">
                <div class="misutech_modal_icon">▣</div>
                <div>
                    <h3 class="misutech_product_modal_title" id="misutech_product_faq_title">Câu hỏi thường gặp (FAQ)</h3>
                    <p class="misutech_product_modal_text">Các thông tin quan trọng khi mua thiết bị tại MISUTECH</p>
                </div>
            </div>
            <div class="misutech_product_faq_list">
                <details class="misutech_product_faq_item" open>
                    <summary>1. Sản phẩm có đầy đủ chứng chỉ CO, CQ không?</summary>
                    <p>Có. 100% sản phẩm do MISUTECH cung cấp đều là hàng chính hãng từ nhà sản xuất, có đầy đủ chứng nhận
                        xuất xứ (CO), chứng nhận chất lượng (CQ) và hóa đơn VAT hợp lệ.</p>
                </details>
                <details class="misutech_product_faq_item">
                    <summary>2. Thời gian giao hàng bao lâu?</summary>
                    <p>Nội thành Hải Phòng, Hà Nội, TP.HCM giao hỏa tốc trong 2 – 4 giờ. Các tỉnh thành khác giao từ 1 – 3
                        ngày làm việc qua chuyển phát nhanh hoặc chành xe theo yêu cầu.</p>
                </details>
                <details class="misutech_product_faq_item">
                    <summary>3. Chính sách bảo hành và đổi trả như thế nào?</summary>
                    <p>Sản phẩm được bảo hành chính hãng 12 tháng. Đổi mới 1 - 1 trong vòng 7 ngày đầu nếu phát sinh lỗi kỹ
                        thuật do nhà sản xuất.</p>
                </details>
                <details class="misutech_product_faq_item">
                    <summary>4. Có được hỗ trợ kỹ thuật cài đặt &amp; đấu nối không?</summary>
                    <p>Đội ngũ kỹ sư tự động hóa của MISUTECH sẵn sàng hỗ trợ tư vấn sơ đồ dây, cấu hình thông số và xử lý
                        sự cố 24/7 qua Hotline / Zalo.</p>
                </details>
            </div>
        </div>
    </div>

    {{-- Modal Chia sẻ --}}
    <div class="misutech_product_modal" data-modal="share" hidden>
        <div class="misutech_product_modal_dialog" role="dialog" aria-modal="true"
            aria-labelledby="misutech_product_share_title">
            <button class="misutech_product_modal_close" type="button" data-close-modal aria-label="Đóng">✕</button>
            <div class="misutech_modal_header">
                <div class="misutech_modal_icon">↗</div>
                <div>
                    <h3 class="misutech_product_modal_title" id="misutech_product_share_title">Chia sẻ sản phẩm</h3>
                    <p class="misutech_product_modal_text">Sao chép đường dẫn hoặc chia sẻ trực tiếp qua mạng xã hội</p>
                </div>
            </div>
            <div class="misutech_product_copy_control">
                <input class="misutech_product_copy_input" type="text" value="{{ request()->url() }}" readonly>
                <button class="misutech_product_copy_button" type="button">COPY LINK</button>
            </div>
        </div>
    </div>

    <div class="misutech_product_modal" data-modal="quick" hidden>
        <div class="misutech_product_modal_dialog misutech_product_quick_dialog" role="dialog" aria-modal="true"
            aria-labelledby="misutech_product_quick_title">
            <button class="misutech_product_modal_close" type="button" data-close-modal aria-label="Đóng">✕</button>
            <img class="misutech_product_quick_image" src="" alt="Sản phẩm liên quan">
            <div>
                <h2 class="misutech_product_modal_title" id="misutech_product_quick_title"></h2>
                <strong class="misutech_product_quick_price"></strong>
                <button class="misutech_product_modal_submit misutech_product_quick_add" type="button">THÊM VÀO GIỎ
                    HÀNG</button>
            </div>
        </div>
    </div>

    @if (!empty($embeddedSeriesModels))
        <script id="misutech_embedded_models" type="application/json">
            {!! json_encode($embeddedSeriesModels, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
    @endif
@endsection
