@extends('clients.layouts.master')

@section('title', 'Trung Tâm Tài Liệu & Catalog Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $docTitle = 'Trung Tâm Tài Liệu & Catalog Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $docUrl = route('documents.index');
        $docDesc = 'Tra cứu và tải về miễn phí toàn bộ tài liệu kỹ thuật, hướng dẫn sử dụng, Catalog PDF, Datasheet, sơ đồ đấu nối biến tần, PLC, cảm biến chính hãng tại ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')) . '.';
        $docKeywords = 'tài liệu kỹ thuật, catalog tự động hóa, datasheet biến tần, hướng dẫn cài đặt plc, sơ đồ đấu dây cảm biến, ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $docImage = !empty($settings->og_image) 
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

        $docItemList = [];
        if (isset($documents) && $documents->isNotEmpty()) {
            foreach ($documents as $idx => $doc) {
                $docItemList[] = [
                    "@type" => "ListItem",
                    "position" => $idx + 1,
                    "url" => $doc->url ? asset('storage/' . $doc->url) : $docUrl,
                    "name" => $doc->title ?? ($doc->original_name ?? $doc->filename)
                ];
            }
        }

        $schemaDoc = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $docImage,
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
                        "target" => route('documents.index') . "?q={search_term_string}",
                        "query-input" => "required name=search_term_string"
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $docUrl . "#webpage",
                    "url" => $docUrl,
                    "name" => "Trung tâm tài liệu & Catalog kỹ thuật",
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $docUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $docImage
                    ]
                ],
                [
                    "@type" => "BreadcrumbList",
                    "@id" => $docUrl . "#breadcrumb",
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
                                "@id" => $docUrl,
                                "name" => "Tài liệu kỹ thuật"
                            ]
                        ]
                    ]
                ],
                [
                    "@type" => "ItemList",
                    "@id" => $docUrl . "#itemlist",
                    "url" => $docUrl,
                    "name" => "Danh sách tài liệu kỹ thuật & Catalog",
                    "itemListOrder" => "https://schema.org/ItemListOrderDescending",
                    "numberOfItems" => count($docItemList),
                    "itemListElement" => $docItemList
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $docUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $docUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $docUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $docKeywords }}">
    <meta name="description" content="{{ $docDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $docTitle }}">
    <meta property="og:description" content="{{ $docDesc }}">
    <meta property="og:url" content="{{ $docUrl }}">
    <meta property="og:image" content="{{ $docImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $docTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $docTitle }}">
    <meta name="twitter:description" content="{{ $docDesc }}">
    <meta name="twitter:image" content="{{ $docImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaDoc, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/documents.css') }}">
@endpush

