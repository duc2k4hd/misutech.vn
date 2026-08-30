@extends('clients.layouts.master')

@section('title', 'Thương Hiệu Đối Tác Phân Phối Chính Hãng - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $brandsTitle = 'Thương Hiệu Đối Tác Phân Phối Chính Hãng - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $brandsUrl = route('brands.index');
        $brandsDesc = 'Danh sách đầy đủ các thương hiệu thiết bị tự động hóa và đối tác uy tín hàng đầu: Omron, Mitsubishi, Autonics, Schneider, Fuji, LS... được phân phối chính thức tại ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')) . '. Cam kết 100% chính hãng.';
        $brandsKeywords = 'thương hiệu tự động hóa, đối tác misutech, omron, mitsubishi, autonics, schneider electric, phân phối chính hãng, ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $brandsImage = !empty($settings->og_image) 
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

        $brandItemList = [];
        if (isset($brands) && $brands->isNotEmpty()) {
            foreach ($brands as $idx => $b) {
                $brandItemList[] = [
                    "@type" => "ListItem",
                    "position" => $idx + 1,
                    "url" => route('shop.index', ['brands' => $b->slug]),
                    "name" => $b->name
                ];
            }
        }

        $schemaBrands = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $brandsImage,
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
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $brandsUrl . "#webpage",
                    "url" => $brandsUrl,
                    "name" => "Thương hiệu đối tác phân phối chính hãng",
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $brandsUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $brandsImage
                    ]
                ],
                [
                    "@type" => "BreadcrumbList",
                    "@id" => $brandsUrl . "#breadcrumb",
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
                                "@id" => $brandsUrl,
                                "name" => "Thương hiệu"
                            ]
                        ]
                    ]
                ],
                [
                    "@type" => "ItemList",
                    "@id" => $brandsUrl . "#itemlist",
                    "url" => $brandsUrl,
                    "name" => "Danh sách các thương hiệu tự động hóa",
                    "itemListOrder" => "https://schema.org/ItemListOrderDescending",
                    "numberOfItems" => count($brandItemList),
                    "itemListElement" => $brandItemList
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $brandsUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $brandsUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $brandsUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $brandsKeywords }}">
    <meta name="description" content="{{ $brandsDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $brandsTitle }}">
    <meta property="og:description" content="{{ $brandsDesc }}">
    <meta property="og:url" content="{{ $brandsUrl }}">
    <meta property="og:image" content="{{ $brandsImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $brandsTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $brandsTitle }}">
    <meta name="twitter:description" content="{{ $brandsDesc }}">
    <meta name="twitter:image" content="{{ $brandsImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaBrands, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/brands.css') }}">
@endpush

@section('content')
    <div class="misutech_brands_container misutech_home_container">
        <div class="misutech_brands_header">
            <h1 class="misutech_brands_title">Thương hiệu phân phối chính hãng tại MISUTECH</h1>
            <div class="misutech_brands_description">
                <p>Chào mừng bạn đến với danh mục các thương hiệu uy tín đang được phân phối chính thức tại <strong>MISUTECH</strong>. Chúng tôi tự hào hợp tác với những nhà sản xuất hàng đầu thế giới để mang đến cho khách hàng các giải pháp toàn diện và sản phẩm thiết bị công nghiệp chất lượng cao nhất.</p>
                <p>Tất cả sản phẩm đều được cam kết 100% chính hãng, có nguồn gốc xuất xứ rõ ràng và được bảo hành theo đúng tiêu chuẩn của nhà sản xuất. Dưới đây là danh sách các thương hiệu đối tác tiêu biểu, giúp bạn dễ dàng tra cứu và lựa chọn sản phẩm phù hợp.</p>
            </div>
        </div>
        <div class="misutech_brands_grid">
            @foreach($brands as $brand)
                <a href="{{ route('shop.index', ['brands' => $brand->slug]) }}" class="misutech_brands_item" title="Xem các sản phẩm thương hiệu {{ $brand->name }}">
                    <div class="misutech_brands_image_wrapper">
                        @if($brand->logo)
                            <img class="misutech_brands_image" src="{{ asset('storage/clients/imgs/brands/' . $brand->logo) }}" alt="{{ $brand->name }}" loading="lazy" decoding="async">
                        @else
                            <img class="misutech_brands_image" src="{{ asset('storage/clients/imgs/brands/no-image.png') }}" alt="{{ $brand->name }}" loading="lazy" decoding="async">
                        @endif
                    </div>
                    <div class="misutech_brands_info">
                        <span class="misutech_brands_name">{{ $brand->name }}</span>
                        @if($brand->products_count)
                            <span class="misutech_brands_count">({{ $brand->products_count }} sản phẩm)</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
