@extends('clients.layouts.master')

@section('title', ($category->name ?? 'Danh mục') . ' - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $catTitle = ($category->name ?? 'Danh mục') . ' - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $catUrl = route('categories.show', $category->slug);
        $catDesc = !empty($category->content) 
            ? Str::limit(strip_tags($category->content), 160) 
            : 'Khám phá các sản phẩm ' . $category->name . ' chính hãng, chất lượng cao, giá tốt và bảo hành uy tín tại ' . ($settings->company ?? 'MISUTECH') . '.';
        $catKeywords = $category->name . ', thiết bị ' . $category->name . ', ' . $category->name . ' chính hãng, tự động hóa, ' . ($settings->name ?? 'MISUTECH');
        $catImage = !empty($category->icon) 
            ? asset('storage/clients/imgs/categories/' . $category->icon) 
            : (!empty($settings->og_image) 
                ? (Str::startsWith($settings->og_image, ['http://', 'https://']) ? $settings->og_image : asset('storage/clients/imgs/settings/' . $settings->og_image)) 
                : asset('storage/clients/imgs/settings/banner-seo-misutech.jpg'));
        $logoUrl = !empty($settings->site_logo)
            ? (Str::startsWith($settings->site_logo, ['http://', 'https://']) ? $settings->site_logo : asset('storage/clients/imgs/settings/' . $settings->site_logo))
            : asset('storage/clients/imgs/settings/logo-misutech.png');
        $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
        $hotline = $settings->hotline ?? ($settings->phone ?? '0866555212');
        $email = $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn';
        $address = $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam';
        $fbUrl = $settings->facebook ?? ($settings->facebook_url ?? 'https://www.facebook.com/misutech.vn');
        $zaloUrl = $settings->zalo ?? ($settings->zalo_url ?? 'https://zalo.me/0866555212');

        $itemList = [];
        if (isset($products) && $products->isNotEmpty()) {
            foreach ($products as $idx => $p) {
                $itemList[] = [
                    "@type" => "ListItem",
                    "position" => $idx + 1,
                    "url" => route('product.show', $p->slug),
                    "name" => $p->name
                ];
            }
        }

        $schemaCategory = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $catImage,
                    "url" => route('home.index'),
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $logoUrl
                    ],
                    "email" => $email,
                    "telephone" => $hotline,
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
                            "contactType" => "customer service",
                            "availableLanguage" => ["Vietnamese"],
                            "areaServed" => "VN"
                        ]
                    ],
                    "sameAs" => array_filter([$fbUrl, $zaloUrl])
                ],
                [
                    "@type" => "WebSite",
                    "@id" => route('home.index') . "#website",
                    "url" => route('home.index'),
                    "name" => $settings->name ?? "MISUTECH",
                    "publisher" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "potentialAction" => [
                        "@type" => "SearchAction",
                        "target" => route('shop.index') . "?tim-kiem={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $catUrl . "#webpage",
                    "url" => $catUrl,
                    "name" => $category->name,
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $catUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $catImage
                    ],
                    "datePublished" => optional($category->created_at)->toDateString() ?? now()->toDateString(),
                    "dateModified" => optional($category->updated_at)->toDateString() ?? now()->toDateString()
                ],
                [
                    "@type" => "LocalBusiness",
                    "@id" => route('home.index') . "#localbusiness",
                    "name" => $companyName,
                    "image" => $catImage,
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $logoUrl
                    ],
                    "url" => route('home.index'),
                    "telephone" => $hotline,
                    "email" => $email,
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
                    ],
                    "sameAs" => array_filter([$fbUrl, $zaloUrl])
                ],
                [
                    "@type" => "BreadcrumbList",
                    "@id" => $catUrl . "#breadcrumb",
                    "itemListElement" => [
                        [
                            "@type" => "ListItem",
                            "position" => 1,
                            "item" => [
                                "@id" => route('home.index'),
                                "name" => "Trang chủ"
                            ]
                        ],
                        [
                            "@type" => "ListItem",
                            "position" => 2,
                            "item" => [
                                "@id" => route('shop.index'),
                                "name" => "Cửa hàng"
                            ]
                        ],
                        [
                            "@type" => "ListItem",
                            "position" => 3,
                            "item" => [
                                "@id" => $catUrl,
                                "name" => $category->name
                            ]
                        ]
                    ]
                ],
                [
                    "@type" => "ItemList",
                    "@id" => $catUrl . "#itemlist",
                    "url" => $catUrl,
                    "name" => "Danh sách sản phẩm " . $category->name,
                    "itemListOrder" => "https://schema.org/ItemListOrderDescending",
                    "numberOfItems" => count($itemList),
                    "itemListElement" => $itemList
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $catUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $catUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $catUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $catKeywords }}">
    <meta name="description" content="{{ $catDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $catTitle }}">
    <meta property="og:description" content="{{ $catDesc }}">
    <meta property="og:url" content="{{ $catUrl }}">
    <meta property="og:image" content="{{ $catImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $catTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $catTitle }}">
    <meta name="twitter:description" content="{{ $catDesc }}">
    <meta name="twitter:image" content="{{ $catImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaCategory, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/category.css') }}?v={{ file_exists(public_path('clients/css/category.css')) ? filemtime(public_path('clients/css/category.css')) : time() }}">
