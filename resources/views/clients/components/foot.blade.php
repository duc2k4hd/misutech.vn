{{-- Floating Tools Bar --}}
<div class="misutech_home_float_tools">
    {{-- Back to top button --}}
    <button class="misutech_home_float_button" type="button" data-scroll-top aria-label="Về đầu trang"
        title="Về đầu trang">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
            stroke-linecap="round" stroke-linejoin="round">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    {{-- Contact Float Button with Icon & Pulse Animation --}}
    <button class="misutech_home_float_button misutech_float_contact_btn" type="button" id="btnOpenSupportPopup"
        aria-label="Tư vấn & Hotline nhanh" title="Tư vấn bán hàng & Dịch vụ kỹ thuật">
        {{-- Icon Headset / Support SVG --}}
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
            <path
                d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z">
            </path>
        </svg>
        <span class="misutech_float_badge_pulse" title="Sẵn sàng hỗ trợ 24/7"></span>
    </button>
</div>

{{-- Support Contacts Popup Modal --}}
<div class="misutech_support_overlay" id="supportContactOverlay" aria-hidden="true">
    <div class="misutech_support_popup" role="dialog" aria-modal="true" aria-labelledby="supportPopupTitle">
        {{-- Header --}}
        <div class="support_popup_header">
            <div class="support_header_title">
                <h3 id="supportPopupTitle">HỖ TRỢ & TƯ VẤN TRỰC TUYẾN</h3>
                <p>Bấm gọi hoặc chat Zalo trực tiếp với chuyên viên phụ trách</p>
            </div>
            <button type="button" class="support_popup_close" id="btnCloseSupportPopup"
                aria-label="Đóng popup">✕</button>
        </div>

        {{-- Body --}}
        <div class="support_popup_body">
            @php
                $saleContacts = isset($supportContacts)
                    ? $supportContacts->where('department_type', 'sale')
                    : collect();
                $warrantyContacts = isset($supportContacts)
                    ? $supportContacts->whereIn('department_type', ['warranty', 'technical'])
                    : collect();
                $otherContacts = isset($supportContacts)
                    ? $supportContacts->where('department_type', 'other')
                    : collect();
            @endphp

            {{-- 1. Danh sách nhân viên Bán hàng & Báo giá --}}
            @if ($saleContacts->isNotEmpty())
                <div class="support_sale_list">
                    @foreach ($saleContacts as $person)
                        <div class="support_person_card">
                            <div class="support_person_name">
                                {{ $person->name }}
                                @if (!empty($person->note))
                                    <span
                                        style="font-size: 11px; font-weight: normal; color: #64748b; margin-left: 4px;">({{ $person->note }})</span>
                                @endif
                            </div>
                            <div class="support_person_bar">
                                <a href="tel:{{ $person->phone }}" class="support_call_link">
                                    <span>Call/Zalo: <strong>{{ $person->phone }}</strong></span>
                                </a>
                                <a href="https://zalo.me/{{ $person->zalo_phone ?: $person->phone }}" target="_blank"
                                    rel="noopener noreferrer" class="support_zalo_icon_link"
                                    title="Nhắn tin Zalo với {{ $person->name }}">
                                    {{-- Zalo SVG / Icon --}}
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#ffffff">
                                        <path
                                            d="M12 2C6.477 2 2 6.145 2 11.258c0 2.894 1.455 5.485 3.738 7.152L5 22l4.004-1.745c.953.266 1.956.403 2.996.403 5.523 0 10-4.145 10-9.4C22 6.145 17.523 2 12 2z" />
                                    </svg>
                                    <span>Zalo</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- 2. Khối Dịch vụ kỹ thuật / Bảo hành --}}
            @if ($warrantyContacts->isNotEmpty())
                @foreach ($warrantyContacts as $tech)
                    <div class="support_warranty_card">
                        <div class="support_warranty_header">
                            <span>🛠️ {{ $tech->name }}</span>
                            <span style="font-size: 11px; font-weight: normal; opacity: 0.9;">Hỗ trợ 24/7</span>
                        </div>
                        <div class="support_warranty_body">
                            <div class="support_person_bar">
                                <a href="tel:{{ $tech->phone }}" class="support_call_link">
                                    <span>Call/Zalo: <strong>{{ $tech->phone }}</strong></span>
                                </a>
                                <a href="https://zalo.me/{{ $tech->zalo_phone ?: $tech->phone }}" target="_blank"
                                    rel="noopener noreferrer" class="support_zalo_icon_link" title="Nhắn tin Zalo">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#ffffff">
                                        <path
                                            d="M12 2C6.477 2 2 6.145 2 11.258c0 2.894 1.455 5.485 3.738 7.152L5 22l4.004-1.745c.953.266 1.956.403 2.996.403 5.523 0 10-4.145 10-9.4C22 6.145 17.523 2 12 2z" />
                                    </svg>
                                    <span>Zalo</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- 3. Các bộ phận tùy chỉnh khác --}}
            @if ($otherContacts->isNotEmpty())
                @foreach ($otherContacts as $other)
                    <div class="support_person_card support_other_dept_card">
                        <div class="support_person_name">
                            {{ $other->name }} - <span class="support_other_dept_label">{{ $other->department }}</span>
                        </div>
                        <div class="support_person_bar">
                            <a href="tel:{{ $other->phone }}" class="support_call_link">
                                <span>Call/Zalo: <strong>{{ $other->phone }}</strong></span>
                            </a>
                            <a href="https://zalo.me/{{ $other->zalo_phone ?: $other->phone }}" target="_blank"
                                rel="noopener noreferrer" class="support_zalo_icon_link">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="#ffffff">
                                    <path
                                        d="M12 2C6.477 2 2 6.145 2 11.258c0 2.894 1.455 5.485 3.738 7.152L5 22l4.004-1.745c.953.266 1.956.403 2.996.403 5.523 0 10-4.145 10-9.4C22 6.145 17.523 2 12 2z" />
                                </svg>
                                <span>Zalo</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Footer --}}
        <div class="support_popup_footer">
            Hotline Tổng: <a
                href="tel:{{ $settings->phone ?? ($settings->hotline ?? '0866555212') }}">{{ $settings->hotline ?? '0866.555.212' }}</a>
            &nbsp;•&nbsp;
            <a href="{{ route('quote.index') }}">Lập Báo Giá Online ›</a>
        </div>
    </div>
