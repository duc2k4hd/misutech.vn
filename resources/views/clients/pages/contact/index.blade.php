@extends('clients.layouts.master')

@section('title', 'Liên Hệ & Hỗ Trợ Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))

@push('meta')
    @php
        $contactTitle = 'Liên Hệ & Hỗ Trợ Kỹ Thuật - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $contactUrl = route('contact.index');
        $contactDesc = 'Liên hệ với ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')) . ' để nhận tư vấn kỹ thuật, báo giá thiết bị tự động hóa (Biến tần, PLC, Cảm biến, HMI, Servo) chính hãng nhanh nhất. Hotline: ' . ($settings->hotline ?? '0866.555.212') . '.';
        $contactKeywords = 'liên hệ misutech, báo giá thiết bị tự động hóa, tư vấn kỹ thuật plc biến tần, địa chỉ misutech hải phòng, ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech'));
        $contactImage = !empty($settings->og_image) 
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

        $schemaContact = [
            "@context" => "https://schema.org",
            "@graph" => [
                [
                    "@type" => "Organization",
                    "@id" => route('home.index') . "#organization",
                    "name" => $companyName,
                    "image" => $contactImage,
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
                    ]
                ],
                [
                    "@type" => "ContactPage",
                    "@id" => $contactUrl . "#webpage",
                    "url" => $contactUrl,
                    "name" => "Liên hệ & Hỗ trợ kỹ thuật",
                    "inLanguage" => "vi",
                    "isPartOf" => [
                        "@id" => route('home.index') . "#website"
                    ],
                    "about" => [
                        "@id" => route('home.index') . "#organization"
                    ],
                    "breadcrumb" => [
                        "@id" => $contactUrl . "#breadcrumb"
                    ],
                    "primaryImageOfPage" => [
                        "@type" => "ImageObject",
                        "url" => $contactImage
                    ]
                ],
                [
                    "@type" => "LocalBusiness",
                    "@id" => route('home.index') . "#localbusiness",
                    "name" => $companyName,
                    "image" => $contactImage,
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
                    "@id" => $contactUrl . "#breadcrumb",
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
                                "@id" => $contactUrl,
                                "name" => "Liên hệ"
                            ]
                        ]
                    ]
                ]
            ]
        ];
    @endphp

    {{-- Canonical & Language Alternates --}}
    <link rel="canonical" href="{{ $contactUrl }}">
    <link rel="alternate" hreflang="vi" href="{{ $contactUrl }}">
    <link rel="alternate" hreflang="x-default" href="{{ $contactUrl }}">

    {{-- SEO Meta Tags --}}
    <meta name="keywords" content="{{ $contactKeywords }}">
    <meta name="description" content="{{ $contactDesc }}">
    <meta name="robots" content="index, follow, max-snippet:-1, max-video-preview:-1, max-image-preview:large">

    {{-- OpenGraph Tags --}}
    <meta property="og:title" content="{{ $contactTitle }}">
    <meta property="og:description" content="{{ $contactDesc }}">
    <meta property="og:url" content="{{ $contactUrl }}">
    <meta property="og:image" content="{{ $contactImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $contactTitle }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $companyName }}">
    <meta property="og:locale" content="vi_VN">

    {{-- Twitter Card Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $contactTitle }}">
    <meta name="twitter:description" content="{{ $contactDesc }}">
    <meta name="twitter:image" content="{{ $contactImage }}">
    <meta name="twitter:creator" content="{{ $companyName }}">

    {{-- Structured Data Schema JSON-LD --}}
    <script type="application/ld+json">
        {!! json_encode($schemaContact, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/contact.css') }}?v={{ time() }}">
@endpush

@section('content')
    <div class="misutech_contact_page">
        {{-- Breadcrumb --}}
        <nav class="misutech_contact_breadcrumb" aria-label="Breadcrumb">
            <div class="misutech_home_container">
                <ol class="misutech_contact_breadcrumb_list">
                    <li><a href="{{ route('home.index') }}">Trang chủ</a></li>
                    <li class="separator">»</li>
                    <li class="current" aria-current="page">Liên hệ & Hỗ trợ kỹ thuật</li>
                </ol>
            </div>
        </nav>

        <div class="misutech_home_container">
            {{-- Header Card --}}
            <div class="misutech_contact_header_card">
                <span class="contact_header_badge">TRUNG TÂM DỊCH VỤ & HỖ TRỢ KHÁCH HÀNG</span>
                <h1 class="contact_header_title">Liên Hệ Với Đội Ngũ Kỹ Sư MISUTECH</h1>
                <p class="contact_header_desc">
                    Quý khách hàng cần tư vấn kỹ thuật, yêu cầu báo giá dự án hoặc hỗ trợ xử lý sự cố thiết bị tự động hóa? Hãy liên hệ ngay với chúng tôi qua các kênh dưới đây hoặc gửi form yêu cầu trực tuyến.
                </p>
            </div>

            {{-- Main Grid: Info + Form --}}
            <div class="misutech_contact_grid">
                {{-- Left: Company Information --}}
                <div class="misutech_contact_info_box">
                    <h2 class="misutech_contact_info_title">THÔNG TIN LIÊN HỆ TRỰC TIẾP</h2>
                    
                    <div class="misutech_contact_list">
                        <div class="misutech_contact_item">
                            <div class="contact_item_icon">🏢</div>
                            <div class="contact_item_content">
                                <h4>Trụ sở & Văn phòng</h4>
                                <p>{{ $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam' }}</p>
                            </div>
                        </div>

                        <div class="misutech_contact_item">
                            <div class="contact_item_icon">📞</div>
                            <div class="contact_item_content">
                                <h4>Hotline Tư vấn & Báo giá 24/7</h4>
                                <p><a href="tel:{{ preg_replace('/[^0-9]/', '', $settings->hotline ?? '0866555212') }}">{{ $settings->hotline ?? '0866.555.212' }}</a></p>
                            </div>
                        </div>

                        <div class="misutech_contact_item">
                            <div class="contact_item_icon">✉</div>
                            <div class="contact_item_content">
                                <h4>Email Phòng kinh doanh & Kỹ thuật</h4>
                                <p><a href="mailto:{{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}">{{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}</a></p>
                            </div>
                        </div>

                        <div class="misutech_contact_item">
                            <div class="contact_item_icon">💬</div>
                            <div class="contact_item_content">
                                <h4>Kênh hỗ trợ Zalo</h4>
                                <p><a href="{{ $settings->zalo ?? ($settings->zalo_url ?? 'https://zalo.me/0866555212') }}" target="_blank" rel="noopener noreferrer">Nhắn tin Zalo Kỹ sư MISUTECH</a></p>
                            </div>
                        </div>

                        <div class="misutech_contact_item">
                            <div class="contact_item_icon">⏰</div>
                            <div class="contact_item_content">
                                <h4>Thời gian làm việc</h4>
                                <p>Thứ 2 – Thứ 7: 08:00 – 17:30 (Hotline & Zalo hỗ trợ 24/7)</p>
                            </div>
                        </div>
                    </div>

                    {{-- Commitments --}}
                    <div class="misutech_contact_commitments">
                        <h4>CAM KẾT DỊCH VỤ TẠI MISUTECH:</h4>
                        <ul class="commit_list">
                            <li>100% thiết bị chính hãng, đầy đủ CO/CQ từ nhà sản xuất.</li>
                            <li>Báo giá nhanh chóng, chiết khấu dự án cạnh tranh nhất thị trường.</li>
                            <li>Đội ngũ kỹ sư tự động hóa giàu kinh nghiệm sẵn sàng hỗ trợ kỹ thuật và giải pháp tận nơi.</li>
                            <li>Chính sách bảo hành uy tín, hỗ trợ 1 đổi 1 với thiết bị lỗi kỹ thuật.</li>
                        </ul>
                    </div>
                </div>

                {{-- Right: Contact / Quote Form --}}
                <div class="misutech_contact_form_box">
                    <h2 class="misutech_contact_form_title">GỬI YÊU CẦU TƯ VẤN / BÁO GIÁ</h2>
                    <p class="misutech_contact_form_subtitle">Điền thông tin yêu cầu của bạn, kỹ sư MISUTECH sẽ phản hồi trong vòng 15-30 phút.</p>

                    @if(session('success'))
                        <div class="contact_alert contact_alert_success">
                            <span>✔</span>
                            <div>{{ session('success') }}</div>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="contact_alert contact_alert_error">
                            <span>✘</span>
                            <div>
                                @foreach($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                        @csrf
                        {{-- Anti-Spam Time Token --}}
                        <input type="hidden" name="_form_time_token" value="{{ $formToken ?? '' }}">

                        {{-- Anti-Bot Honeypot (Invisible Field) --}}
                        <div style="position: absolute; left: -9999px; top: -9999px; opacity: 0; pointer-events: none;" aria-hidden="true">
                            <label for="company_hp_url">Do not fill this field</label>
                            <input type="text" id="company_hp_url" name="company_hp_url" tabindex="-1" autocomplete="off" value="">
                        </div>

                        <div class="contact_form_row">
                            <div class="contact_form_group">
                                <label for="fullname">Họ và tên <span class="required">*</span></label>
                                <input type="text" id="fullname" name="fullname" class="contact_form_control" placeholder="Ví dụ: Nguyễn Văn An" value="{{ old('fullname') }}" required maxlength="80">
                            </div>

                            <div class="contact_form_group">
                                <label for="phone">Số điện thoại / Zalo <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="contact_form_control" placeholder="Ví dụ: 0866555212" value="{{ old('phone') }}" required pattern="^(0|\+84)(3|5|7|8|9)[0-9]{8}$" title="Vui lòng nhập số điện thoại hợp lệ (10 số, đầu số 03, 05, 07, 08, 09)">
                            </div>
                        </div>

                        <div class="contact_form_row">
                            <div class="contact_form_group">
                                <label for="email">Địa chỉ Email (Tùy chọn)</label>
                                <input type="email" id="email" name="email" class="contact_form_control" placeholder="Ví dụ: example@company.com" value="{{ old('email') }}" maxlength="90">
                            </div>

                            <div class="contact_form_group">
                                <label for="subject">Chủ đề cần hỗ trợ</label>
                                <select id="subject" name="subject" class="contact_form_control">
                                    <option value="Báo giá thiết bị tự động hóa" {{ old('subject') == 'Báo giá thiết bị tự động hóa' ? 'selected' : '' }}>Yêu cầu báo giá thiết bị</option>
                                    <option value="Tư vấn giải pháp kỹ thuật" {{ old('subject') == 'Tư vấn giải pháp kỹ thuật' ? 'selected' : '' }}>Tư vấn giải pháp kỹ thuật / Lập trình</option>
                                    <option value="Hỗ trợ bảo hành / Sửa chữa" {{ old('subject') == 'Hỗ trợ bảo hành / Sửa chữa' ? 'selected' : '' }}>Hỗ trợ bảo hành / Sửa chữa</option>
                                    <option value="Hợp tác cung ứng dự án" {{ old('subject') == 'Hợp tác cung ứng dự án' ? 'selected' : '' }}>Hợp tác cung ứng dự án</option>
                                    <option value="Ý kiến đóng góp khác" {{ old('subject') == 'Ý kiến đóng góp khác' ? 'selected' : '' }}>Khác</option>
                                </select>
                            </div>
                        </div>

                        <div class="contact_form_group">
                            <label for="message">Nội dung chi tiết yêu cầu <span class="required">*</span></label>
                            <textarea id="message" name="message" class="contact_form_control" rows="4" placeholder="Nhập mã sản phẩm, số lượng cần báo giá hoặc mô tả vấn đề kỹ thuật cần hỗ trợ..." required maxlength="1500">{{ old('message') }}</textarea>
                        </div>

                        {{-- Client Telemetry Inputs --}}
                        <input type="hidden" name="_client_screen" id="_c_screen" value="">
                        <input type="hidden" name="_client_language" id="_c_lang" value="">
                        <input type="hidden" name="_client_timezone" id="_c_tz" value="">
                        <input type="hidden" name="_client_referer" id="_c_ref" value="">
                        <input type="hidden" name="_client_time" id="_c_time" value="">

                        <button type="submit" class="contact_submit_btn" id="submitBtn">
                            <span class="btn_text">Gửi Yêu Cầu Cho Kỹ Sư MISUTECH ›</span>
                        </button>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const form = document.getElementById('contactForm');
                            const btn = document.getElementById('submitBtn');

                            // Capture Client Telemetry
                            try {
                                document.getElementById('_c_screen').value = window.screen.width + 'x' + window.screen.height + ' (' + window.devicePixelRatio + 'dpr)';
                                document.getElementById('_c_lang').value = navigator.language || navigator.userLanguage || 'vi';
                                document.getElementById('_c_tz').value = Intl.DateTimeFormat().resolvedOptions().timeZone || 'Asia/Ho_Chi_Minh';
                                document.getElementById('_c_ref').value = document.referrer || '';
                                document.getElementById('_c_time').value = new Date().toISOString();
                            } catch (e) {}

                            if (form && btn) {
                                form.addEventListener('submit', function () {
                                    btn.disabled = true;
                                    btn.style.opacity = '0.7';
                                    btn.style.cursor = 'not-allowed';
                                    btn.innerHTML = '<span>Đang gửi thông tin, vui lòng chờ...</span>';
                                });
                            }
                        });
                    </script>
                </div>
            </div>

            {{-- Google Map Embed --}}
            <div class="misutech_contact_map_wrap">
                <div class="misutech_contact_map_header">
                    <h3>📍 VỊ TRÍ TRỤ SỞ MISUTECH TRÊN BẢN ĐỒ</h3>
                </div>
                <iframe class="misutech_contact_map_iframe" 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3728.847528399581!2d106.6908569!3d20.8388484!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a706509935105%3A0x67dbfa79f64db27!2zMjUyIMSQ4bqhaSBUaOG6r25nLCBU4buVIDQsIETGsMahbmcgS2luaCwgSOG6o2kgUGjDsm5n!5e0!3m2!1svi!2svn!4v1700000000000!5m2!1svi!2svn" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade" 
                    title="Bản đồ chỉ đường đến MISUTECH">
                </iframe>
            </div>

            {{-- FAQ Section --}}
            <div class="misutech_contact_faq">
                <h2 class="misutech_contact_faq_title">CÂU HỎI THƯỜNG GẶP (FAQ)</h2>
                <div class="faq_grid">
                    <div class="faq_item">
                        <h4>1. MISUTECH có cung cấp hàng chính hãng và đầy đủ chứng chỉ CO/CQ không?</h4>
                        <p>Toàn bộ sản phẩm biến tần, PLC, cảm biến, HMI, Servo tại MISUTECH đều là hàng chính hãng 100% từ Omron, Mitsubishi, Autonics, Schneider... đi kèm đầy đủ chứng từ xuất xứ (CO) và chất lượng (CQ) cho dự án.</p>
                    </div>

                    <div class="faq_item">
                        <h4>2. Thời gian giao hàng và hình thức thanh toán như thế nào?</h4>
                        <p>Hàng có sẵn tại kho được giao hỏa tốc trong ngày tại Hải Phòng - Hà Nội và 1-3 ngày toàn quốc. Hỗ trợ thanh toán linh hoạt: Chuyển khoản công ty, COD hoặc bảo lãnh dự án theo hợp đồng.</p>
                    </div>

                    <div class="faq_item">
                        <h4>3. Đội ngũ kỹ sư MISUTECH có hỗ trợ khảo sát và cài đặt tận nơi không?</h4>
                        <p>Có. Với các dự án nâng cấp hệ thống, thay thế biến tần công suất lớn hoặc lắp đặt tủ điện điều khiển, đội ngũ kỹ sư sẵn sàng hỗ trợ khảo sát hiện trường, tư vấn giải pháp và hướng dẫn đấu nối, cài đặt chi tiết.</p>
                    </div>

                    <div class="faq_item">
                        <h4>4. Làm thế nào để nhận báo giá chiết khấu đại lý / nhà thầu?</h4>
                        <p>Quý khách chỉ cần liên hệ Hotline / Zalo <strong>0866.555.212</strong> hoặc gửi danh mục vật tư qua email <strong>kinhdoanhhpt@haiphongtech.vn</strong>, chúng tôi sẽ phản hồi bảng giá chiết khấu tốt nhất trong 15-30 phút.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
