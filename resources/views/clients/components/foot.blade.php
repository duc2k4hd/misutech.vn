{{-- Floating Tools Bar --}}
<div class="misutech_home_float_tools" aria-label="Công cụ nhanh">
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
                            <div class="support_person_bar" style="background: #ffffff; border: 1px solid #bae6fd;">
                                <a href="tel:{{ $tech->phone }}" class="support_call_link" style="color: #0369a1;">
                                    <span>Call/Zalo: <strong>{{ $tech->phone }}</strong></span>
                                </a>
                                <a href="https://zalo.me/{{ $tech->zalo_phone ?: $tech->phone }}" target="_blank"
                                    rel="noopener noreferrer" class="support_zalo_icon_link"
                                    style="background: #0284c7;" title="Nhắn tin Zalo">
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
                    <div class="support_person_card" style="border-left: 3px solid #003b70;">
                        <div class="support_person_name">
                            {{ $other->name }} - <span
                                style="font-size: 12px; color: #003b70;">{{ $other->department }}</span>
                        </div>
                        <div class="support_person_bar">
                            <a href="tel:{{ $other->phone }}" class="support_call_link">
                                <span>Call/Zalo: <strong>{{ $other->phone }}</strong></span>
                            </a>
                            <a href="https://zalo.me/{{ $other->zalo_phone ?: $other->phone }}" target="_blank"
                                rel="noopener noreferrer" class="support_zalo_icon_link">
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

<div class="misutech_home_toast" role="status" aria-live="polite" aria-hidden="true"></div>

{{-- Scripts --}}
<script src="{{ asset('clients/js/main.js') }}?v={{ time() }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen = document.getElementById('btnOpenSupportPopup');
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