</div>

{{-- Mobile Fixed Bottom Navigation Bar (Chỉ hiển thị trên Mobile) --}}
<nav class="misutech_mobile_bottom_nav" aria-label="Điều hướng nhanh di động">
    {{-- 1. Trang chủ --}}
    <a href="{{ route('home.index') }}" class="misutech_mb_nav_item {{ request()->routeIs('home.index') ? 'active' : '' }}">
        <div class="misutech_mb_nav_icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
            </svg>
        </div>
        <span class="misutech_mb_nav_label">Trang chủ</span>
    </a>

    {{-- 2. Giỏ hàng --}}
    <a href="{{ route('cart.index') }}" class="misutech_mb_nav_item {{ request()->routeIs('cart.*') ? 'active' : '' }}">
        <div class="misutech_mb_nav_icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
            </svg>
            @php $cartCountVal = session('cart_count', 0); @endphp
            <span class="misutech_mb_cart_badge" id="mobileNavCartBadge" {{ $cartCountVal > 0 ? '' : 'style=display:none;' }}>{{ $cartCountVal }}</span>
        </div>
        <span class="misutech_mb_nav_label">Giỏ hàng</span>
    </a>

    {{-- 3. Nút Gọi Hotline / Tư vấn Nổi ở giữa (Center Raised Call Button) --}}
    <div class="misutech_mb_nav_center">
        <button type="button" class="misutech_mb_center_call_btn" id="btnMobileCallPopup" aria-label="Gọi điện & Tư vấn hỗ trợ">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
            </svg>
            <span class="misutech_mb_center_pulse"></span>
        </button>
    </div>

    {{-- 4. Trợ lý AI --}}
    <a href="{{ route('quote.index') }}" class="misutech_mb_nav_item {{ request()->routeIs('quote.*') ? 'active' : '' }}">
        <div class="misutech_mb_nav_icon misutech_mb_ai_icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.38-1 1.72V7h4a3 3 0 0 1 3 3v8a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3v-8a3 3 0 0 1 3-3h4V5.72c-.6-.34-1-.98-1-1.72a2 2 0 0 1 2-2zm-3 9a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm6 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm-5 5a.75.75 0 0 0 0 1.5h4a.75.75 0 0 0 0-1.5H10zM2 13h1v4H2v-4zm20 0h1v4h-1v-4z"/>
            </svg>
        </div>
        <span class="misutech_mb_nav_label">Trợ lý AI</span>
    </a>

    {{-- 5. Zalo --}}
    <a href="https://zalo.me/0988902520" target="_blank" rel="noopener noreferrer" class="misutech_mb_nav_item misutech_mb_zalo_item">
        <div class="misutech_mb_nav_icon misutech_mb_zalo_icon">
            <svg width="22" height="22" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M22.782 0.166016H27.199C33.2653 0.166016 36.8103 1.05701 39.9572 2.74421C43.1041 4.4314 45.5875 6.89585 47.2557 10.0428C48.9429 13.1897 49.8339 16.7347 49.8339 22.801V27.1991C49.8339 33.2654 48.9429 36.8104 47.2557 39.9573C45.5685 43.1042 43.1041 45.5877 39.9572 47.2559C36.8103 48.9431 33.2653 49.8341 27.199 49.8341H22.8009C16.7346 49.8341 13.1896 48.9431 10.0427 47.2559C6.89583 45.5687 4.41243 43.1042 2.7442 39.9573C1.057 36.8104 0.166016 33.2654 0.166016 27.1991V22.801C0.166016 16.7347 1.057 13.1897 2.7442 10.0428C4.43139 6.89585 6.89583 4.41245 10.0427 2.74421C13.1707 1.05701 16.7346 0.166016 22.782 0.166016Z" fill="#0068FF"/>
                <path opacity="0.12" fill-rule="evenodd" clip-rule="evenodd" d="M49.8336 26.4736V27.1994C49.8336 33.2657 48.9427 36.8107 47.2555 39.9576C45.5683 43.1045 43.1038 45.5879 39.9569 47.2562C36.81 48.9434 33.265 49.8344 27.1987 49.8344H22.8007C17.8369 49.8344 14.5612 49.2378 11.8104 48.0966L7.27539 43.4267L49.8336 26.4736Z" fill="#001A33"/>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M7.779 43.5892C10.1019 43.846 13.0061 43.1836 15.0682 42.1825C24.0225 47.1318 38.0197 46.8954 46.4923 41.4732C46.8209 40.9803 47.1279 40.4677 47.4128 39.9363C49.1062 36.7779 50.0004 33.22 50.0004 27.1316V22.7175C50.0004 16.629 49.1062 13.0711 47.4128 9.91273C45.7385 6.75436 43.2461 4.28093 40.0877 2.58758C36.9293 0.894239 33.3714 0 27.283 0H22.8499C17.6644 0 14.2982 0.652754 11.4699 1.89893C11.3153 2.03737 11.1636 2.17818 11.0151 2.32135C2.71734 10.3203 2.08658 27.6593 9.12279 37.0782C9.13064 37.0921 9.13933 37.1061 9.14889 37.1203C10.2334 38.7185 9.18694 41.5154 7.55068 43.1516C7.28431 43.399 7.37944 43.5512 7.779 43.5892Z" fill="white"/>
                <path d="M20.5632 17H10.8382V19.0853H17.5869L10.9329 27.3317C10.7244 27.635 10.5728 27.9194 10.5728 28.5639V29.0947H19.748C20.203 29.0947 20.5822 28.7156 20.5822 28.2606V27.1421H13.4922L19.748 19.2938C19.8428 19.1801 20.0134 18.9716 20.0893 18.8768L20.1272 18.8199C20.4874 18.2891 20.5632 17.8341 20.5632 17.2844V17Z" fill="#0068FF"/>
                <path d="M32.9416 29.0947H34.3255V17H32.2402V28.3933C32.2402 28.7725 32.5435 29.0947 32.9416 29.0947Z" fill="#0068FF"/>
                <path d="M25.814 19.6924C23.1979 19.6924 21.0747 21.8156 21.0747 24.4317C21.0747 27.0478 23.1979 29.171 25.814 29.171C28.4301 29.171 30.5533 27.0478 30.5533 24.4317C30.5723 21.8156 28.4491 19.6924 25.814 19.6924ZM25.814 27.2184C24.2785 27.2184 23.0273 25.9672 23.0273 24.4317C23.0273 22.8962 24.2785 21.645 25.814 21.645C27.3495 21.645 28.6007 22.8962 28.6007 24.4317C28.6007 25.9672 27.3685 27.2184 25.814 27.2184Z" fill="#0068FF"/>
                <path d="M40.4867 19.6162C37.8516 19.6162 35.7095 21.7584 35.7095 24.3934C35.7095 27.0285 37.8516 29.1707 40.4867 29.1707C43.1217 29.1707 45.2639 27.0285 45.2639 24.3934C45.2639 21.7584 43.1217 19.6162 40.4867 19.6162ZM40.4867 27.2181C38.9322 27.2181 37.681 25.9669 37.681 24.4124C37.681 22.8579 38.9322 21.6067 40.4867 21.6067C42.0412 21.6067 43.2924 22.8579 43.2924 24.4124C43.2924 25.9669 42.0412 27.2181 40.4867 27.2181Z" fill="#0068FF"/>
                <path d="M29.4562 29.0944H30.5747V19.957H28.6221V28.2793C28.6221 28.7153 29.0012 29.0944 29.4562 29.0944Z" fill="#0068FF"/>
            </svg>
        </div>
        <span class="misutech_mb_nav_label">Zalo</span>
    </a>
