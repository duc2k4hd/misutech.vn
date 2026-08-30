@extends('clients.layouts.master')

@php
    $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
    $rawPostTitle = $post->meta_title ?: $post->title;
    $cleanPostTitle = preg_replace('/(\s*[\-\|]\s*(MISUTECH|Công ty cổ phần Misutech)).*$/iu', '', $rawPostTitle);
    $finalPostTitle = $cleanPostTitle . ' - ' . $companyName;
@endphp

@section('title', $finalPostTitle)

@push('meta')
    @php
        $pageTitle = $finalPostTitle;
        $pageDesc = $post->meta_description ?: ($post->summary ?: Str::limit(strip_tags($post->content), 160));
        $postKeywords = $post->meta_keywords ?: ($post->title . ', ' . ($post->category->name ?? 'Tự động hóa') . ', cẩm nang kỹ thuật, ' . ($settings->name ?? 'MISUTECH'));
        $postUrl = route('blogs.show', $post->slug);
        $thumb = $post->thumbnailMedia->first();
        $thumbUrl = $thumb 
            ? (Str::startsWith($thumb->url, ['http://', 'https://']) ? $thumb->url : asset('storage/clients/imgs/posts/' . $thumb->url)) 
            : (!empty($settings->og_image) 
                ? (Str::startsWith($settings->og_image, ['http://', 'https://']) ? $settings->og_image : asset('storage/clients/imgs/settings/' . $settings->og_image)) 
                : asset('storage/clients/imgs/settings/banner-seo-misutech.jpg'));
        $logoUrl = !empty($settings->site_logo) 
            ? (Str::startsWith($settings->site_logo, ['http://', 'https://']) ? $settings->site_logo : asset('storage/clients/imgs/settings/' . $settings->site_logo)) 
            : asset('storage/clients/imgs/settings/logo-misutech.png');
        $companyName = $settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech');
        $authorName = $post->author->name ?? 'Kỹ sư MISUTECH';
        $publishedTime = $post->published_at ? $post->published_at->toIso8601String() : optional($post->created_at)->toIso8601String();
        $modifiedTime = optional($post->updated_at)->toIso8601String() ?? $publishedTime;

        $breadcrumbs = [
            [
                "@type" => "ListItem",
                "position" => 1,
                "name" => "Trang chủ",
                "item" => route('home.index')
            ],
            [
                "@type" => "ListItem",
                "position" => 2,
                "name" => "Blog",
                "item" => route('blogs.index')
            ]
        ];

        if ($post->category) {
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $post->category->name,
                "item" => route('blogs.index', ['category' => $post->category->slug])
            ];
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => 4,
                "name" => $post->title,
                "item" => $postUrl
            ];
        } else {
            $breadcrumbs[] = [
                "@type" => "ListItem",
                "position" => 3,
                "name" => $post->title,
                "item" => $postUrl
            ];
        }
    @endphp

    {{-- SEO Meta Tags --}}
    <meta name="description" content="{{ $pageDesc }}">
    <meta name="keywords" content="{{ $postKeywords }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $postUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $postUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $postUrl }}">

    {{-- OpenGraph Tags --}}
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDesc }}">
    <meta property="og:url" content="{{ $postUrl }}">
    <meta property="og:image" content="{{ $thumbUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $post->title }}">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">
    <meta property="article:published_time" content="{{ $publishedTime }}">
    <meta property="article:modified_time" content="{{ $modifiedTime }}">
    @if($post->category)
        <meta property="article:section" content="{{ $post->category->name }}">
    @endif
    <meta property="article:author" content="{{ $authorName }}">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDesc }}">
    <meta name="twitter:image" content="{{ $thumbUrl }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Preload Featured Image --}}
    <link rel="preload" as="image" href="{{ $thumbUrl }}">

    @php
        $schemaBlogDetail = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "url" => route('home.index'),
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $logoUrl
                    ]
                ],
                [
                    "@type" => "WebSite",
                    "@id" => route('home.index') . "#website",
                    "name" => $settings->name ?? "MISUTECH",
                    "url" => route('home.index')
                ],
                [
                    "@type" => "BreadcrumbList",
                    "@id" => $postUrl . "#breadcrumb",
                    "itemListElement" => $breadcrumbs
                ],
                [
                    "@type" => [
                        "Article",
                        "BlogPosting"
                    ],
                    "@id" => $postUrl,
                    "headline" => $post->title,
                    "description" => $pageDesc,
                    "url" => $postUrl,
                    "datePublished" => $publishedTime,
                    "dateModified" => $modifiedTime,
                    "author" => [
                        "@type" => "Person",
                        "name" => $authorName,
                        "url" => route('home.index')
                    ],
                    "publisher" => [
                        "@type" => "Organization",
                        "name" => $companyName,
                        "url" => route('home.index'),
                        "logo" => [
                            "@type" => "ImageObject",
                            "url" => $logoUrl
                        ]
                    ],
                    "mainEntityOfPage" => [
                        "@type" => "WebPage",
                        "@id" => $postUrl
                    ],
                    "articleSection" => $post->category ? $post->category->name : 'Tin tức công nghệ',
                    "inLanguage" => "vi-VN",
                    "image" => [
                        "@type" => "ImageObject",
                        "url" => $thumbUrl,
                        "width" => 1200,
                        "height" => 630
                    ]
                ]
            ]
        ];
    @endphp

    {{-- Schema JSON-LD Structured Data --}}
    <script type="application/ld+json">
        {!! json_encode($schemaBlogDetail, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/blogs.css') }}">
@endpush

@section('content')
    <div class="misutech_blog_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_blog_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_blog_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    <li><a href="{{ route('blogs.index') }}">Tin tức & Cẩm nang</a></li>
                    @if($post->category)
                        <li class="separator">»</li>
                        <li><a href="{{ route('blogs.index', ['category' => $post->category->slug]) }}">{{ $post->category->name }}</a></li>
                    @endif
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">{{ Str::limit($post->title, 40) }}</li>
                </ol>
            </div>
        </nav>

        {{-- Main Container --}}
        <div class="misutech_home_container misutech_blog_layout">
            {{-- Left Content Column --}}
            <main class="misutech_blog_main">
                <article class="misutech_blog_detail_card">
                    {{-- Header Bài viết --}}
                    <header class="blog_detail_header">
                        @if($post->category)
                            <a href="{{ route('blogs.index', ['category' => $post->category->slug]) }}" class="blog_detail_tag">
                                {{ $post->category->name }}
                            </a>
                        @endif
                        <h1 class="blog_detail_title">{{ $post->title }}</h1>

                        <div class="blog_detail_meta">
                            <span class="meta_item author">
                                <svg class="meta_icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                {{ $post->author ? $post->author->name : 'Kỹ sư MISUTECH' }}
                            </span>
                            <span class="meta_dot">•</span>
                            <span class="meta_item date">
                                <svg class="meta_icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
                            </span>
                            <span class="meta_dot">•</span>
                            <span class="meta_item views">
                                <svg class="meta_icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                {{ $post->views_count }} lượt xem
                            </span>
                        </div>
                    </header>

                    {{-- Ảnh đại diện chính của bài viết --}}
                    @if($thumb)
                        <div class="blog_detail_featured_image">
                            <img src="{{ $thumbUrl }}" alt="{{ $post->title }}" loading="lazy">
                        </div>
                    @endif

                    {{-- Tóm tắt bài viết --}}
                    @if($post->summary)
                        <div class="blog_detail_summary">
                            <strong>Tóm tắt:</strong> {{ $post->summary }}
                        </div>
                    @endif

                    {{-- Mục lục bài viết (Table of Contents - Tự động sinh từ Heading) --}}
                    <nav class="misutech_toc_box" id="misutechToc" aria-label="Mục lục bài viết" style="display: none;">
                        <div class="toc_header" onclick="toggleToc()">
                            <span class="toc_title">Mục lục nội dung</span>
                            <button type="button" class="toc_toggle_btn" id="tocToggleBtn" aria-label="Ẩn hiện mục lục">
                                <span class="toggle_txt">[Ẩn]</span>
                            </button>
                        </div>
                        <div class="toc_body" id="tocBody">
                            <ul class="toc_list" id="tocList">
                                {{-- JS tự động sinh danh mục Heading --}}
                            </ul>
                        </div>
                    </nav>

                    {{-- Nội dung bài viết --}}
                    <div class="misutech_blog_content" id="blogContent">
                        {!! $post->content !!}
                    </div>

                    {{-- Social Share & Action Bar --}}
                    <div class="blog_detail_share_bar">
                        <span class="share_lbl">Chia sẻ bài viết:</span>
                        <div class="share_buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn_share fb" title="Chia sẻ lên Facebook">
                                Facebook
                            </a>
                            <a href="https://zalo.me/share?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" class="btn_share zalo" title="Chia sẻ lên Zalo">
                                Zalo
                            </a>
                            <button type="button" class="btn_share copy" onclick="copyPostLink()" title="Sao chép liên kết">
                                🔗 Sao chép liên kết
                            </button>
                        </div>
                    </div>
                </article>

                {{-- Related Posts --}}
                @if($relatedPosts->isNotEmpty())
                    <section class="misutech_related_posts_section">
                        <h3 class="related_section_title">Bài viết liên quan cùng chuyên mục</h3>
                        <div class="related_posts_grid">
                            @foreach($relatedPosts as $rel)
                                @php
                                    $relThumb = $rel->thumbnailMedia->first();
                                    $relThumbUrl = $relThumb ? $relThumb->url : asset('clients/imgs/default-blog.jpg');
                                @endphp
                                <article class="related_post_card">
                                    <a href="{{ route('blogs.show', $rel->slug) }}" class="rel_thumb">
                                        <img src="{{ $relThumbUrl }}" alt="{{ $rel->title }}" loading="lazy" decoding="async">
                                    </a>
                                    <div class="rel_info">
                                        <span class="rel_date">{{ $rel->published_at ? $rel->published_at->format('d/m/Y') : '' }}</span>
                                        <h4 class="rel_title">
                                            <a href="{{ route('blogs.show', $rel->slug) }}" title="{{ $rel->title }}">
                                                {{ $rel->title }}
                                            </a>
                                        </h4>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>

            {{-- Right Sidebar --}}
            <aside class="misutech_blog_sidebar">
                {{-- Search Box --}}
                <div class="sidebar_widget">
                    <h3 class="widget_title">Tìm kiếm bài viết</h3>
                    <form action="{{ route('blogs.index') }}" method="GET" class="sidebar_search_form">
                        <input type="text" name="q" placeholder="Nhập từ khóa tìm kiếm...">
                        <button type="submit" aria-label="Tìm kiếm">🔍</button>
                    </form>
                </div>

                {{-- Categories Widget --}}
                <div class="sidebar_widget">
                    <h3 class="widget_title">Chuyên mục bài viết</h3>
                    <ul class="sidebar_cat_list">
                        <li>
                            <a href="{{ route('blogs.index') }}">
                                <span>Tất cả bài viết</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('blogs.index', ['category' => $cat->slug]) }}" class="{{ ($post->category && $post->category->id === $cat->id) ? 'active' : '' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="cat_count">{{ $cat->posts_count }}</span>
                                </a>
                                @if($cat->children && $cat->children->count() > 0)
                                    <ul class="sidebar_subcat_list">
                                        @foreach($cat->children as $sub)
                                            <li>
                                                <a href="{{ route('blogs.index', ['category' => $sub->slug]) }}" class="{{ ($post->category && $post->category->id === $sub->id) ? 'active' : '' }}">
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

                {{-- Cụm Sticky Sidebar: Tự động chèn TOC lên trên Bài viết mới nhất khi cuộn qua TOC bài viết --}}
                <div class="misutech_sidebar_sticky_group" id="sidebarStickyGroup">
                    {{-- Sidebar Sticky TOC (Chỉ hiện khi cuộn qua TOC trong nội dung) --}}
                    <div class="sidebar_widget sidebar_toc_widget" id="sidebarTocWidget" style="display: none;">
                        <h3 class="widget_title">Mục lục nội dung</h3>
                        <div class="sidebar_toc_body">
                            <ul class="sidebar_toc_list" id="sidebarTocList">
                                {{-- JS tự động điền danh sách Heading --}}
                            </ul>
                        </div>
                    </div>

                    {{-- Recent Posts Widget --}}
                    @if($recentPosts->isNotEmpty())
                        <div class="sidebar_widget">
                            <h3 class="widget_title">Bài viết mới nhất</h3>
                            <div class="sidebar_recent_list">
                                @foreach($recentPosts->take(3) as $rec)
                                    @php
                                        $rThumb = $rec->thumbnailMedia->first();
                                        $rThumbUrl = $rThumb ? $rThumb->url : asset('clients/imgs/default-blog.jpg');
                                    @endphp
                                    <article class="sidebar_recent_item">
                                        <a href="{{ route('blogs.show', $rec->slug) }}" class="recent_thumb">
                                            <img src="{{ $rThumbUrl }}" alt="{{ $rec->title }}" loading="lazy" decoding="async">
                                        </a>
                                        <div class="recent_info">
                                            <h4 class="recent_title">
                                                <a href="{{ route('blogs.show', $rec->slug) }}">
                                                    {{ $rec->title }}
                                                </a>
                                            </h4>
                                            <span class="recent_date">{{ $rec->published_at ? $rec->published_at->format('d/m/Y') : '' }}</span>
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
                </div>
            </aside>
        </div>
    </div>

    {{-- Floating Quick TOC Button for Mobile --}}
    <button type="button" class="misutech_mobile_toc_trigger" id="mobileTocTrigger" onclick="openMobileToc()" aria-label="Mở mục lục bài viết" style="display: none;">
        <span class="m_txt">Mục lục nội dung</span>
    </button>

    {{-- Mobile TOC Drawer (Offcanvas Slide-in) --}}
    <div class="misutech_mobile_toc_overlay" id="mobileTocOverlay" onclick="closeMobileToc()"></div>
    <aside class="misutech_mobile_toc_drawer" id="mobileTocDrawer" aria-label="Mục lục bài viết">
        <div class="mobile_toc_header">
            <div class="mobile_toc_title">Mục lục nội dung</div>
            <button type="button" class="mobile_toc_close" onclick="closeMobileToc()" aria-label="Đóng mục lục">✕</button>
        </div>
        <div class="mobile_toc_body">
            <ul class="mobile_toc_list" id="mobileTocList">
                {{-- JS tự động điền --}}
            </ul>
        </div>
    </aside>

    {{-- Script TOC & Copy Link --}}
    <script>
        function copyPostLink() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                alert('Đã sao chép liên kết bài viết vào bộ nhớ tạm!');
            }, function() {
                prompt('Sao chép liên kết dưới đây:', window.location.href);
            });
        }

        // ================= TỰ ĐỘNG TẠO MỤC LỤC (TOC) =================
        let isTocOpen = true;

        function toggleToc() {
            isTocOpen = !isTocOpen;
            const body = document.getElementById('tocBody');
            const toggleTxt = document.querySelector('#tocToggleBtn .toggle_txt');
            
            if (isTocOpen) {
                body.style.display = 'block';
                toggleTxt.textContent = '[Ẩn]';
            } else {
                body.style.display = 'none';
                toggleTxt.textContent = '[Hiện]';
            }
        }

        function openMobileToc() {
            document.getElementById('mobileTocDrawer').classList.add('active');
            document.getElementById('mobileTocOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileToc() {
            document.getElementById('mobileTocDrawer').classList.remove('active');
            document.getElementById('mobileTocOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('blogContent');
            if (!content) return;

            const headings = content.querySelectorAll('h2, h3, h4');
            if (headings.length < 1) {
                // Nếu không có heading nào thì không cần hiển thị TOC
                return;
            }

            const tocBox = document.getElementById('misutechToc');
            const tocList = document.getElementById('tocList');
            const sidebarTocWidget = document.getElementById('sidebarTocWidget');
            const sidebarTocList = document.getElementById('sidebarTocList');
            const mobileTocList = document.getElementById('mobileTocList');
            const mobileTrigger = document.getElementById('mobileTocTrigger');

            tocBox.style.display = 'block';

            let h2Index = 0;
            let h3Index = 0;
            let h4Index = 0;

            headings.forEach((heading, idx) => {
                const tag = heading.tagName.toLowerCase();
                const text = heading.textContent.trim();
                
                // Tự động loại bỏ số thứ tự đã gõ sẵn ở đầu tiêu đề (nếu có, VD: "1. ", "1.1. ", "1) ", "1 - ") để tránh lặp số
                const cleanText = text.replace(/^(\d+(\.\d+)*(\.|\)|\-|\:)?\s*|[IVXLCDM]+\.\s*|[A-Z]\.\s*)/i, '').trim();

                // Gán ID nếu chưa có
                let id = heading.id;
                if (!id) {
                    id = 'toc-heading-' + (idx + 1);
                    heading.id = id;
                }

                // Đánh số thứ tự phân cấp
                let prefixNumber = '';
                if (tag === 'h2') {
                    h2Index++;
                    h3Index = 0;
                    h4Index = 0;
                    prefixNumber = `${h2Index}. `;
                } else if (tag === 'h3') {
                    h3Index++;
                    h4Index = 0;
                    prefixNumber = `${h2Index}.${h3Index}. `;
                } else if (tag === 'h4') {
                    h4Index++;
                    prefixNumber = `${h2Index}.${h3Index}.${h4Index}. `;
                }

                // 1. Tạo item cho Desktop TOC trong bài viết
                const li = document.createElement('li');
                li.className = `toc_item toc_level_${tag}`;
                li.innerHTML = `<a href="#${id}" onclick="smoothScrollToHeading(event, '${id}')"><span class="toc_num">${prefixNumber}</span><span class="toc_text">${cleanText}</span></a>`;
                tocList.appendChild(li);

                // 2. Tạo item cho Sidebar Sticky TOC
                if (sidebarTocList) {
                    const sLi = document.createElement('li');
                    sLi.className = `sidebar_toc_item sidebar_toc_level_${tag}`;
                    sLi.innerHTML = `<a href="#${id}" onclick="smoothScrollToHeading(event, '${id}')"><span class="toc_num">${prefixNumber}</span><span class="toc_text">${cleanText}</span></a>`;
                    sidebarTocList.appendChild(sLi);
                }

                // 3. Tạo item cho Mobile Drawer TOC
                const mLi = document.createElement('li');
                mLi.className = `mobile_toc_item mobile_toc_level_${tag}`;
                mLi.innerHTML = `<a href="#${id}" onclick="smoothScrollToHeading(event, '${id}', true)"><span class="toc_num">${prefixNumber}</span><span class="toc_text">${cleanText}</span></a>`;
                mobileTocList.appendChild(mLi);
            });

            // Lắng nghe cuộn trang để hiện Sidebar Sticky TOC trên desktop và Mobile Trigger
            window.addEventListener('scroll', function() {
                const scrollPos = window.scrollY || window.pageYOffset;
                const tocRect = tocBox.getBoundingClientRect();
                
                // Desktop: Khi cuộn qua TOC trong nội dung thì tự động hiện TOC trên Sidebar (phía trên Bài viết mới nhất)
                if (window.innerWidth > 992 && sidebarTocWidget) {
                    if (tocRect.bottom < 85) {
                        sidebarTocWidget.style.display = 'block';
                    } else {
                        sidebarTocWidget.style.display = 'none';
                    }
                }

                // Mobile: Hiện nút Mobile Trigger khi đã cuộn qua TOC trên màn hình nhỏ
                if (window.innerWidth <= 992 && mobileTrigger) {
                    if (tocRect.bottom < 0) {
                        mobileTrigger.style.display = 'flex';
                    } else {
                        mobileTrigger.style.display = 'none';
                    }
                }

                // Scrollspy Highlight Active Heading
                let currentActiveId = '';
                headings.forEach(heading => {
                    const top = heading.getBoundingClientRect().top;
                    if (top <= 135) {
                        currentActiveId = heading.id;
                    }
                });

                if (currentActiveId) {
                    document.querySelectorAll('.toc_item a, .sidebar_toc_item a, .mobile_toc_item a').forEach(a => {
                        if (a.getAttribute('href') === '#' + currentActiveId) {
                            a.classList.add('active');
                        } else {
                            a.classList.remove('active');
                        }
                    });
                }
            });
        });

        function smoothScrollToHeading(e, id, isMobile = false) {
            e.preventDefault();
            const target = document.getElementById(id);
            if (!target) return;

            if (isMobile) {
                closeMobileToc();
            }

            const headerOffset = 115;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    </script>
@endsection