@section('content')
    <div class="misutech_doc_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_doc_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_doc_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">Tài liệu kỹ thuật</li>
                </ol>
            </div>
        </nav>

        {{-- Page Header --}}
        <section class="misutech_doc_header_section">
            <div class="misutech_home_container">
                <div class="misutech_doc_header_card">
                    <div class="misutech_doc_header_info">
                        <span class="misutech_doc_badge_top">TRUNG TÂM TÀI LIỆU KỸ THUẬT</span>
                        <h1 class="misutech_doc_main_title">Tài liệu kỹ thuật & Catalog Datasheet</h1>
                        <p class="misutech_doc_desc">
                            Tra cứu, xem trực tiếp và tải về toàn bộ cẩm nang hướng dẫn sử dụng, thông số kỹ thuật, bản vẽ CAD và Catalog PDF chính hãng phục vụ thiết kế, lắp đặt và vận hành hệ thống tự động hóa.
                        </p>
                    </div>
                    <div class="misutech_doc_header_stat">
                        <span class="stat_num">{{ $documents->total() }}</span>
                        <span class="stat_lbl">Tài liệu sẵn sàng tải về</span>
                    </div>
                </div>
            </div>
        </section>

        {{-- Filter & Search Toolbar --}}
        <section class="misutech_doc_filter_section">
            <div class="misutech_home_container">
                <form action="{{ route('documents.index') }}" method="GET" class="misutech_doc_filter_form">
                    <div class="filter_input_group search_box">
                        <span class="input_icon">🔍</span>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Nhập tên tài liệu, model, mã SKU, từ khóa...">
                    </div>

                    <div class="filter_input_group select_box">
                        <label for="filter_brand">Thương hiệu:</label>
                        <select name="brand" id="filter_brand" onchange="this.form.submit()">
                            <option value="">-- Tất cả thương hiệu --</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->slug }}" {{ $brandSlug === $b->slug ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter_input_group select_box">
                        <label for="filter_category">Danh mục:</label>
                        <select name="category" id="filter_category" onchange="this.form.submit()">
                            <option value="">-- Tất cả danh mục --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->slug }}" {{ $catSlug === $c->slug ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter_actions">
                        <button type="submit" class="btn_filter_submit">Tìm kiếm</button>
                        @if(!empty($search) || !empty($brandSlug) || !empty($catSlug))
                            <a href="{{ route('documents.index') }}" class="btn_filter_reset">Đặt lại</a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        {{-- Documents Listing --}}
        <section class="misutech_doc_list_section" id="doc_list_section" style="scroll-margin-top: 140px;">
            <div class="misutech_home_container">
                @if($documents->isEmpty())
                    <div class="misutech_doc_empty">
                        <span class="empty_icon">📑</span>
                        <h3>Không tìm thấy tài liệu phù hợp</h3>
                        <p>Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc để xem toàn bộ danh mục tài liệu.</p>
                        <a href="{{ route('documents.index') }}" class="btn_reset_empty">Xem tất cả tài liệu</a>
                    </div>
                @else
                    <div class="misutech_doc_grid">
                        @foreach($documents as $doc)
                            @php
                                $relatedProduct = $doc->products->first();
                                $rawName = $doc->title ?: ($doc->original_name ?: $doc->filename);
                                $cleanName = preg_replace('/\.(pdf|doc|docx|zip|rar)$/i', '', $rawName);
                                if (str_contains($cleanName, '-') && !str_contains($cleanName, ' ')) {
                                    $cleanName = str_replace('-', ' ', $cleanName);
                                    $cleanName = mb_convert_case($cleanName, MB_CASE_TITLE, "UTF-8");
                                }
                                $docTitle = $cleanName;
                                $fileExt = strtoupper($doc->extension ?: pathinfo($doc->filename, PATHINFO_EXTENSION) ?: 'PDF');
                                $fileSize = $doc->size ? (round($doc->size / 1024 / 1024, 2) . ' MB') : ($fileExt . ' Document');
                            @endphp
                            <article class="misutech_doc_card">
                                <div class="misutech_doc_card_top">
                                    <div class="doc_type_tag">
                                        <span class="file_icon">📄</span>
                                        <span class="file_ext">{{ $fileExt }}</span>
                                    </div>
                                    <span class="doc_filesize">{{ $fileSize }}</span>
                                </div>

                                <div class="misutech_doc_card_main">
                                    <h2 class="doc_title" title="{{ $docTitle }}">
                                        {{ $docTitle }}
                                    </h2>

                                    @if($relatedProduct)
                                        <div class="doc_product_info">
                                            <div class="doc_product_head">
                                                <span class="info_label">Sản phẩm liên quan</span>
                                                @if($relatedProduct->brand)
                                                    <span class="meta_tag brand_tag">{{ $relatedProduct->brand->name }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('product.show', $relatedProduct->slug) }}" class="product_link" title="{{ $relatedProduct->name }}">
                                                {{ $relatedProduct->name }}
                                            </a>
                                            <div class="doc_meta_tags">
                                                @if($relatedProduct->sku)
                                                    <span class="meta_tag sku_tag">SKU: {{ $relatedProduct->sku }}</span>
                                                @endif
                                                @if($relatedProduct->category)
                                                    <span class="meta_tag cat_tag" title="{{ $relatedProduct->category->name }}">{{ $relatedProduct->category->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="doc_product_info">
                                            <div class="doc_product_head">
                                                <span class="info_label">Tài liệu kỹ thuật tổng hợp</span>
                                                <span class="meta_tag brand_tag">MISUTECH</span>
                                            </div>
                                            <div class="product_link" style="color: #64748b; font-weight: 500;">
                                                Catalog kỹ thuật & Hướng dẫn sử dụng
                                            </div>
                                            <div class="doc_meta_tags">
                                                <span class="meta_tag">Tự động hóa</span>
                                                <span class="meta_tag">Thiết bị điện</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="misutech_doc_card_footer">
                                    <a href="{{ $doc->url }}" target="_blank" rel="noopener noreferrer" class="btn_doc_view" title="Xem trực tiếp trong trình duyệt">
                                        Xem trực tiếp ↗
                                    </a>
                                    <a href="{{ route('documents.download', $doc->id) }}" class="btn_doc_download" title="Tải file về máy tính">
                                        Tải về ⤓
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($documents->hasPages())
                        <div class="misutech_doc_pagination">
                            {{ $documents->appends(request()->query())->links('clients.components.pagination') }}
                        </div>
                    @endif
                @endif
            </div>
        </section>

        {{-- Technical Support Help Box --}}
        <section class="misutech_doc_help_section">
            <div class="misutech_home_container">
                <div class="misutech_doc_help_card">
                    <div class="help_icon">☎</div>
                    <div class="help_content">
                        <h3>Cần tìm tài liệu kỹ thuật chuyên sâu hoặc báo giá giải pháp?</h3>
                        <p>Nếu bạn chưa tìm thấy tài liệu của mã sản phẩm đặc thù, hãy gửi yêu cầu cho đội ngũ kỹ sư hỗ trợ kỹ thuật 24/7 của MISUTECH.</p>
                    </div>
                    <div class="help_action">
                        <a href="tel:0866555212" class="btn_help_call">Hotline: 0866.555.212</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
