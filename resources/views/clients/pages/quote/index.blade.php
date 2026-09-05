@extends('clients.layouts.master')

@section('title', 'Báo Giá Thiết Bị Tự Động Hóa & Xuất PDF - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $quotePageTitle = 'Báo Giá Thiết Bị Tự Động Hóa & Xuất PDF - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $quotePageUrl = route('quote.index');
        $quotePageDesc = 'Báo giá thiết bị tự động hóa (Biến tần, PLC, Cảm biến, HMI, Servo) chính hãng trực tuyến. Tra cứu giá, tính chiết khấu dự án và xuất file PDF báo giá chuẩn doanh nghiệp trong 1 phút tại ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')) . '.';
        $quoteKeywords = 'báo giá thiết bị tự động hóa, báo giá biến tần, bảng giá plc mitsubishi, xuất file pdf báo giá, ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $quoteImage = !empty($settings->og_image) 
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

        $schemaQuote = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $quoteImage,
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
                    "@type" => "WebApplication",
                    "@id" => $quotePageUrl . "#webapp",
                    "name" => "Công cụ Báo giá & Xuất PDF MISUTECH",
                    "url" => $quotePageUrl,
                    "applicationCategory" => "BusinessApplication",
                    "operatingSystem" => "All",
                    "browserRequirements" => "Requires JavaScript. Requires HTML5.",
                    "description" => $quotePageDesc,
                    "publisher" => [
                        "@id" => route('home.index') . "#organization"
                    ]
                ],
                [
                    "@type" => "WebPage",
                    "@id" => $quotePageUrl . "#webpage",
                    "url" => $quotePageUrl,
                    "name" => "Báo Giá Thiết Bị Tự Động Hóa & Xuất PDF",
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $quotePageUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $quoteImage
                    ]
                ],
                [
                    "@type" => "BreadcrumbList",
                    "@id" => $quotePageUrl . "#breadcrumb",
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
                                "@id" => $quotePageUrl,
                                "name" => "Báo giá"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $quotePageUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $quotePageUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $quotePageUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $quoteKeywords }}">
    <meta name="description" content="{{ $quotePageDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $quotePageTitle }}">
    <meta property="og:description" content="{{ $quotePageDesc }}">
    <meta property="og:url" content="{{ $quotePageUrl }}">
    <meta property="og:image" content="{{ $quoteImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $quotePageTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $quotePageTitle }}">
    <meta name="twitter:description" content="{{ $quotePageDesc }}">
    <meta name="twitter:image" content="{{ $quoteImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaQuote, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/quote.css') }}?v={{ file_exists(public_path('clients/css/quote.css')) ? filemtime(public_path('clients/css/quote.css')) : time() }}">
@endpush