@endpush

@push('scripts')
    <script>
        window.currentCategorySlug = "{{ $category->slug ?? '' }}";
    </script>
    <script src="{{ asset('clients/js/category.js') }}?v={{ file_exists(public_path('clients/js/category.js')) ? filemtime(public_path('clients/js/category.js')) : time() }}"></script>
@endpush

@section('content')
    @php
        $catBannerUrl = asset('storage/clients/imgs/banners/banner-category.png');
        if (!empty($category->banner)) {
            if (Str::startsWith($category->banner, ['http://', 'https://'])) {
                $catBannerUrl = $category->banner;
            } elseif (file_exists(public_path('storage/clients/imgs/banners/' . $category->banner))) {
                $catBannerUrl = asset('storage/clients/imgs/banners/' . $category->banner);
            } elseif (file_exists(public_path('storage/clients/imgs/categories/' . $category->banner))) {
                $catBannerUrl = asset('storage/clients/imgs/categories/' . $category->banner);
            }
        } else {
            // Tự động kiểm tra file theo slug trong folder banners
            $possibleFiles = [
                $category->slug . '.png',
                $category->slug . '.jpg',
                $category->slug . '.webp',
                $category->slug . '.jpeg',
            ];
            foreach ($possibleFiles as $pFile) {
                if (file_exists(public_path('storage/clients/imgs/banners/' . $pFile))) {
                    $catBannerUrl = asset('storage/clients/imgs/banners/' . $pFile);
                    break;
                }
            }
        }
    @endphp
    <section
        style="background-image:url('{{ $catBannerUrl }}'); background-repeat: no-repeat; background-size: cover; background-position: center;"
        class="misutech_home_page_intro">
        <div class="misutech_home_container misutech_home_page_intro_inner">
            <h1 class="misutech_home_page_title">{{ $category->name }}</h1>
            <nav class="misutech_home_breadcrumb" aria-label="Breadcrumb">
                <a class="misutech_home_breadcrumb_link" href="{{ route('home.index') }}">Trang chủ</a>
                <span class="misutech_home_breadcrumb_separator">>></span>
                <a class="misutech_home_breadcrumb_link" href="{{ route('shop.index') }}">Cửa hàng</a>
                <span class="misutech_home_breadcrumb_separator">>></span>
                <span>{{ $category->name }}</span>
            </nav>
        </div>
    </section>

    <section class="misutech_home_categories" id="misutech_home_categories" aria-label="Product categories">
        <div class="misutech_home_container misutech_home_category_carousel">
            <button class="misutech_home_category_arrow" type="button" data-category-direction="-1"
                aria-label="Danh mục trước">‹</button>
            <div class="misutech_home_category_track_wrapper" style="overflow: hidden; width: 100%;">
                <div class="misutech_home_category_track">
                    @foreach ($parentCategories as $parent)
                        <button class="misutech_home_category_card" type="button" data-category="{{ $parent->slug }}">
                            <div class="misutech_home_category_image" style="border-radius: 50%; overflow: hidden;">
                                <a href="{{ route('categories.show', $parent->slug) }}">
                                    <img src="{{ asset('storage/clients/imgs/categories/' . ($parent->icon ?? 'no-image.png')) }}"
                                        alt="{{ $parent->name ?? 'No image' }}"
                                        style="width: 100%; height: 100%; object-fit: contain;">
                                </a>
                            </div>
                            <strong class="misutech_home_category_name">
                                <a
                                    href="{{ route('categories.show', $parent->slug) }}">{{ $parent->name ?? 'No image' }}</a>
                            </strong>
                            <span class="misutech_home_category_count">{{ $parent->products_count }} Sản phẩm</span>
                        </button>
                    @endforeach
                </div>
            </div>
            <button class="misutech_home_category_arrow" type="button" data-category-direction="1"
                aria-label="Danh mục tiếp theo">›</button>
        </div>
    </section>

    <section class="misutech_home_catalog" id="misutech_home_products">
        <div class="misutech_home_container misutech_home_catalog_layout">
            <aside class="misutech_home_filters" aria-label="Bộ lọc sản phẩm">
                <h2 class="misutech_home_filter_title"><span class="misutech_home_filter_title_icon">☷</span> LỌC THEO
                </h2>
                <button class="misutech_home_clear_filters" type="button">♲ XÓA TẤT CẢ</button>

                <section class="misutech_home_filter_group">
                    <div class="misutech_home_filter_group_header">
                        <h3 class="misutech_home_filter_group_title">⌃ &nbsp; GIÁ</h3>
                        <button class="misutech_home_filter_reset" type="button" data-reset="price">ĐẶT LẠI</button>
                    </div>
                    <div class="misutech_home_price_slider">
                        <input class="misutech_home_price_range" type="range" min="0" max="{{ $maxPrice }}"
                            value="0" data-price="min" aria-label="Giá thấp nhất">
                        <input class="misutech_home_price_range" type="range" min="0" max="{{ $maxPrice }}"
                            value="{{ $maxPrice }}" data-price="max" aria-label="Giá cao nhất">
                    </div>
                    <div class="misutech_home_price_ticks">
                        <span>0</span><span>{{ round($maxPrice * 0.25) }}</span><span>{{ round($maxPrice * 0.5) }}</span><span>{{ round($maxPrice * 0.75) }}</span><span>{{ $maxPrice }}</span>
                    </div>
                    <div class="misutech_home_price_inputs"><input class="misutech_home_price_input" type="number"
                            min="0" max="{{ $maxPrice }}" value="0" data-price-input="min"
                            aria-label="Giá thấp nhất value"><span>—</span><input class="misutech_home_price_input"
                            type="number" min="0" max="{{ $maxPrice }}" value="{{ $maxPrice }}"
                            data-price-input="max" aria-label="Giá cao nhất value"></div>
                </section>

                <section class="misutech_home_filter_group">
                    <div class="misutech_home_filter_group_header">
                        <h3 class="misutech_home_filter_group_title">⌃ &nbsp; THƯƠNG HIỆU</h3>
                        <button class="misutech_home_filter_reset" type="button" data-reset="brand">ĐẶT LẠI</button>
                    </div>
                    @php
                        $selectedBrands = array_filter(explode(',', (string)request('brands', '')));
                    @endphp
                    @foreach ($brands as $brand)
                        <label class="misutech_home_checkbox_label">
                            <input class="misutech_home_filter_checkbox" type="checkbox" name="brand"
                                value="{{ $brand->slug }}" data-filter="brand"
                                {{ in_array($brand->slug, $selectedBrands) ? 'checked' : '' }}>
                            <span class="misutech_home_checkbox_box"></span>{{ $brand->name }}
                            <b>({{ $brand->products_count }})</b>
                        </label>
                    @endforeach
                </section>
            </aside>

            <div class="misutech_home_product_area">
                <div class="misutech_home_product_toolbar">
                    <label class="misutech_home_sort_label">Sắp xếp theo
                        <select class="misutech_home_sort_select" aria-label="Sắp xếp sản phẩm">
                            <option value="featured">Nổi bật</option>
                            <option value="price-asc">Giá, thấp đến cao</option>
                            <option value="price-desc">Giá, cao đến thấp</option>
                            <option value="name">Theo thứ tự A-Z</option>
                        </select>
                    </label>
                    <div class="misutech_home_view_modes" role="group" aria-label="Product view">
                        <button class="misutech_home_view_button" type="button" data-columns="3" aria-pressed="true"
                            aria-label="Lưới 3 cột">▦</button>
                        <button class="misutech_home_view_button" type="button" data-columns="2" aria-pressed="false"
                            aria-label="Lưới 2 cột">▦</button>
                        <button class="misutech_home_view_button" type="button" data-columns="1" aria-pressed="false"
                            aria-label="Dạng danh sách">☷</button>
                    </div>
                </div>

                <div class="misutech_home_product_grid" data-grid-columns="3">
                    @forelse($products as $product)
                        <article class="misutech_home_product_card" data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}"
                            data-brand="{{ $product->brand ? $product->brand->slug : '' }}" data-availability="in-stock"
                            data-category="{{ $product->category ? $product->category->slug : '' }}">

                            @if ($product->sale_price && $product->sale_price < $product->price)
                                <span
                                    class="misutech_home_sale_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
                            @endif

                            <div class="misutech_home_product_media">
                                <a href="{{ route('product.show', $product->slug) }}">
                                    <img src="{{ $product->thumbnailMedia->first()?->url ?? asset('storage/clients/imgs/products/no-image.png') }}"
                                        alt="{{ $product->name ?? 'No image' }}"
                                        loading="lazy"
                                        decoding="async">
                                </a>
                                <div class="misutech_home_product_actions">
                                    <button class="misutech_home_product_action" type="button" data-favorite
                                        aria-label="Thêm vào yêu thích">♡</button>
                                    <button class="misutech_home_product_action" type="button" data-cart
                                        aria-label="Thêm vào giỏ hàng">＋</button>
                                </div>
                            </div>

                            <div class="misutech_home_product_info">
                                @if ($product->brand || $product->sku)
                                    <div class="misutech_home_product_meta_tags">
                                        @if ($product->brand)
                                            <span class="product_meta_brand">{{ $product->brand->name }}</span>
                                        @endif
                                        @if ($product->sku)
                                            <span class="product_meta_sku">Mã: {{ $product->sku }}</span>
                                        @endif
                                    </div>
                                @endif

                                <h3 class="misutech_home_product_name">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        style="color: inherit; text-decoration: none;">{{ $product->name }}</a>
                                </h3>

                                <div class="misutech_home_product_rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($product->rating_average ?? 5))
                                            <span class="star-filled">★</span>
                                        @else
                                            <span class="star-empty">☆</span>
                                        @endif
                                    @endfor
                                    <span class="rating_text">({{ number_format($product->rating_average ?? 5, 1) }})</span>
                                </div>

                                {{-- Khung thông tin chỉ hiển thị ở Dạng Danh Sách (1 Cột) --}}
                                <div class="misutech_home_product_list_desc">
                                    @if (!empty($product->short_description))
                                        <p class="product_short_desc">{{ Str::limit(strip_tags($product->short_description), 140) }}</p>
                                    @endif
                                    <ul class="product_feature_badges">
                                        <li><span class="badge_icon">✓</span> Hàng chính hãng 100%</li>
                                        <li><span class="badge_icon">✓</span> Bảo hành 12 tháng</li>
                                        <li><span class="badge_icon">✓</span> Đầy đủ CO/CQ & VAT</li>
                                        <li><span class="badge_icon">✓</span> Sẵn hàng giao nhanh</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="misutech_home_product_footer_action">
                                <div class="misutech_home_product_stock_badge">
                                    <span class="stock_dot"></span> Còn hàng
                                </div>

                                <div class="misutech_home_product_price_wrapper">
                                    @if ($product->sale_price && $product->sale_price < $product->price)
                                        <div class="misutech_home_product_price_line">
                                            <strong
                                                class="misutech_home_product_price">{{ number_format($product->sale_price, 0, ',', '.') }}
                                                VNĐ</strong>
                                            <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }}
                                                VNĐ</del>
                                        </div>
                                    @elseif($product->price > 0)
                                        <div class="misutech_home_product_price_line">
                                            <strong
                                                class="misutech_home_product_price">{{ number_format($product->price, 0, ',', '.') }}
                                                VNĐ</strong>
                                        </div>
                                    @else
                                        <div class="misutech_home_product_price_line">
                                            <strong class="misutech_home_product_price" style="color: #003b70;">Liên hệ báo giá</strong>
                                        </div>
                                    @endif
                                </div>

                                <div class="misutech_home_product_list_buttons">
                                    <a href="{{ route('product.show', $product->slug) }}" class="btn_view_product">
                                        Xem chi tiết
                                    </a>
                                    <button class="btn_add_to_cart" type="button" data-cart>
                                        + Thêm giỏ hàng
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="misutech_home_no_products">Không có sản phẩm nào.</div>
                    @endforelse
                </div>

                <div class="misutech_home_no_results" hidden>Không có sản phẩm nào phù hợp với bộ lọc.</div>

                <div class="misutech_home_load_area" @if ($totalProducts == 0) style="display: none;" @endif>
                    <p class="misutech_home_result_count">Hiển thị <strong
                            id="visible-count">{{ $products->count() }}</strong> trên <span
                            id="total-count">{{ $totalProducts }}</span> sản
                        phẩm.</p>
                    <div class="misutech_home_progress"><span class="misutech_home_progress_value" id="progress-bar"
                            style="width: {{ $totalProducts > 0 ? ($products->count() / $totalProducts) * 100 : 0 }}%;"></span>
                    </div>
                    @if ($products->count() < $totalProducts)
                        <button class="misutech_home_load_more" type="button" id="load-more-btn"
                            data-offset="{{ $products->count() }}">TẢI THÊM SẢN PHẨM</button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="misutech_home_description" id="misutech_home_description">
        <div class="misutech_home_container" style="display: grid; grid-template-columns: 1fr 2fr; gap: 60px;">
            <div class="misutech_home_tags_wrapper">
                <h2 class="misutech_home_tags_title"
                    style="margin-bottom: 20px; font-size: 16px; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    TỪ KHÓA PHỔ BIẾN
                </h2>
                <div class="misutech_home_tags_cloud">
                    @foreach ($parentCategories->take(8) as $category)
                        <a href="{{ route('categories.show', $category->slug) }}"
                            class="misutech_home_tag_item">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>

            <article class="misutech_home_description_article" style="max-width: none;">
                @if (!empty($category->content))
                    <h2 class="misutech_home_description_title"
                        style="font-size: 24px; margin-bottom: 20px; color: var(--misutech_home_primary);">
                        {{ $category->name }}</h2>
                    <div class="misutech_home_category_content"
                        style="font-size: 15px; line-height: 1.8; position: relative;">
                        <div class="misutech_home_category_content_inner"
                            style="max-height: 150px; overflow: hidden; transition: max-height 0.3s ease;">
                            {!! $category->content !!}
                        </div>
                        <div class="misutech_home_category_content_overlay"
                            style="position: absolute; bottom: 0; left: 0; width: 100%; height: 50px; background: linear-gradient(to bottom, rgba(255,255,255,0), rgba(255,255,255,1));">
                        </div>
                    </div>
                    <button class="misutech_home_see_more_html" type="button" aria-expanded="false"
                        style="display: inline-flex; align-items: center; gap: 8px; margin-top: 15px; font-size: 13px; color: var(--misutech_home_primary); background: transparent; padding: 0; font-weight: 700; border: none; cursor: pointer;">
                        <span>ĐỌC THÊM</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="misutech_home_see_more_icon" style="transition: transform 0.3s ease;">
                            <path d="M5 12h14"></path>
                            <path d="M12 5l7 7-7 7"></path>
                        </svg>
                    </button>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const btn = document.querySelector('.misutech_home_see_more_html');
                            const inner = document.querySelector('.misutech_home_category_content_inner');
                            const overlay = document.querySelector('.misutech_home_category_content_overlay');

                            if (btn && inner && overlay) {
                                // If content is too short, hide the button and overlay
                                if (inner.scrollHeight <= 150) {
                                    btn.style.display = 'none';
                                    overlay.style.display = 'none';
                                    inner.style.maxHeight = 'none';
                                }

                                btn.addEventListener('click', function() {
                                    const icon = this.querySelector('svg');
                                    const span = this.querySelector('span');

                                    if (this.getAttribute('aria-expanded') === 'false') {
                                        inner.style.maxHeight = inner.scrollHeight + 'px';
                                        overlay.style.display = 'none';
                                        this.setAttribute('aria-expanded', 'true');
                                        span.innerText = 'THU GỌN';
                                        icon.style.transform = 'rotate(180deg)';
                                    } else {
                                        inner.style.maxHeight = '150px';
                                        overlay.style.display = 'block';
                                        this.setAttribute('aria-expanded', 'false');
                                        span.innerText = 'ĐỌC THÊM';
                                        icon.style.transform = 'rotate(0deg)';
                                    }
                                });
                            }
                        });
                    </script>
                @endif
            </article>
        </div>
    </section>

    <section class="misutech_home_benefits" aria-label="Shopping benefits"
        style="background: var(--misutech_home_white); padding: 60px 0;">
        <div class="misutech_home_benefit_grid" style="gap: 40px;">
            <article class="misutech_home_benefit"
                style="align-items: center; padding: 30px; border-radius: 12px; background: #f8f9fa; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div class="misutech_home_benefit_icon_wrapper"
                    style="width: 50px; height: 50px; background: var(--misutech_home_white); aspect-ratio: 1 / 1; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); color: var(--misutech_home_primary);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                </div>
                <div>
                    <h2 class="misutech_home_benefit_title"
                        style="font-size: 14px; margin-bottom: 8px; color: var(--misutech_home_ink);">GIAO HÀNG MIỄN PHÍ
                    </h2>
                    <p class="misutech_home_benefit_text" style="font-size: 13px; line-height: 1.5; color: #666;">Không lo
                        phí vận chuyển. Bạn chỉ thanh toán đúng giá niêm yết của sản phẩm.</p>
                </div>
            </article>

            <article class="misutech_home_benefit"
                style="align-items: center; padding: 30px; border-radius: 12px; background: #f8f9fa; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div class="misutech_home_benefit_icon_wrapper"
                    style="width: 50px; height: 50px; background: var(--misutech_home_white); aspect-ratio: 1 / 1; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); color: var(--misutech_home_primary);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path
                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h2 class="misutech_home_benefit_title"
                        style="font-size: 14px; margin-bottom: 8px; color: var(--misutech_home_ink);">GIAO HÀNG TOÀN QUỐC
                    </h2>
                    <p class="misutech_home_benefit_text" style="font-size: 13px; line-height: 1.5; color: #666;">Hợp tác
                        cùng đối tác uy tín, đưa sản phẩm an toàn đến mọi miền đất nước.</p>
                </div>
            </article>

            <article class="misutech_home_benefit"
                style="align-items: center; padding: 30px; border-radius: 12px; background: #f8f9fa; transition: transform 0.3s ease, box-shadow 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <div class="misutech_home_benefit_icon_wrapper"
                    style="width: 50px; height: 50px; background: var(--misutech_home_white); aspect-ratio: 1 / 1; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); color: var(--misutech_home_primary);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <div>
                    <h2 class="misutech_home_benefit_title"
                        style="font-size: 14px; margin-bottom: 8px; color: var(--misutech_home_ink);">BẢO HÀNH CHÍNH HÃNG
                    </h2>
                    <p class="misutech_home_benefit_text" style="font-size: 13px; line-height: 1.5; color: #666;">An tâm
                        tuyệt đối với chính sách bảo hành 1 đổi 1 và hoàn tiền trong 30 ngày.</p>
                </div>
            </article>
        </div>
    </section>

    @if (count($viewedProducts) > 0)
        <section class="misutech_home_recently_viewed">
            <div class="misutech_home_container">
                <h2 class="misutech_home_recent_title" style="margin-bottom: 40px;">Sản Phẩm Đã Xem</h2>
                <div class="misutech_home_product_grid" style="grid-template-columns: repeat(4, 1fr);">
                    @foreach ($viewedProducts as $product)
                        @include('clients.pages.shop.partials.product_card', ['product' => $product])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