</nav>

{{-- Unified Global Toast Notification (Dùng chung cho toàn bộ website) --}}
<div id="misutech_global_toast" class="misutech_toast misutech_home_toast misutech_product_toast" role="status" aria-live="polite" aria-hidden="true" hidden></div>

{{-- Scripts --}}
@php
    $mainJsVersion = file_exists(public_path('clients/js/main.js')) ? filemtime(public_path('clients/js/main.js')) : '1.0';
@endphp
<script src="{{ asset('clients/js/main.js?v=' . $mainJsVersion) }}" defer></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen = document.getElementById('btnOpenSupportPopup');
        const btnMobileCall = document.getElementById('btnMobileCallPopup');
        const overlay = document.getElementById('supportContactOverlay');
        const btnClose = document.getElementById('btnCloseSupportPopup');

        function openSupportPopup() {
            if (overlay) {
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeSupportPopup() {
            if (overlay) {
                overlay.classList.remove('is-open');
                overlay.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
        }

        if (btnOpen) {
            btnOpen.addEventListener('click', function(e) {
                e.preventDefault();
                openSupportPopup();
            });
        }

        if (btnMobileCall) {
            btnMobileCall.addEventListener('click', function(e) {
                e.preventDefault();
                openSupportPopup();
            });
        }

        if (btnClose) {
            btnClose.addEventListener('click', function(e) {
                e.preventDefault();
                closeSupportPopup();
            });
        }

        if (overlay) {
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    closeSupportPopup();
                }
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay && overlay.classList.contains('is-open')) {
                closeSupportPopup();
            }
        });
    });
</script>