@section('content')
    <div class="misutech_quote_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_quote_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_quote_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">Báo giá</li>
                </ol>
            </div>
        </nav>

        <div class="misutech_home_container">
            {{-- Header Intro --}}
            <div class="misutech_quote_intro">
                <div class="quote_intro_text">
                    <span class="quote_badge">CÔNG CỤ BÁO GIÁ TỰ ĐỘNG B2B</span>
                    <h1>Bảng Báo Giá Thiết Bị & Xuất File PDF</h1>
                    <p>Tra cứu nhanh hàng triệu mã sản phẩm, điều chỉnh số lượng, chiết khấu và tải về file PDF báo giá chuẩn doanh nghiệp có đầy đủ thông tin pháp lý của MISUTECH.</p>
                </div>
            </div>

            {{-- Main Two-Column Layout --}}
            <div class="misutech_quote_layout">
                {{-- LEFT: Builder Controls & Search --}}
                <div class="misutech_quote_builder">
                    {{-- Card 1: Product Search & Quick Add --}}
                    <div class="quote_card">
                        <h3 class="quote_card_title">
                            <span>🔍 TÌM & THÊM SẢN PHẨM</span>
                            <span style="font-size: 11px; font-weight: normal; color: #64748b;">(Hỗ trợ tìm theo SKU, Mã, Tên)</span>
                        </h3>

                        <div class="quote_search_wrapper">
                            <div class="quote_search_input_wrap">
                                <span class="quote_search_icon">🔎</span>
                                <input type="text" id="productSearchInput" class="q_input quote_search_input" placeholder="Nhập mã sản phẩm, SKU hoặc tên thiết bị (Ví dụ: FR-D720, FX5U, E2E...)" autocomplete="off">
                                <div class="quote_search_spinner" id="searchSpinner"></div>
                            </div>
                            <div class="quote_search_results" id="searchResults"></div>
                        </div>

                        {{-- Quick Custom Item Toggle --}}
                        <button type="button" class="quote_custom_item_toggle" id="toggleCustomItemBtn">＋ Thêm mục thiết bị / Dịch vụ tùy chỉnh ngoài danh mục</button>
                        
                        <div class="quote_custom_item_form" id="customItemForm">
                            <div class="q_form_group">
                                <label>Tên thiết bị / Mã hàng tùy chỉnh <span class="req">*</span></label>
                                <input type="text" id="c_name" class="q_input" placeholder="Ví dụ: Tủ điện điều khiển biến tần 15kW">
                            </div>
                            <div class="q_row_2">
                                <div class="q_form_group">
                                    <label>Hãng / Thương hiệu</label>
                                    <input type="text" id="c_brand" class="q_input" placeholder="Ví dụ: Mitsubishi / MISUTECH" value="MISUTECH">
                                </div>
                                <div class="q_form_group">
                                    <label>Đơn vị tính</label>
                                    <input type="text" id="c_unit" class="q_input" value="Bộ" placeholder="Bộ / Cái / Hệ">
                                </div>
                            </div>
                            <div class="q_row_2">
                                <div class="q_form_group">
                                    <label>Số lượng</label>
                                    <input type="number" id="c_qty" class="q_input" value="1" min="1">
                                </div>
                                <div class="q_form_group">
                                    <label>Đơn giá tham khảo (VNĐ)</label>
                                    <input type="number" id="c_price" class="q_input" placeholder="Nhập số tiền..." min="0">
                                </div>
                            </div>
                            <button type="button" class="btn_quote_pdf" style="width: 100%; justify-content: center; padding: 7px 12px; font-size: 12px;" id="addCustomItemBtn">Thêm vào bảng báo giá</button>
                        </div>
                    </div>

                    {{-- Card 2: Customer Information --}}
                    <div class="quote_card">
                        <h3 class="quote_card_title">
                            <span>👤 THÔNG TIN KHÁCH HÀNG / ĐƠN VỊ</span>
                        </h3>

                        <div class="q_form_group">
                            <label>Họ và tên người nhận <span class="req">*</span></label>
                            <input type="text" id="cust_name" class="q_input" placeholder="Ví dụ: Nguyễn Văn An" value="Quý Khách Hàng">
                        </div>

                        <div class="q_row_2">
                            <div class="q_form_group">
                                <label>Số điện thoại / Zalo <span class="req">*</span></label>
                                <input type="tel" id="cust_phone" class="q_input" placeholder="Ví dụ: 0866555212">
                            </div>
                            <div class="q_form_group">
                                <label>Địa chỉ Email</label>
                                <input type="email" id="cust_email" class="q_input" placeholder="email@company.com">
                            </div>
                        </div>

                        <div class="q_form_group">
                            <label>Tên Công ty / Đơn vị (Nếu có)</label>
                            <input type="text" id="cust_company" class="q_input" placeholder="Ví dụ: Công ty TNHH Cơ Điện Tự Động Hóa...">
                        </div>

                        <div class="q_row_2">
                            <div class="q_form_group">
                                <label>Mã số thuế</label>
                                <input type="text" id="cust_tax" class="q_input" placeholder="Ví dụ: 0201999888">
                            </div>
                            <div class="q_form_group">
                                <label>Địa chỉ / Dự án</label>
                                <input type="text" id="cust_address" class="q_input" placeholder="Ví dụ: KCN Đình Vũ, Hải Phòng">
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Pricing & Commercial Terms --}}
                    <div class="quote_card">
                        <h3 class="quote_card_title">
                            <span>⚙️ CHIẾT KHẤU & ĐIỀU KHOẢN</span>
                        </h3>

                        <div class="q_row_2">
                            <div class="q_form_group">
                                <label>Chiết khấu dự án (%)</label>
                                <input type="number" id="discount_percent" class="q_input" value="0" min="0" max="100" step="0.5">
                            </div>
                            <div class="q_form_group">
                                <label>Thuế VAT (%)</label>
                                <select id="vat_percent" class="q_input">
                                    <option value="10" selected>10% (VAT tiêu chuẩn)</option>
                                    <option value="8">8% (VAT ưu đãi)</option>
                                    <option value="0">0% (Không tính VAT)</option>
                                </select>
                            </div>
                        </div>

                        <div class="q_form_group">
                            <label>Ghi chú thêm cho bản báo giá</label>
                            <textarea id="quote_notes" class="q_input" rows="2" placeholder="Ghi chú thời gian giao hàng, địa điểm giao hoặc yêu cầu xuất hóa đơn..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Interactive Live Quote Sheet Preview --}}
                <div class="misutech_quote_preview_wrap">
                    {{-- Sticky Action Bar --}}
                    <div class="quote_action_bar">
                        <div class="action_bar_left">
                            <strong>Trạng thái:</strong> <span id="itemCountBadge" style="color: #003b70; font-weight: 700;">0 sản phẩm</span> trong báo giá
                        </div>
                        <div class="action_bar_right">
                            <button type="button" class="btn_quote_clear" id="clearQuoteBtn" title="Xóa toàn bộ sản phẩm">Làm mới</button>
                            <button type="button" class="btn_quote_print" id="printQuoteBtn">🖨 In Báo Giá</button>
                            <button type="button" class="btn_quote_pdf" id="downloadPdfBtn">📥 Tải PDF Báo Giá</button>
                        </div>
                    </div>

                    {{-- A4 Paper Template --}}
                    <div class="quote_paper" id="quotePaper">
                        {{-- Company Header --}}
                        <div class="paper_header">
                            <div class="paper_company_info">
                                <h4 class="paper_company_name">{{ $settings->company ?? 'CÔNG TY CỔ PHẦN MISUTECH' }}</h4>
                                <div class="paper_company_detail">
                                    <strong>Địa chỉ:</strong> {{ $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam' }}<br>
                                    <strong>Hotline:</strong> {{ $settings->hotline ?? '0866.555.212' }} &nbsp;|&nbsp; <strong>Email:</strong> {{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}<br>
                                    <strong>Website:</strong> {{ url('/') }} &nbsp;|&nbsp; <strong>MST:</strong> 0202159888
                                </div>
                            </div>
                            <div class="paper_logo_wrap">
                                <img src="{{ $logoUrl }}" alt="MISUTECH Logo" class="paper_logo">
                            </div>
                        </div>

                        {{-- Document Title --}}
                        <div class="paper_title_section">
                            <h2 class="paper_doc_title">BẢNG BÁO GIÁ THIẾT BỊ TỰ ĐỘNG HÓA</h2>
                            <div class="paper_doc_meta">
                                <strong>Số báo giá:</strong> <span id="previewQuoteCode">BG-{{ date('Ymd') }}-PREVIEW</span> &nbsp;•&nbsp; 
                                <strong>Ngày lập:</strong> {{ date('d/m/Y') }} &nbsp;•&nbsp; 
                                <strong>Hiệu lực:</strong> 30 ngày kể từ ngày lập
                            </div>
                        </div>

                        {{-- Customer Info Preview Box --}}
                        <div class="paper_customer_box">
                            <div class="paper_customer_row">
                                <strong>Kính gửi:</strong> <span id="pv_cust_name">Quý Khách Hàng</span>
                            </div>
                            <div class="paper_customer_row">
                                <strong>Số điện thoại:</strong> <span id="pv_cust_phone">---</span>
                            </div>
                            <div class="paper_customer_row">
                                <strong>Đơn vị:</strong> <span id="pv_cust_company">---</span>
                            </div>
                            <div class="paper_customer_row">
                                <strong>Email:</strong> <span id="pv_cust_email">---</span>
                            </div>
                            <div class="paper_customer_row" style="grid-column: 1 / -1;">
                                <strong>Địa chỉ / Dự án:</strong> <span id="pv_cust_address">---</span>
                            </div>
                        </div>

                        {{-- Product Table --}}
                        <div class="paper_table_wrap">
                            <table class="paper_table" id="quoteItemsTable">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 38px;">STT</th>
                                        <th>Tên Hàng Hóa / Mã Thiết Bị</th>
                                        <th style="width: 90px;">Thương Hiệu</th>
                                        <th class="text-center" style="width: 50px;">ĐVT</th>
                                        <th class="text-center" style="width: 75px;">Số Lượng</th>
                                        <th class="text-right" style="width: 110px;">Đơn Giá (VNĐ)</th>
                                        <th class="text-right" style="width: 120px;">Thành Tiền (VNĐ)</th>
                                        <th class="text-center tbl_btn_del" style="width: 35px;">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="quoteTableBody">
                                    {{-- Render động bằng JS --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Summary & Commercial Terms --}}
                        <div class="paper_summary_grid">
                            <div class="paper_terms">
                                <h5>ĐIỀU KHOẢN THƯƠNG MẠI & DỊCH VỤ:</h5>
                                <ul>
                                    <li><strong>Chất lượng:</strong> Cam kết hàng mới 100% chính hãng, đầy đủ CO/CQ.</li>
                                    <li><strong>Bảo hành:</strong> 12 - 24 tháng theo tiêu chuẩn chính hãng nhà sản xuất.</li>
                                    <li><strong>Giao hàng:</strong> Miễn phí giao hàng nội thành Hải Phòng - Hà Nội. Hỗ trợ giao hỏa tốc toàn quốc.</li>
                                    <li><strong>Thanh toán:</strong> Tiền mặt hoặc Chuyển khoản qua tài khoản công ty MISUTECH.</li>
                                </ul>
                                <div id="pv_notes_block" style="margin-top: 8px; display: none;">
                                    <strong>Ghi chú riêng:</strong> <span id="pv_quote_notes"></span>
                                </div>
                            </div>

                            <div class="paper_totals">
                                <div class="totals_row">
                                    <span>Tạm tính (Subtotal):</span>
                                    <strong id="pv_subtotal">0 ₫</strong>
                                </div>
                                <div class="totals_row" id="pv_discount_row" style="display: none; color: #16a34a;">
                                    <span>Chiết khấu (<span id="pv_discount_rate">0</span>%):</span>
                                    <strong id="pv_discount_amount">-0 ₫</strong>
                                </div>
                                <div class="totals_row">
                                    <span>Thuế VAT (<span id="pv_vat_rate">10</span>%):</span>
                                    <strong id="pv_vat_amount">0 ₫</strong>
                                </div>
                                <div class="totals_row grand_total">
                                    <span>TỔNG CỘNG:</span>
                                    <span id="pv_grand_total">0 ₫</span>
                                </div>
                                <div class="totals_amount_words" id="pv_words_total">
                                    (Bằng chữ: Không đồng)
                                </div>
                            </div>
                        </div>

                        {{-- Signatures Section --}}
                        <div class="paper_signatures">
                            <div class="sig_box">
                                <h5>ĐẠI DIỆN KHÁCH HÀNG</h5>
                                <p>(Ký, ghi rõ họ tên và đóng dấu)</p>
                                <div class="sig_name" id="pv_sig_customer">---</div>
                            </div>
                            <div class="sig_box">
                                <h5>ĐẠI DIỆN CÔNG TY MISUTECH</h5>
                                <p>(Ký, đóng dấu xác nhận)</p>
                                <div class="sig_name">PHÒNG KINH DOANH & DỰ ÁN</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- html2pdf CDN for fast client-side PDF export --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        (function () {
            // State Management
            let quoteItems = [];
            let timeOnPageStart = Date.now();
            const STORAGE_KEY = 'misutech_instant_quote_cart';

            // DOM Elements
            const searchInput = document.getElementById('productSearchInput');
            const searchSpinner = document.getElementById('searchSpinner');
            const searchResults = document.getElementById('searchResults');
            const tableBody = document.getElementById('quoteTableBody');
            const itemCountBadge = document.getElementById('itemCountBadge');

            // Customer Input Elements
            const custNameInput = document.getElementById('cust_name');
            const custPhoneInput = document.getElementById('cust_phone');
            const custEmailInput = document.getElementById('cust_email');
            const custCompanyInput = document.getElementById('cust_company');
            const custTaxInput = document.getElementById('cust_tax');
            const custAddressInput = document.getElementById('cust_address');
            const discountInput = document.getElementById('discount_percent');
            const vatSelect = document.getElementById('vat_percent');
            const notesInput = document.getElementById('quote_notes');

            // Preview Elements
            const pvName = document.getElementById('pv_cust_name');
            const pvPhone = document.getElementById('pv_cust_phone');
            const pvEmail = document.getElementById('pv_cust_email');
            const pvCompany = document.getElementById('pv_cust_company');
            const pvAddress = document.getElementById('pv_cust_address');
            const pvSigCustomer = document.getElementById('pv_sig_customer');
            const pvSubtotal = document.getElementById('pv_subtotal');
            const pvDiscountRow = document.getElementById('pv_discount_row');
            const pvDiscountRate = document.getElementById('pv_discount_rate');
            const pvDiscountAmount = document.getElementById('pv_discount_amount');
            const pvVatRate = document.getElementById('pv_vat_rate');
            const pvVatAmount = document.getElementById('pv_vat_amount');
            const pvGrandTotal = document.getElementById('pv_grand_total');
            const pvWordsTotal = document.getElementById('pv_words_total');
            const pvNotesBlock = document.getElementById('pv_notes_block');
            const pvQuoteNotes = document.getElementById('pv_quote_notes');

            // Initial Load from localStorage
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    quoteItems = JSON.parse(saved);
                }
            } catch (e) {
                quoteItems = [];
            }

            // Sync Customer Fields To Preview
            function syncCustomerInfo() {
                const name = custNameInput.value.trim() || 'Quý Khách Hàng';
                const phone = custPhoneInput.value.trim() || '---';
                const email = custEmailInput.value.trim() || '---';
                const company = custCompanyInput.value.trim() || '---';
                const address = custAddressInput.value.trim() || '---';
                const notes = notesInput.value.trim();

                pvName.textContent = name;
                pvPhone.textContent = phone;
                pvEmail.textContent = email;
                pvCompany.textContent = company;
                pvAddress.textContent = address;
                pvSigCustomer.textContent = name;

                if (notes) {
                    pvNotesBlock.style.display = 'block';
                    pvQuoteNotes.textContent = notes;
                } else {
                    pvNotesBlock.style.display = 'none';
                }
            }

            [custNameInput, custPhoneInput, custEmailInput, custCompanyInput, custTaxInput, custAddressInput, notesInput].forEach(el => {
                el.addEventListener('input', syncCustomerInfo);
            });
            discountInput.addEventListener('input', renderTable);
            vatSelect.addEventListener('change', renderTable);

            // Number to Vietnamese Words Converter
            function docTienBangChu(soTien) {
                soTien = Math.round(soTien);
                if (soTien <= 0) return 'Không đồng';

                const ChuSo = [" không ", " một ", " hai ", " ba ", " bốn ", " năm ", " sáu ", " bảy ", " tám ", " chín "];
                const Tien = ["", " nghìn", " triệu", " tỷ", " nghìn tỷ", " triệu tỷ"];

                function docSo3ChuSo(so) {
                    let tram = Math.floor(so / 100);
                    let chuc = Math.floor((so % 100) / 10);
                    let donvi = so % 10;
                    let ketQua = "";

                    if (tram == 0 && chuc == 0 && donvi == 0) return "";

                    if (tram != 0) {
                        ketQua += ChuSo[tram] + " trăm ";
                        if (chuc == 0 && donvi != 0) ketQua += " linh ";
                    }

                    if (chuc != 0 && chuc != 1) {
                        ketQua += ChuSo[chuc] + " mươi";
                        if (chuc == 0 && donvi != 0) ketQua = ketQua + " linh ";
                    }

                    if (chuc == 1) ketQua += " mười ";

                    switch (donvi) {
                        case 1:
                            if (chuc != 0 && chuc != 1) ketQua += " mốt ";
                            else ketQua += ChuSo[donvi];
                            break;
                        case 5:
                            if (chuc == 0) ketQua += ChuSo[donvi];
                            else ketQua += " lăm ";
                            break;
                        default:
                            if (donvi != 0) ketQua += ChuSo[donvi];
                            break;
                    }
                    return ketQua;
                }

                let lan = 0;
                let i = 0;
                let tongTien = soTien;
                let ketQua = "";
                let viTri = [];

                if (tongTien < 0) return "Số tiền âm!";

                viTri[5] = Math.floor(tongTien / 1000000000000000);
                if (isNaN(viTri[5])) viTri[5] = "0";
                tongTien = tongTien - parseFloat(viTri[5].toString()) * 1000000000000000;
                viTri[4] = Math.floor(tongTien / 1000000000000);
                if (isNaN(viTri[4])) viTri[4] = "0";
                tongTien = tongTien - parseFloat(viTri[4].toString()) * 1000000000000;
                viTri[3] = Math.floor(tongTien / 1000000000);
                if (isNaN(viTri[3])) viTri[3] = "0";
                tongTien = tongTien - parseFloat(viTri[3].toString()) * 1000000000;
                viTri[2] = Math.floor(tongTien / 1000000);
                if (isNaN(viTri[2])) viTri[2] = "0";
                tongTien = tongTien - parseFloat(viTri[2].toString()) * 1000000;
                viTri[1] = Math.floor(tongTien / 1000);
                if (isNaN(viTri[1])) viTri[1] = "0";
                tongTien = tongTien - parseFloat(viTri[1].toString()) * 1000;
                viTri[0] = Math.floor(tongTien);
                if (isNaN(viTri[0])) viTri[0] = "0";

                if (viTri[5] > 0) lan = 5;
                else if (viTri[4] > 0) lan = 4;
                else if (viTri[3] > 0) lan = 3;
                else if (viTri[2] > 0) lan = 2;
                else if (viTri[1] > 0) lan = 1;
                else lan = 0;

                for (i = lan; i >= 0; i--) {
                    let tmp = docSo3ChuSo(viTri[i]);
                    ketQua += tmp;
                    if (viTri[i] > 0) ketQua += Tien[i];
                }

                ketQua = ketQua.trim();
                ketQua = ketQua.substring(0, 1).toUpperCase() + ketQua.substring(1);
                return ketQua + " đồng chẵn.";
            }

            // Format Currency
            function formatVND(num) {
                return new Intl.NumberFormat('vi-VN').format(Math.round(num)) + ' ₫';
            }

            // Render Table
            function renderTable() {
                tableBody.innerHTML = '';

                if (quoteItems.length === 0) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 30px; color: #94a3b8;">
                                📦 Chưa có sản phẩm nào trong báo giá.<br>
                                <span style="font-size: 11.5px;">Hãy tìm kiếm mã sản phẩm ở khung bên trái hoặc bấm "Thêm tùy chỉnh" để bắt đầu lập báo giá.</span>
                            </td>
                        </tr>
                    `;
                }

                let subtotal = 0;

                quoteItems.forEach((item, index) => {
                    const itemTotal = item.qty * item.price;
                    subtotal += itemTotal;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td>
                            <strong>${item.name}</strong>
                            ${item.sku ? `<div style="font-size: 11px; color: #64748b;">SKU: ${item.sku}</div>` : ''}
                        </td>
                        <td>${item.brand || 'MISUTECH'}</td>
                        <td class="text-center">${item.unit || 'Cái'}</td>
                        <td class="text-center">
                            <input type="number" class="tbl_qty_input" value="${item.qty}" min="1" max="10000" data-index="${index}">
                        </td>
                        <td class="text-right">
                            <input type="number" class="tbl_price_input" value="${item.price}" min="0" data-index="${index}">
                        </td>
                        <td class="text-right">
                            <strong>${formatVND(itemTotal)}</strong>
                        </td>
                        <td class="text-center tbl_btn_del">
                            <button type="button" class="tbl_btn_del remove_item_btn" data-index="${index}" title="Xóa">✕</button>
                        </td>
                    `;
                    tableBody.appendChild(tr);
                });

                // Attach Table Input Events
                document.querySelectorAll('.tbl_qty_input').forEach(input => {
                    input.addEventListener('change', function () {
                        const idx = parseInt(this.dataset.index);
                        let val = parseInt(this.value) || 1;
                        if (val < 1) val = 1;
                        quoteItems[idx].qty = val;
                        saveAndReRender();
                    });
                });

                document.querySelectorAll('.tbl_price_input').forEach(input => {
                    input.addEventListener('change', function () {
                        const idx = parseInt(this.dataset.index);
                        let val = parseFloat(this.value) || 0;
                        if (val < 0) val = 0;
                        quoteItems[idx].price = val;
                        saveAndReRender();
                    });
                });

                document.querySelectorAll('.remove_item_btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const idx = parseInt(this.dataset.index);
                        quoteItems.splice(idx, 1);
                        saveAndReRender();
                    });
                });

                // Calculate Totals
                const discountRate = parseFloat(discountInput.value) || 0;
                const vatRate = parseFloat(vatSelect.value) || 0;

                const discountAmount = (subtotal * discountRate) / 100;
                const afterDiscount = subtotal - discountAmount;
                const vatAmount = (afterDiscount * vatRate) / 100;
                const grandTotal = afterDiscount + vatAmount;

                pvSubtotal.textContent = formatVND(subtotal);

                if (discountRate > 0) {
                    pvDiscountRow.style.display = 'flex';
                    pvDiscountRate.textContent = discountRate;
                    pvDiscountAmount.textContent = '-' + formatVND(discountAmount);
                } else {
                    pvDiscountRow.style.display = 'none';
                }

                pvVatRate.textContent = vatRate;
                pvVatAmount.textContent = formatVND(vatAmount);
                pvGrandTotal.textContent = formatVND(grandTotal);
                pvWordsTotal.textContent = '(Bằng chữ: ' + docTienBangChu(grandTotal) + ')';

                itemCountBadge.textContent = quoteItems.length + ' sản phẩm';
            }

            function saveAndReRender() {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(quoteItems));
                } catch (e) {}
                renderTable();
            }

            // Live Search Logic (Debounced API)
            let searchTimeout = null;
            searchInput.addEventListener('input', function () {
                const q = this.value.trim();
                clearTimeout(searchTimeout);

                if (q.length < 2) {
                    searchResults.style.display = 'none';
                    searchResults.innerHTML = '';
                    searchSpinner.style.display = 'none';
                    return;
                }

                searchSpinner.style.display = 'block';
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('quote.api.search') }}?q=${encodeURIComponent(q)}`)
                        .then(res => res.json())
                        .then(data => {
                            searchSpinner.style.display = 'none';
                            if (data.success && data.data && data.data.length > 0) {
                                searchResults.innerHTML = '';
                                data.data.forEach(p => {
                                    const div = document.createElement('div');
                                    div.className = 'search_item';
                                    div.innerHTML = `
                                        <img src="${p.image}" class="search_item_img" alt="${p.name}">
                                        <div class="search_item_info">
                                            <div class="search_item_name">${p.name}</div>
                                            <div class="search_item_meta">
                                                <span>Mã: <strong>${p.sku}</strong></span> • 
                                                <span>Hãng: ${p.brand}</span>
                                            </div>
                                        </div>
                                        <div class="search_item_price">${p.price_text}</div>
                                        <button type="button" class="search_item_add_btn">＋ Thêm</button>
                                    `;
                                    div.addEventListener('click', () => {
                                        addItemToQuote({
                                            product_id: p.id,
                                            name: p.name,
                                            sku: p.sku,
                                            brand: p.brand,
                                            unit: 'Cái',
                                            qty: 1,
                                            price: p.price
                                        });
                                        searchResults.style.display = 'none';
                                        searchInput.value = '';
                                    });
                                    searchResults.appendChild(div);
                                });
                                searchResults.style.display = 'block';
                            } else {
                                searchResults.innerHTML = '<div class="search_empty">Không tìm thấy sản phẩm phù hợp. Bạn có thể nhấn "Thêm tùy chỉnh" bên dưới.</div>';
                                searchResults.style.display = 'block';
                            }
                        })
                        .catch(() => {
                            searchSpinner.style.display = 'none';
                        });
                }, 250);
            });

            // Close Search Dropdown when click outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                }
            });

            // Add Item Function
            function addItemToQuote(item) {
                const existing = quoteItems.find(i => (i.product_id && i.product_id === item.product_id) || (i.sku && i.sku === item.sku && i.name === item.name));
                if (existing) {
                    existing.qty += 1;
                } else {
                    quoteItems.push(item);
                }
                saveAndReRender();
            }

            // Custom Item Toggle & Add
            const toggleCustomBtn = document.getElementById('toggleCustomItemBtn');
            const customForm = document.getElementById('customItemForm');
            const addCustomBtn = document.getElementById('addCustomItemBtn');

            toggleCustomBtn.addEventListener('click', function () {
                customForm.style.display = customForm.style.display === 'block' ? 'none' : 'block';
            });

            addCustomBtn.addEventListener('click', function () {
                const name = document.getElementById('c_name').value.trim();
                const brand = document.getElementById('c_brand').value.trim() || 'MISUTECH';
                const unit = document.getElementById('c_unit').value.trim() || 'Cái';
                const qty = parseInt(document.getElementById('c_qty').value) || 1;
                const price = parseFloat(document.getElementById('c_price').value) || 0;

                if (!name) {
                    alert('Vui lòng nhập tên thiết bị hoặc mô tả mục cần báo giá.');
                    return;
                }

                addItemToQuote({
                    product_id: null,
                    name: name,
                    sku: 'Tùy chỉnh',
                    brand: brand,
                    unit: unit,
                    qty: qty,
                    price: price
                });

                document.getElementById('c_name').value = '';
                document.getElementById('c_price').value = '';
                customForm.style.display = 'none';
            });

            // Clear All
            document.getElementById('clearQuoteBtn').addEventListener('click', function () {
                if (confirm('Bạn có chắc muốn xóa toàn bộ sản phẩm trong bảng báo giá?')) {
                    quoteItems = [];
                    saveAndReRender();
                }
            });

            // Print
            document.getElementById('printQuoteBtn').addEventListener('click', function () {
                if (quoteItems.length === 0) {
                    alert('Vui lòng thêm ít nhất 1 sản phẩm vào báo giá trước khi in.');
                    return;
                }
                trackQuoteBackend('printed');
                window.print();
            });

            // Export PDF
            document.getElementById('downloadPdfBtn').addEventListener('click', function () {
                if (quoteItems.length === 0) {
                    alert('Vui lòng thêm ít nhất 1 sản phẩm vào báo giá trước khi xuất PDF.');
                    return;
                }

                const btn = this;
                btn.disabled = true;
                btn.textContent = '⏳ Đang tạo PDF...';

                trackQuoteBackend('generated_pdf', (quoteCode) => {
                    if (quoteCode) {
                        document.getElementById('previewQuoteCode').textContent = quoteCode;
                    }

                    const element = document.getElementById('quotePaper');
                    const opt = {
                        margin:       [8, 8, 8, 8],
                        filename:     `Bao-Gia-MISUTECH-${quoteCode || 'Online'}.pdf`,
                        image:        { type: 'jpeg', quality: 0.98 },
                        html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
                        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    };

                    html2pdf().set(opt).from(element).save().then(() => {
                        btn.disabled = false;
                        btn.textContent = '📥 Tải PDF Báo Giá';
                    }).catch(() => {
                        btn.disabled = false;
                        btn.textContent = '📥 Tải PDF Báo Giá';
                        alert('Đã xuất báo giá! Bạn có thể dùng tính năng In để lưu file PDF nếu cần.');
                    });
                });
            });

            // Send telemetry & lead info to backend
            function trackQuoteBackend(actionType, callback) {
                const duration = Math.round((Date.now() - timeOnPageStart) / 1000);
                const discountRate = parseFloat(discountInput.value) || 0;
                const vatRate = parseFloat(vatSelect.value) || 0;

                let subtotal = 0;
                const itemsPayload = quoteItems.map(i => {
                    const total = i.qty * i.price;
                    subtotal += total;
                    return {
                        product_id: i.product_id || null,
                        product_name: i.name,
                        product_sku: i.sku || 'N/A',
                        brand_name: i.brand || 'MISUTECH',
                        unit: i.unit || 'Cái',
                        quantity: i.qty,
                        unit_price: i.price,
                        total_price: total
                    };
                });

                const discountAmount = (subtotal * discountRate) / 100;
                const afterDiscount = subtotal - discountAmount;
                const vatAmount = (afterDiscount * vatRate) / 100;
                const grandTotal = afterDiscount + vatAmount;

                const payload = {
                    customer_name: custNameInput.value.trim() || 'Khách Hàng Trực Tuyến',
                    customer_phone: custPhoneInput.value.trim() || '0866555212',
                    customer_email: custEmailInput.value.trim() || null,
                    customer_company: custCompanyInput.value.trim() || null,
                    customer_tax_code: custTaxInput.value.trim() || null,
                    customer_address: custAddressInput.value.trim() || null,
                    subtotal: subtotal,
                    discount_percent: discountRate,
                    vat_percent: vatRate,
                    total_amount: grandTotal,
                    duration_seconds: duration,
                    action_type: actionType,
                    notes: notesInput.value.trim() || null,
                    items: itemsPayload,
                    _client_screen: (window.screen.width + 'x' + window.screen.height + ' (' + window.devicePixelRatio + 'dpr)'),
                    _client_language: (navigator.language || 'vi'),
                    _client_timezone: (Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Ho_Chi_Minh'),
                    _client_referer: (document.referrer || ''),
                    _client_time: new Date().toISOString(),
                    _token: '{{ csrf_token() }}'
                };

                fetch('{{ route("quote.track") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (callback && data.quote_code) {
                        callback(data.quote_code);
                    }
                })
                .catch(() => {
                    if (callback) callback(null);
                });
            }

            // Initial Render
            syncCustomerInfo();
            renderTable();
        })();
    </script>
@endpush
