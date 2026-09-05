<div class="misutech_home_topbar">
    <div class="misutech_home_container misutech_home_topbar_inner">
        <div class="misutech_home_topbar_left">
            <a class="misutech_home_topbar_item" href="mailto:{{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}">
                <svg class="misutech_home_topbar_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555zM0 4.697v7.104l5.803-3.558L0 4.697zM6.761 8.83l-6.57 4.027A2 2 0 0 0 2 14h12a2 2 0 0 0 1.808-1.144l-6.57-4.027L8 9.586l-1.239-.757zm3.436-.586L16 11.801V4.697l-5.803 3.546z" />
                </svg>
                Email : {{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}
            </a>
            <a class="misutech_home_topbar_item" href="tel:{{ $settings->phone ?? '0866.555.212' }}">
                <svg class="misutech_home_topbar_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM11.5 1a.5.5 0 0 1 .5.5V2h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-1.5h-.5a.5.5 0 0 1-.5-.5v-.5h-.5a.5.5 0 0 1-.5-.5v-.5h-.5a.5.5 0 0 1-.5-.5v-.5h-.5a.5.5 0 0 1-.5-.5V1.5a.5.5 0 0 1 .5-.5z" />
                </svg>
                Hotline : {{ $settings->hotline ?? '0866.555.212' }}
            </a>
            <a class="misutech_home_topbar_item" href="{{ $settings->deals_url ?? '#' }}">
                <svg class="misutech_home_topbar_icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586l7 7L13.586 9l-7-7H2v4.586z" />
                    <path fill="currentColor"
                        d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z" />
                </svg>
                Ưu đãi hôm nay
            </a>
        </div>
        <div class="misutech_home_topbar_right">
            <a class="misutech_home_topbar_social" href="{{ $settings->facebook ?? ($settings->facebook_url ?? '#') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                </svg>
            </a>
            <a class="misutech_home_topbar_social" href="{{ $settings->instagram ?? ($settings->instagram_url ?? '#') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z" />
                </svg>
            </a>
            <a class="misutech_home_topbar_social" href="{{ $settings->twitter ?? ($settings->twitter_url ?? ($settings->x_url ?? '#')) }}" target="_blank" rel="noopener noreferrer" aria-label="X">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z" />
                </svg>
            </a>
            <a class="misutech_home_topbar_social" href="{{ $settings->tiktok ?? ($settings->tiktok_url ?? '#') }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                    <path fill="currentColor"
                        d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z" />
                </svg>
            </a>
        </div>
    </div>
</div>

<div class="misutech_header_sticky_group" id="misutechHeaderStickyGroup">
<header class="misutech_home_header">
    <div class="misutech_home_container misutech_home_header_inner">
        {{-- Mobile Hamburger Button --}}
        <button class="misutech_mobile_hamburger" id="btnOpenMobileDrawer" type="button" aria-label="Mở menu điều hướng di động">
            <span class="hamburger_line"></span>
            <span class="hamburger_line"></span>
            <span class="hamburger_line"></span>
        </button>

        <a class="misutech_home_logo" href="/" aria-label="Trang chủ MISUTECH">
            <img src="{{ !empty($settings->site_logo) ? asset('storage/clients/imgs/settings/' . $settings->site_logo) : asset('clients/imgs/no-image.png') }}" alt="{{ !empty($settings->name) ? $settings->name : 'Logo MISUTECH' }}">
        </a>
        <form class="misutech_home_search" role="search" action="{{ route('shop.index') }}" method="GET">
            <input class="misutech_home_search_input" name="tim-kiem" type="search" placeholder="Bạn cần tìm sản phẩm gì?"
                aria-label="Tìm kiếm sản phẩm" value="{{ request('tim-kiem') }}" />
            <button class="misutech_home_search_button" type="submit" aria-label="Tìm kiếm">
                ⌕
            </button>
        </form>
        <div class="misutech_home_header_actions">
            <button class="misutech_home_header_action" type="button">
                <span class="misutech_home_header_action_icon">☎</span>
                <span class="misutech_home_header_action_copy">Tư vấn miễn
                    phí<strong>{{ $settings->hotline ?? '0866.555.212' }}</strong></span>
            </button>
            <button class="misutech_home_header_action" type="button">
                <span class="misutech_home_header_action_icon">♙</span>
                <span class="misutech_home_header_action_copy">Tài khoản<strong>Đăng nhập</strong></span>
            </button>
            <a href="{{ route('cart.index') }}" class="misutech_home_header_action">
                <span class="misutech_home_header_action_icon">▣<span
                        class="misutech_home_cart_count">{{ session('cart_count', 0) }}</span></span>
                <span class="misutech_home_header_action_copy">Giỏ hàng<strong>{{ session('cart_count', 0) }} sản phẩm</strong></span>
            </a>
        </div>
    </div>
</header>

<nav class="misutech_home_nav" aria-label="Điều hướng chính">
    <div class="misutech_home_container misutech_home_nav_inner" style="position: relative;">
        <button class="misutech_home_nav_categories" type="button" aria-expanded="false" aria-label="Danh mục sản phẩm">
            <span>☰</span> DANH MỤC SẢN PHẨM
        </button>
        <div class="misutech_home_dropdown_wrapper" hidden>
            <ul class="misutech_home_category_menu">
                @foreach ($mainCategories as $category)
                    <li class="misutech_home_category_item">
                        <a href="/danh-muc/{{ $category->slug }}" class="misutech_home_category_label">
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
                                                        <li><a href="{{ route('categories.show', ['category' => $grandchild->slug]) }}">{{ $grandchild->name }}</a></li>
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
        </div>
        <div class="misutech_home_nav_links">
            <a class="misutech_home_nav_link misutech_home_nav_hot {{ request()->routeIs('brands.*') || request()->is('thuong-hieu*') ? 'active' : '' }}" href="{{ route('brands.index') }}">Thương hiệu</a>
            <a class="misutech_home_nav_link {{ request()->routeIs('documents.*') || request()->is('tai-lieu*') ? 'active' : '' }}" href="{{ route('documents.index') }}">Tài liệu</a>
            <a class="misutech_home_nav_link {{ request()->routeIs('quote.*') || request()->is('bao-gia*') ? 'active' : '' }}" href="{{ route('quote.index') }}">Báo giá</a>
            <a class="misutech_home_nav_link {{ request()->routeIs('blogs.*') || request()->routeIs('posts.*') || request()->is('tin-tuc*') || request()->is('kien-thuc*') || request()->is('tin-cong-nghe*') ? 'active' : '' }}" href="{{ route('blogs.index') }}">Tin công nghệ</a>
            <a class="misutech_home_nav_link {{ request()->routeIs('contact.*') || request()->is('lien-he*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Liên hệ</a>
        </div>
    </div>
</nav>
</div>

{{-- Lớp bóng mờ (Overlay Backdrop) khi mở Menu Danh mục sản phẩm trên Desktop/Tablet --}}
<div class="misutech_menu_backdrop" id="menuBackdrop"></div>

{{-- ═══════════════════════════════════════════════════════════════════════════
   OFF-CANVAS MOBILE DRAWER NAVIGATION (MENU MỞ NGANG MOBILE)
   ═══════════════════════════════════════════════════════════════════════════ --}}
<div class="misutech_drawer_backdrop" id="mobileDrawerBackdrop" aria-hidden="true"></div>

<aside class="misutech_mobile_drawer" id="mobileDrawer" aria-label="Menu điều hướng di động" aria-hidden="true">
    {{-- Drawer Top Header --}}
    <div class="misutech_drawer_header">
        <a href="/" class="misutech_drawer_logo" aria-label="Trang chủ MISUTECH">
            <img src="{{ !empty($settings->site_logo) ? asset('storage/clients/imgs/settings/' . $settings->site_logo) : asset('clients/imgs/no-image.png') }}" alt="{{ $settings->name ?? 'MISUTECH' }}">
        </a>
        <button type="button" class="misutech_drawer_close" id="btnCloseMobileDrawer" aria-label="Đóng menu">✕</button>
    </div>

    {{-- Drawer Search Bar --}}
    <div class="misutech_drawer_search_wrap">
        <form class="misutech_drawer_search" role="search" action="{{ route('shop.index') }}" method="GET">
            <input class="misutech_drawer_search_input" name="tim-kiem" type="search" placeholder="Tìm kiếm sản phẩm..."
                aria-label="Tìm kiếm sản phẩm" value="{{ request('tim-kiem') }}" />
            <button class="misutech_drawer_search_btn" type="submit" aria-label="Tìm kiếm">
                ⌕
            </button>
        </form>
    </div>

    {{-- Drawer Tabs Switcher --}}
    <div class="misutech_drawer_tabs">
        <button type="button" class="misutech_drawer_tab is-active" data-drawer-tab="categories">
            <span>☰</span> DANH MỤC
        </button>
        <button type="button" class="misutech_drawer_tab" data-drawer-tab="menu">
            <span>❖</span> TRANG CHÍNH
        </button>
    </div>

    {{-- Drawer Body: Tab 1 - Danh mục sản phẩm (Tree Accordion) --}}
    <div class="misutech_drawer_panel is-active" id="drawerPanelCategories">
        <ul class="misutech_drawer_tree">
            @foreach ($mainCategories as $category)
                <li class="misutech_drawer_item {{ $category->children->count() > 0 ? 'has-children' : '' }}">
                    <div class="misutech_drawer_item_head">
                        <a href="/danh-muc/{{ $category->slug }}" class="misutech_drawer_link">
                            <span class="misutech_drawer_cat_icon">▣</span>
                            <span>{{ $category->name }}</span>
                        </a>
                        @if ($category->children->count() > 0)
                            <button type="button" class="misutech_drawer_toggle" aria-label="Mở rộng danh mục {{ $category->name }}">
                                <span>›</span>
                            </button>
                        @endif
                    </div>

                    @if ($category->children->count() > 0)
                        <ul class="misutech_drawer_submenu" hidden>
                            @foreach ($category->children as $child)
                                <li class="misutech_drawer_subitem {{ $child->children->count() > 0 ? 'has-grand' : '' }}">
                                    <div class="misutech_drawer_subhead">
                                        <a href="/danh-muc/{{ $child->slug }}" class="misutech_drawer_sublink">
                                            <span>•</span> {{ $child->name }}
                                        </a>
                                        @if ($child->children->count() > 0)
                                            <button type="button" class="misutech_drawer_grandtoggle" aria-label="Mở rộng danh mục {{ $child->name }}">
                                                <span>›</span>
                                            </button>
                                        @endif
                                    </div>

                                    @if ($child->children->count() > 0)
                                        <ul class="misutech_drawer_grandmenu" hidden>
                                            @foreach ($child->children as $grandchild)
                                                <li>
                                                    <a href="/danh-muc/{{ $grandchild->slug }}" class="misutech_drawer_grandlink">
                                                        - {{ $grandchild->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach

            <li class="misutech_drawer_item">
                <div class="misutech_drawer_item_head">
                    <a href="{{ route('shop.index') }}" class="misutech_drawer_link misutech_drawer_link_all">
                        <span class="misutech_drawer_cat_icon">＋</span>
                        <span>Xem tất cả sản phẩm ›</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>

    {{-- Drawer Body: Tab 2 - Menu Trang Chính & Tiện ích --}}
    <div class="misutech_drawer_panel" id="drawerPanelMenu" hidden>
        <ul class="misutech_drawer_navlist">
            <li>
                <a href="{{ route('brands.index') }}" class="misutech_drawer_navitem {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                    <span class="misutech_drawer_navicon">🔥</span>
                    <span>Thương hiệu</span>
                </a>
            </li>
            <li>
                <a href="{{ route('documents.index') }}" class="misutech_drawer_navitem {{ request()->routeIs('documents.*') ? 'active' : '' }}">
                    <span class="misutech_drawer_navicon">📑</span>
                    <span>Tài liệu kỹ thuật</span>
                </a>
            </li>
            <li>
                <a href="{{ route('quote.index') }}" class="misutech_drawer_navitem {{ request()->routeIs('quote.*') ? 'active' : '' }}">
                    <span class="misutech_drawer_navicon">⚡</span>
                    <span>Lập báo giá Online</span>
                </a>
            </li>
            <li>
                <a href="{{ route('blogs.index') }}" class="misutech_drawer_navitem {{ request()->routeIs('blogs.*') ? 'active' : '' }}">
                    <span class="misutech_drawer_navicon">📰</span>
                    <span>Tin công nghệ</span>
                </a>
            </li>
            <li>
                <a href="{{ route('contact.index') }}" class="misutech_drawer_navitem {{ request()->routeIs('contact.*') ? 'active' : '' }}">
                    <span class="misutech_drawer_navicon">✉️</span>
                    <span>Liên hệ & Địa chỉ</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Drawer Bottom Footer: Hotline & Hỗ trợ --}}
    <div class="misutech_drawer_footer">
        <div class="misutech_drawer_hotline_box">
            <div class="misutech_drawer_hotline_label">Hotline & Tư vấn 24/7</div>
            <a href="tel:{{ $settings->phone ?? ($settings->hotline ?? '0866555212') }}" class="misutech_drawer_hotline_btn">
                <span>☎ {{ $settings->hotline ?? '0866.555.212' }}</span>
            </a>
        </div>
        <div class="misutech_drawer_email">
            <span>✉ {{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}</span>
        </div>
    </div>
</aside>

