@extends('clients.layouts.master')

@section('title', ($selectedCategory ? $selectedCategory->name . ' - ' : '') . 'Tin Tức & Cẩm Nang Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $blogTitle = ($selectedCategory ? $selectedCategory->name . ' - ' : '') . 'Tin Tức & Cẩm Nang Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $blogUrl = $selectedCategory ? route('blogs.index', ['category' => $selectedCategory->slug]) : route('blogs.index');
        $blogDesc = $selectedCategory 
            ? ('Tổng hợp bài viết, cẩm nang và hướng dẫn kỹ thuật chuyên sâu về ' . $selectedCategory->name . ' tại ' . ($settings->company ?? 'MISUTECH') . '.') 
            : 'Cập nhật tin tức công nghệ tự động hóa, cẩm nang kỹ thuật, hướng dẫn cài đặt và xử lý sự cố thiết bị công nghiệp Omron, Mitsubishi, Panasonic... tại ' . ($settings->company ?? 'MISUTECH') . '.';
        $blogKeywords = ($selectedCategory ? $selectedCategory->name . ', ' : '') . 'tin tức tự động hóa, cẩm nang kỹ thuật, biến tần, PLC, cảm biến, hướng dẫn cài đặt, ' . ($settings->name ?? 'MISUTECH');
        $blogImage = !empty($settings->og_image) 
            ? (Str::startsWith($settings->og_image, ['http://', 'https://']) ? $settings->og_image : asset('storage/clients/imgs/settings/' . $settings->og_image)) 
            : asset('storage/clients/imgs/settings/banner-seo-misutech.jpg');
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
        $itemPos = 1;
        if (isset($featuredPost) && $featuredPost) {
            $itemList[] = [
                "@type" => "ListItem",
                "position" => $itemPos++,
                "url" => route('blogs.show', $featuredPost->slug),
                "name" => $featuredPost->title
            ];
        }
        if (isset($posts) && $posts->isNotEmpty()) {
            foreach ($posts as $p) {
                $itemList[] = [
                    "@type" => "ListItem",
                    "position" => $itemPos++,
                    "url" => route('blogs.show', $p->slug),
                    "name" => $p->title
                ];
            }
        }

        $breadcrumbs = [
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
                    "@id" => route('blogs.index'),
                    "name" => "Tin tức & Cẩm nang"
                ]
            ]
        ];

        if ($selectedCategory) {
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => 3,
                "item" => [
                    "@id" => $blogUrl,
                    "name" => $selectedCategory->name
                ]
            ];
        }

        $schemaBlog = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $blogImage,
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
                        "target" => route('blogs.index') . "?q={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $blogUrl . "#webpage",
                    "url" => $blogUrl,
                    "name" => $blogTitle,
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $blogUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $blogImage
                    ]
                ],
                [
                    "@type" => "LocalBusiness",
                    "@id" => route('home.index') . "#localbusiness",
                    "name" => $companyName,
                    "image" => $blogImage,
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
                    "@id" => $blogUrl . "#breadcrumb",
                    "itemListElement" => $breadcrumbs
                ],
                [
                    "@type" => "ItemList",
                    "@id" => $blogUrl . "#itemlist",
                    "url" => $blogUrl,
                    "name" => "Danh sách bài viết tin tức & cẩm nang kỹ thuật",
                    "itemListOrder" => "https://schema.org/ItemListOrderDescending",
                    "numberOfItems" => count($itemList),
                    "itemListElement" => $itemList
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $blogUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $blogUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $blogUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $blogKeywords }}">
    <meta name="description" content="{{ $blogDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $blogTitle }}">
    <meta property="og:description" content="{{ $blogDesc }}">
    <meta property="og:url" content="{{ $blogUrl }}">
    <meta property="og:image" content="{{ $blogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $blogTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $blogTitle }}">
    <meta name="twitter:description" content="{{ $blogDesc }}">
    <meta name="twitter:image" content="{{ $blogImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaBlog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/blogs.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="misutech_blog_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_blog_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_blog_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    @if($selectedCategory)
                        <li><a href="{{ route('blogs.index') }}">Tin tức & Cẩm nang</a></li>
                        <li class="separator">»</li>
                        <li class="current" aria-current="page">{{ $selectedCategory->name }}</li>
                    @else
                        <li class="current" aria-current="page">Tin tức & Cẩm nang kỹ thuật</li>
                    @endif
                </ol>
            </div>
        </nav>

        {{-- Main Container --}}
        <div class="misutech_home_container misutech_blog_layout">
            {{-- Left Content Column --}}
            <main class="misutech_blog_main">
                {{-- Category / Search Title Header --}}
                <div class="misutech_blog_header_card">
                    <span class="blog_header_badge">BẢN TIN CÔNG NGHỆ TỰ ĐỘNG HÓA</span>
                    <h1 class="blog_header_title">
                        @if($selectedCategory)
                            Chuyên mục: {{ $selectedCategory->name }}
                        @elseif(!empty($search))
                            Kết quả tìm kiếm: "{{ $search }}"
                        @else
                            Tin tức công nghệ & Cẩm nang kỹ thuật
                        @endif
                    </h1>
                    <p class="blog_header_desc">
                        Cập nhật các giải pháp tự động hóa công nghiệp mới nhất, cẩm nang hướng dẫn sử dụng, kinh nghiệm chọn thiết bị và xử lý sự cố thực tế từ đội ngũ kỹ sư MISUTECH.
                    </p>
                </div>

                {{-- Featured Post (chỉ hiển thị ở trang 1 khi không tìm kiếm) --}}
                @if($featuredPost)
                    @php
                        $featThumb = $featuredPost->thumbnailMedia->first();
                        $featThumbUrl = $featThumb ? $featThumb->url : asset('clients/imgs/default-blog.jpg');
                    @endphp
                    <article class="misutech_blog_featured_card">
                        <a href="{{ route('blogs.show', $featuredPost->slug) }}" class="featured_thumb_link">
                            <img src="{{ $featThumbUrl }}" alt="{{ $featuredPost->title }}" class="featured_thumb_img" loading="lazy" decoding="async">
                        </a>
                        <div class="featured_info">
                            <div class="featured_meta">
                                @if($featuredPost->category)
                                    <a href="{{ route('blogs.index', ['category' => $featuredPost->category->slug]) }}" class="blog_tag">
                                        {{ $featuredPost->category->name }}
                                    </a>
                                @endif
                                <span class="blog_date">{{ $featuredPost->published_at ? $featuredPost->published_at->format('d/m/Y') : '' }}</span>
                            </div>
                            <h2 class="featured_title">
                                <a href="{{ route('blogs.show', $featuredPost->slug) }}">
                                    {{ $featuredPost->title }}
                                </a>
                            </h2>
                            <p class="featured_summary">
                                {{ $featuredPost->summary }}
                            </p>
                            <a href="{{ route('blogs.show', $featuredPost->slug) }}" class="btn_read_more">
                                Đọc bài viết →
                            </a>
                        </div>
                    </article>
                @endif

                {{-- Posts Grid --}}
                @if($posts->isEmpty())
                    <div class="misutech_blog_empty">
                        <span class="empty_icon">📰</span>
                        <h3>Không tìm thấy bài viết phù hợp</h3>
                        <p>Vui lòng thử lại với từ khóa khác hoặc quay lại danh mục tất cả bài viết.</p>
                        <a href="{{ route('blogs.index') }}" class="btn_reset_blog">Xem tất cả bài viết</a>
                    </div>
                @else
                    <div class="misutech_blog_grid">
                        @foreach($posts as $p)
                            @php
                                $thumb = $p->thumbnailMedia->first();
                                $thumbUrl = $thumb ? $thumb->url : asset('clients/imgs/default-blog.jpg');
                            @endphp
                            <article class="misutech_blog_card">
                                <a href="{{ route('blogs.show', $p->slug) }}" class="blog_card_thumb">
                                    <img src="{{ $thumbUrl }}" alt="{{ $p->title }}" loading="lazy" decoding="async">
                                </a>
                                <div class="blog_card_body">
                                    <div class="blog_card_meta">
                                        @if($p->category)
                                            <a href="{{ route('blogs.index', ['category' => $p->category->slug]) }}" class="blog_card_category">
                                                {{ $p->category->name }}
                                            </a>
                                        @endif
                                        <span class="blog_card_date">{{ $p->published_at ? $p->published_at->format('d/m/Y') : '' }}</span>
                                    </div>
                                    <h2 class="blog_card_title">
                                        <a href="{{ route('blogs.show', $p->slug) }}" title="{{ $p->title }}">
                                            {{ $p->title }}
                                        </a>
                                    </h2>
                                    <p class="blog_card_summary">
                                        {{ $p->summary }}
                                    </p>
                                    <div class="blog_card_footer">
                                        <a href="{{ route('blogs.show', $p->slug) }}" class="btn_card_read">
                                            Chi tiết →
                                        </a>
                                        <span class="blog_views_count">👁 {{ $p->views_count }} lượt xem</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($posts->hasPages())
                        <div class="misutech_blog_pagination">
                            {{ $posts->appends(request()->query())->links('clients.components.pagination') }}
                        </div>
                    @endif
                @endif
            </main>

            {{-- Right Sidebar --}}
            <aside class="misutech_blog_sidebar">
                {{-- Search Box --}}
                <div class="sidebar_widget">
                    <h3 class="widget_title">Tìm kiếm bài viết</h3>
                    <form action="{{ route('blogs.index') }}" method="GET" class="sidebar_search_form">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Nhập từ khóa tìm kiếm...">
                        <button type="submit" aria-label="Tìm kiếm">🔍</button>
                    </form>
                </div>

                {{-- Categories Widget --}}
                <div class="sidebar_widget">
                    <h3 class="widget_title">Chuyên mục bài viết</h3>
                    <ul class="sidebar_cat_list">
                        <li>
                            <a href="{{ route('blogs.index') }}" class="{{ empty($catSlug) ? 'active' : '' }}">
                                <span>Tất cả bài viết</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blogs.index', ['category' => $cat->slug]) }}" class="{{ $catSlug === $cat->slug ? 'active' : '' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="cat_count">{{ $cat->posts_count }}</span>
                                </a>
                                @if($cat->children && $cat->children->count() > 0)
                                    <ul class="sidebar_subcat_list">
                                        @foreach($cat->children as $sub)
                                            <li>
                                                <a href="{{ route('blogs.index', ['category' => $sub->slug]) }}" class="{{ $catSlug === $sub->slug ? 'active' : '' }}">
                                                    <span>- {{ $sub->name }}</span>
                                                    <span class="cat_count">{{ $sub->posts_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Popular Posts Widget --}}
                @if($popularPosts->isNotEmpty())
                    <div class="sidebar_widget">
                        <h3 class="widget_title">Bài viết xem nhiều</h3>
                        <div class="sidebar_popular_list">
                            @foreach($popularPosts as $idx => $pop)
                                @php
                                    $pThumb = $pop->thumbnailMedia->first();
                                    $pThumbUrl = $pThumb ? $pThumb->url : asset('clients/imgs/default-blog.jpg');
                                @endphp
                                <article class="sidebar_popular_item">
                                    <a href="{{ route('blogs.show', $pop->slug) }}" class="pop_thumb">
                                        <img src="{{ $pThumbUrl }}" alt="{{ $pop->title }}" loading="lazy" decoding="async">
                                    </a>
                                    <div class="pop_info">
                                        <h4 class="pop_title">
                                            <a href="{{ route('blogs.show', $pop->slug) }}">
                                                {{ $pop->title }}
                                            </a>
                                        </h4>
                                        <span class="pop_date">{{ $pop->published_at ? $pop->published_at->format('d/m/Y') : '' }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Technical Support Banner --}}
                <div class="sidebar_widget sidebar_cta_box">
                    <span class="cta_icon">⚡</span>
                    <h4>Tư vấn giải pháp kỹ thuật</h4>
                    <p>Đội ngũ kỹ sư tự động hóa MISUTECH sẵn sàng tư vấn lựa chọn thiết bị và giải pháp tối ưu cho nhà máy.</p>
                    <a href="tel:0866555212" class="btn_sidebar_call">Hotline: 0866.555.212</a>
                </div>
            </aside>
        </div>
    </div>
@endsection
