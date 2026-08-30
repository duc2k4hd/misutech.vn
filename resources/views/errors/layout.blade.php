@extends('clients.layouts.master')

@push('meta')
    {{-- Chặn hoàn toàn bot tìm kiếm / crawl trên các trang lỗi --}}
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="bingbot" content="noindex, nofollow, noarchive, nosnippet">
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/errors.css') }}">
@endpush

@section('content')
    <div class="misutech_error_page">
        <div class="error_container">
            <div class="error_card">
                {{-- Error Code & Badge --}}
                <div class="error_header_badge @yield('badge_class', '')">
                    <span>@yield('badge_text', 'THÔNG BÁO HỆ THỐNG')</span>
                </div>

                <div class="error_code_display @yield('code_class', '')">
                    @yield('code', '404')
                </div>

                <h1 class="error_title">@yield('message_title', 'Không tìm thấy trang yêu cầu')</h1>
                <p class="error_desc">@yield('message_desc', 'Trang bạn đang truy cập có thể đã được đổi tên, chuyển sang đường dẫn mới hoặc tạm thời không khả dụng.')</p>

                {{-- Smart Search Bar to Guide Users --}}
                <div class="error_search_box">
                    <form action="{{ route('shop.index') }}" method="GET" class="error_search_form">
                        <input type="text" name="keyword" class="error_search_input" placeholder="Tìm nhanh thiết bị (Biến tần, PLC, Cảm biến, HMI, Servo...)" required>
                        <button type="submit" class="error_search_btn">
                            <span>🔍 Tìm kiếm</span>
                        </button>
                    </form>
                </div>

                {{-- Key Action Buttons --}}
                <div class="error_actions">
                    <a href="{{ route('home.index') }}" class="btn_error_primary">
                        <span>🏠 Về Trang Chủ</span>
                    </a>
                    <a href="{{ route('shop.index') }}" class="btn_error_secondary">
                        <span>🛒 Danh Mục Sản Phẩm</span>
                    </a>
                    <a href="{{ route('quote.index') }}" class="btn_error_quote">
                        <span>📑 Lập Báo Giá Nhanh</span>
                    </a>
                    <a href="{{ route('documents.index') }}" class="btn_error_secondary">
                        <span>📚 Tài Liệu Kỹ Thuật</span>
                    </a>
                    <a href="{{ route('contact.index') }}" class="btn_error_secondary">
                        <span>📞 Liên Hệ Hỗ Trợ</span>
                    </a>
                </div>

                {{-- Helpful Navigation Shortcuts --}}
                <div class="error_suggestions">
                    <h4 class="suggestions_header">
                        <span>⚡ DANH MỤC THIẾT BỊ TỰ ĐỘNG HÓA PHỔ BIẾN:</span>
                    </h4>
                    <div class="suggestions_grid">
                        <a href="{{ route('shop.index') }}?category=bien-tan" class="suggestion_item">
                            <span class="suggestion_item_title">Biến Tần Inverter</span>
                            <span class="suggestion_item_desc">Mitsubishi, Fuji, Schneider, LS</span>
                        </a>
                        <a href="{{ route('shop.index') }}?category=plc-lap-trinh" class="suggestion_item">
                            <span class="suggestion_item_title">Bộ Điều Khiển PLC</span>
                            <span class="suggestion_item_desc">FX5U, FX3U, Q-Series, S7-1200</span>
                        </a>
                        <a href="{{ route('shop.index') }}?category=man-hinh-hmi" class="suggestion_item">
                            <span class="suggestion_item_title">Màn Hình HMI Cảm Ứng</span>
                            <span class="suggestion_item_desc">GOT2000, Proface, Kinco, Delta</span>
                        </a>
                        <a href="{{ route('shop.index') }}?category=cam-bien-cong-nghiep" class="suggestion_item">
                            <span class="suggestion_item_title">Cảm Biến Công Nghiệp</span>
                            <span class="suggestion_item_desc">Quang, Tiệm cận, Áp suất, Encoder</span>
                        </a>
                    </div>
                </div>

                {{-- Urgent Technical Support Bar --}}
                <div class="error_support_bar">
                    <div class="support_bar_text">
                        <strong>Cần hỗ trợ kỹ thuật hoặc báo giá khẩn cấp?</strong><br>
                        Đội ngũ kỹ sư tự động hóa MISUTECH luôn trực hỗ trợ 24/7.
                    </div>
                    <div class="support_bar_actions">
                        <a href="tel:{{ $settings->hotline ?? ($settings->phone ?? '0866555212') }}" class="support_btn_call">
                            <span>📞 Gọi Hotline</span>
                        </a>
                        <a href="{{ $settings->zalo ?? ($settings->zalo_url ?? 'https://zalo.me/0866555212') }}" target="_blank" rel="noopener noreferrer" class="support_btn_zalo">
                            <span>💬 Chat Zalo Kỹ Sư</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
