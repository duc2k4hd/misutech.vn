@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           SYSTEM SETTINGS - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Header Card */
        .settings-header-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }
        .settings-header-info h4 {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .settings-header-info p {
            font-size: 13px;
            color: #64748b;
            margin: 0;
        }

        /* Filter / Section Tabs */
        .settings-tabs-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .settings-tab-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-size: 13.5px;
            font-weight: 600;
            padding: 9px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .settings-tab-btn:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .settings-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }

        /* Form Card */
        .settings-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
            overflow: hidden;
        }
        .settings-card-header {
            padding: 16px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .settings-card-header h5 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .settings-card-body {
            padding: 24px;
        }

        /* Inputs & Labels */
        .form-label-clean {
            font-size: 13.5px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .form-control-clean {
            height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px;
            color: #0f172a;
            padding: 0 14px;
            transition: all 0.15s;
        }
        .form-control-clean:focus {
            border-color: #003b70;
            box-shadow: 0 0 0 3px rgba(0, 59, 112, 0.1);
        }
        textarea.form-control-clean {
            height: auto;
            padding: 12px 14px;
        }
        .char-counter {
            font-size: 11.5px;
            font-weight: 600;
            color: #94a3b8;
        }

        /* Asset Upload Box */
        .asset-upload-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }
        .asset-preview-box {
            width: 100%;
            height: 120px;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            position: relative;
        }
        .asset-preview-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .asset-upload-btn-wrap {
            position: relative;
            width: 100%;
        }
        .asset-upload-btn-wrap input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* Modern Table for Custom Keys */
        .custom-keys-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .custom-keys-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
        }
        .custom-keys-table tbody td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            vertical-align: middle;
        }
        .custom-keys-table tbody tr:hover td {
            background: #f8fafc;
        }

        /* Floating / Fixed Action Bar */
        .settings-action-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-top: 24px;
        }

        /* Buttons */
        .btn-save-main {
            background: #003b70;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            height: 42px;
            padding: 0 24px;
            font-size: 13.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-save-main:hover {
            background: #002b54;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 59, 112, 0.25);
        }
        .btn-clear-cache-tool {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            border-radius: 8px;
            height: 42px;
            padding: 0 18px;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-clear-cache-tool:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
    </style>
@endsection

@section('content')
    <!-- Page Header Breadcrumb -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark">Cài Đặt Hệ Thống</h4>
                <span class="ml-1 text-muted">Cấu hình thông tin doanh nghiệp, liên hệ, nhận diện thương hiệu và SEO</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Cài đặt</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Action Bar -->
    <div class="settings-header-card">
        <div class="settings-header-info">
            <h4>Bảng Điều Khiển Cấu Hình Website</h4>
            <p>Mọi thay đổi sẽ được cập nhật tức thì trên website sau khi lưu cấu hình.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-clear-cache-tool" onclick="clearSystemCache()">
                <i class="fa fa-bolt text-danger"></i> Xóa Cache Website
            </button>
            <button type="button" class="btn-save-main" onclick="saveAllSettings()">
                <i class="fa fa-check"></i> Lưu Tất Cả Cài Đặt
            </button>
        </div>
    </div>

    <!-- Section Tabs -->
    <div class="settings-tabs-nav">
        <button type="button" class="settings-tab-btn active" data-tab="general" onclick="switchTab('general')">
            <i class="fa fa-building-o"></i> Thông Tin Doanh Nghiệp
        </button>
        <button type="button" class="settings-tab-btn" data-tab="contact" onclick="switchTab('contact')">
            <i class="fa fa-phone"></i> Hotline &amp; Liên Hệ
        </button>
        <button type="button" class="settings-tab-btn" data-tab="branding" onclick="switchTab('branding')">
            <i class="fa fa-picture-o"></i> Logo &amp; Nhận Diện
        </button>
        <button type="button" class="settings-tab-btn" data-tab="seo" onclick="switchTab('seo')">
            <i class="fa fa-globe"></i> SEO &amp; Mạng Xã Hội
        </button>
        <button type="button" class="settings-tab-btn" data-tab="advanced" onclick="switchTab('advanced')">
            <i class="fa fa-sliders"></i> Tùy Chỉnh Nâng Cao
        </button>
    </div>

    <!-- Main Settings Form -->
    <form id="settingsMainForm" enctype="multipart/form-data">
        @csrf

        <!-- ═════════════════════════════════════════════════════════════════
             TAB 1: THÔNG TIN DOANH NGHIỆP
             ═════════════════════════════════════════════════════════════════ -->
        <div class="settings-tab-content" id="tab_general">
            <div class="settings-card">
                <div class="settings-card-header">
                    <h5><i class="fa fa-building-o text-primary"></i> Thông Tin Công Ty &amp; Website</h5>
                </div>
                <div class="settings-card-body">
                    <div class="row">
                        <!-- Tên thương hiệu -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Tên Thương Hiệu Rút Gọn (Brand Name)</label>
                            <input type="text" name="name" class="form-control form-control-clean" 
                                   value="{{ $settings->name ?? 'MISUTECH' }}" placeholder="Ví dụ: MISUTECH">
                            <small class="text-muted">Hiển thị trên header, logo title và các nhãn ngắn.</small>
                        </div>

                        <!-- Tên công ty đầy đủ -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Tên Doanh Nghiệp Đầy Đủ</label>
                            <input type="text" name="company" class="form-control form-control-clean" 
                                   value="{{ $settings->company ?? 'Công ty cổ phần Misutech' }}" placeholder="Ví dụ: Công ty cổ phần Misutech">
                            <small class="text-muted">Dùng cho xuất hóa đơn, chân trang và đăng ký pháp nhân.</small>
                        </div>

                        <!-- Mã số thuế -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Mã Số Thuế (Tax Code)</label>
                            <input type="text" name="tax" class="form-control form-control-clean" 
                                   value="{{ $settings->tax ?? '0202343708' }}" placeholder="Ví dụ: 0202343708">
                        </div>

                        <!-- Website URL -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Địa Chỉ Website Chính Thức (Domain)</label>
                            <input type="url" name="url" class="form-control form-control-clean" 
                                   value="{{ $settings->url ?? 'https://misutech.vn' }}" placeholder="https://misutech.vn">
                        </div>

                        <!-- Địa chỉ trụ sở -->
                        <div class="col-12 mb-3">
                            <label class="form-label-clean">Địa Chỉ Trụ Sở &amp; Chi Nhánh</label>
                            <textarea name="address" class="form-control form-control-clean" rows="2" 
                                      placeholder="Nhập địa chỉ trụ sở công ty">{{ $settings->address ?? 'Số 252 Đường Đại Thắng, Tổ 4, Phường Dương Kinh, Thành phố Hải Phòng, Việt Nam' }}</textarea>
                        </div>

                        <!-- Bản quyền chân trang -->
                        <div class="col-12 mb-0">
                            <label class="form-label-clean">Nội Dung Bản Quyền Chân Trang (Copyright)</label>
                            <textarea name="copyright" class="form-control form-control-clean" rows="2" 
                                      placeholder="Copyright &copy; 2026 Misutech. All Rights Reserved.">{{ $settings->copyright ?? '' }}</textarea>
                            <small class="text-muted">Hỗ trợ mã HTML đơn giản hoặc văn bản thuần.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════
             TAB 2: HOTLINE & LIÊN HỆ
             ═════════════════════════════════════════════════════════════════ -->
        <div class="settings-tab-content" id="tab_contact" style="display: none;">
            <div class="settings-card">
                <div class="settings-card-header">
                    <h5><i class="fa fa-phone text-success"></i> Kênh Liên Lạc &amp; Hỗ Trợ Khách Hàng</h5>
                </div>
                <div class="settings-card-body">
                    <div class="row">
                        <!-- Hotline -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Hotline Tư Vấn 24/7</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-phone text-success"></i></span>
                                </div>
                                <input type="text" name="hotline" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->hotline ?? '0866555212' }}" placeholder="0866555212">
                            </div>
                            <small class="text-muted">Hiển thị ở đầu trang, nút gọi nhanh và báo giá.</small>
                        </div>

                        <!-- Số điện thoại văn phòng -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Số Điện Thoại Văn Phòng / Hỗ Trợ</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-phone-square text-primary"></i></span>
                                </div>
                                <input type="text" name="phone" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->phone ?? '0866555212' }}" placeholder="0866555212">
                            </div>
                        </div>

                        <!-- Email công ty -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Email Doanh Nghiệp / Báo Giá</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-envelope text-warning"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->email ?? 'kinhdoanhhpt@haiphongtech.vn' }}" placeholder="kinhdoanhhpt@haiphongtech.vn">
                            </div>
                        </div>

                        <!-- Zalo Chat Link -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Đường Dẫn Zalo Chat / Zalo OA</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-commenting text-info"></i></span>
                                </div>
                                <input type="url" name="zalo" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->zalo ?? 'https://zalo.me/0866555212' }}" placeholder="https://zalo.me/0866555212">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════
             TAB 3: LOGO & NHẬN DIỆN THƯƠNG HIỆU
             ═════════════════════════════════════════════════════════════════ -->
        <div class="settings-tab-content" id="tab_branding" style="display: none;">
            <div class="settings-card">
                <div class="settings-card-header">
                    <h5><i class="fa fa-picture-o text-info"></i> Logo, Favicon &amp; Ảnh Chia Sẻ Mạng Xã Hội</h5>
                </div>
                <div class="settings-card-body">
                    <div class="row">
                        <!-- Logo Website -->
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="asset-upload-card">
                                <label class="form-label-clean mb-2 text-dark font-weight-bold">Logo Chính Website</label>
                                <div class="asset-preview-box" id="logoPreviewBox">
                                    @php
                                        $logoVal = $settings->site_logo ?? '';
                                        $logoUrl = $logoVal ? (Str::startsWith($logoVal, ['http://', 'https://']) ? $logoVal : asset('storage/clients/imgs/settings/' . $logoVal)) : asset('storage/clients/imgs/settings/logo.png');
                                    @endphp
                                    <img id="logoPreviewImg" src="{{ $logoUrl }}" alt="Site Logo">
                                </div>
                                <div class="asset-upload-btn-wrap mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 font-weight-bold">
                                        <i class="fa fa-upload mr-1"></i> Tải Lên Logo Mới
                                    </button>
                                    <input type="file" name="site_logo" accept="image/*" onchange="previewAssetImage(this, 'logoPreviewImg')">
                                </div>
                                <small class="text-muted mt-2 d-block">Định dạng PNG/SVG nền trong suốt. Kích thước chuẩn: ~250x60px.</small>
                            </div>
                        </div>

                        <!-- Favicon Icon -->
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="asset-upload-card">
                                <label class="form-label-clean mb-2 text-dark font-weight-bold">Favicon Tab Trình Duyệt</label>
                                <div class="asset-preview-box" id="faviconPreviewBox">
                                    @php
                                        $favVal = $settings->site_favicon ?? '';
                                        $favUrl = $favVal ? (Str::startsWith($favVal, ['http://', 'https://']) ? $favVal : asset('storage/clients/imgs/settings/' . $favVal)) : asset('storage/clients/imgs/settings/favicon.png');
                                    @endphp
                                    <img id="faviconPreviewImg" src="{{ $favUrl }}" alt="Site Favicon" style="max-width: 48px; max-height: 48px;">
                                </div>
                                <div class="asset-upload-btn-wrap mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 font-weight-bold">
                                        <i class="fa fa-upload mr-1"></i> Tải Lên Favicon
                                    </button>
                                    <input type="file" name="site_favicon" accept="image/png,image/x-icon,image/svg+xml,image/jpeg" onchange="previewAssetImage(this, 'faviconPreviewImg')">
                                </div>
                                <small class="text-muted mt-2 d-block">Biểu tượng nhỏ hiển thị tab trình duyệt. Chuẩn: 32x32px hoặc 48x48px (PNG/ICO).</small>
                            </div>
                        </div>

                        <!-- OpenGraph Social Banner Image -->
                        <div class="col-lg-4 col-md-12 col-12 mb-4">
                            <div class="asset-upload-card">
                                <label class="form-label-clean mb-2 text-dark font-weight-bold">Ảnh Chia Sẻ Mạng Xã Hội (OG Image)</label>
                                <div class="asset-preview-box" id="ogPreviewBox">
                                    @php
                                        $ogVal = $settings->og_image ?? '';
                                        $ogUrl = $ogVal ? (Str::startsWith($ogVal, ['http://', 'https://']) ? $ogVal : asset('storage/clients/imgs/settings/' . $ogVal)) : asset('storage/clients/imgs/settings/og-image.png');
                                    @endphp
                                    <img id="ogPreviewImg" src="{{ $ogUrl }}" alt="OG Image">
                                </div>
                                <div class="asset-upload-btn-wrap mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary w-100 font-weight-bold">
                                        <i class="fa fa-upload mr-1"></i> Tải Lên Ảnh OG Mới
                                    </button>
                                    <input type="file" name="og_image" accept="image/*" onchange="previewAssetImage(this, 'ogPreviewImg')">
                                </div>
                                <small class="text-muted mt-2 d-block">Hiển thị khi gửi link web qua Zalo, Facebook, Messenger. Chuẩn: 1200x630px.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═════════════════════════════════════════════════════════════════
             TAB 4: SEO & MẠNG XÃ HỘI
             ═════════════════════════════════════════════════════════════════ -->
        <div class="settings-tab-content" id="tab_seo" style="display: none;">
            <div class="settings-card">
                <div class="settings-card-header">
                    <h5><i class="fa fa-globe text-primary"></i> Cấu Hình SEO Trang Chủ &amp; Mã Theo Dõi</h5>
                </div>
                <div class="settings-card-body">
                    <div class="row">
                        <!-- Meta Title -->
                        <div class="col-12 mb-3">
                            <label class="form-label-clean">
                                <span>Tiêu Đề SEO Trang Chủ (Meta Title)</span>
                                <span class="char-counter" id="titleCounter">0/70 ký tự</span>
                            </label>
                            <input type="text" name="meta_title" id="metaTitleInput" class="form-control form-control-clean font-weight-bold" 
                                   value="{{ $settings->meta_title ?? '' }}" 
                                   placeholder="Tiêu đề hiển thị trên kết quả tìm kiếm Google"
                                   oninput="updateCharCount(this, 'titleCounter', 70)">
                            <small class="text-muted">Độ dài lý tưởng: 50 – 65 ký tự để tránh bị Google cắt ngắn.</small>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-12 mb-3">
                            <label class="form-label-clean">
                                <span>Mô Tả SEO Trang Chủ (Meta Description)</span>
                                <span class="char-counter" id="descCounter">0/165 ký tự</span>
                            </label>
                            <textarea name="meta_description" id="metaDescInput" class="form-control form-control-clean" rows="3" 
                                      placeholder="Đoạn trích mô tả tóm tắt nội dung dịch vụ, sản phẩm của website trên Google"
                                      oninput="updateCharCount(this, 'descCounter', 165)">{{ $settings->meta_description ?? '' }}</textarea>
                            <small class="text-muted">Độ dài lý tưởng: 150 – 160 ký tự.</small>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-12 mb-3">
                            <label class="form-label-clean">Từ Khóa SEO (Meta Keywords)</label>
                            <input type="text" name="meta_keywords" class="form-control form-control-clean" 
                                   value="{{ $settings->meta_keywords ?? '' }}" 
                                   placeholder="Từ khóa phân cách bằng dấu phẩy: biến tần, PLC, cảm biến tự động hóa...">
                        </div>

                        <!-- Google Analytics ID -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Mã Theo Dõi Google Analytics (Measurement ID / GTM)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-line-chart text-danger"></i></span>
                                </div>
                                <input type="text" name="google_analytics" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->google_analytics ?? '' }}" placeholder="Ví dụ: G-XXXXXXXXXX hoặc UA-XXXXXXXX-X">
                            </div>
                        </div>

                        <!-- Facebook Fanpage -->
                        <div class="col-md-6 col-12 mb-3">
                            <label class="form-label-clean">Facebook Fanpage URL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-facebook-official text-primary"></i></span>
                                </div>
                                <input type="url" name="facebook" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->facebook ?? 'https://www.facebook.com/misutech.vn' }}" placeholder="https://www.facebook.com/misutech.vn">
                            </div>
                        </div>

                        <!-- YouTube Channel -->
                        <div class="col-md-4 col-12 mb-3">
                            <label class="form-label-clean">YouTube Channel URL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-youtube-play text-danger"></i></span>
                                </div>
                                <input type="url" name="youtube" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->youtube ?? 'https://www.youtube.com/@misutech' }}" placeholder="https://www.youtube.com/@misutech">
                            </div>
                        </div>

                        <!-- TikTok Channel -->
                        <div class="col-md-4 col-12 mb-3">
                            <label class="form-label-clean">TikTok URL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-video-camera text-dark"></i></span>
                                </div>
                                <input type="url" name="tiktok" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->tiktok ?? 'https://www.tiktok.com/@misutech.vn' }}" placeholder="https://www.tiktok.com/@misutech.vn">
                            </div>
                        </div>

                        <!-- Twitter / X -->
                        <div class="col-md-4 col-12 mb-3">
                            <label class="form-label-clean">Twitter / X URL</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-twitter text-info"></i></span>
                                </div>
                                <input type="url" name="twitter" class="form-control form-control-clean border-left-0" 
                                       value="{{ $settings->twitter ?? 'https://x.com/misutech_vn' }}" placeholder="https://x.com/misutech_vn">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- ═════════════════════════════════════════════════════════════════
         TAB 5: CÀI ĐẶT TÙY CHỈNH NÂNG CAO (KEY-VALUE MANAGER)
         ═════════════════════════════════════════════════════════════════ -->
    <div class="settings-tab-content" id="tab_advanced" style="display: none;">
        <div class="settings-card">
            <div class="settings-card-header">
                <h5><i class="fa fa-database text-warning"></i> Quản Lý Khóa Cấu Hình Tùy Biến (Key-Value)</h5>
                <button type="button" class="btn btn-sm btn-primary font-weight-bold" onclick="openKeyModal()">
                    <i class="fa fa-plus mr-1"></i> + Thêm Khóa Mới
                </button>
            </div>
            <div class="settings-card-body p-0">
                <div class="table-responsive">
                    <table class="custom-keys-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th style="width: 200px;">Khóa Cấu Hình (Key)</th>
                                <th>Giá Trị (Value)</th>
                                <th style="width: 120px;">Kiểu Dữ Liệu</th>
                                <th style="width: 150px; text-align: right; white-space: nowrap;">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody id="customKeysBody">
                            <!-- Injected via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Floating Save Bar -->
    <div class="settings-action-bar">
        <div class="d-flex align-items-center gap-2">
            <i class="fa fa-info-circle text-primary"></i>
            <span class="text-muted font-size-13">Nhấn "Lưu Cài Đặt" để áp dụng ngay các thay đổi trên hệ thống.</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-clear-cache-tool" onclick="clearSystemCache()">
                <i class="fa fa-bolt text-danger"></i> Xóa Cache
            </button>
            <button type="button" class="btn-save-main" onclick="saveAllSettings()">
                <i class="fa fa-check"></i> Lưu Cài Đặt
            </button>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════
         MODAL CREATE / EDIT CUSTOM KEY
         ═════════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="customKeyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header py-3 px-4" style="background: #003b70; color: #ffffff;">
                    <h5 class="modal-title font-weight-bold text-white mb-0" id="customKeyModalTitle">Thêm Khóa Cấu Hình Mới</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="customKeyForm">
                    <div class="modal-body p-4">
                        <input type="hidden" id="custom_setting_id" name="id">
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">Khóa (Key Name) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="custom_key" name="key" required placeholder="Ví dụ: hotline_support, promo_banner...">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark mb-1">Kiểu Dữ Liệu (Type) <span class="text-danger">*</span></label>
                            <select class="form-control" id="custom_type" name="type" onchange="renderCustomDynamicInput()">
                                <option value="string">String (Văn bản ngắn)</option>
                                <option value="textarea">Textarea (Văn bản nhiều dòng)</option>
                                <option value="url">URL (Đường dẫn liên kết)</option>
                                <option value="email">Email</option>
                                <option value="integer">Integer (Số nguyên)</option>
                                <option value="float">Float (Số thực)</option>
                                <option value="boolean">Boolean (True/False)</option>
                                <option value="json">JSON</option>
                                <option value="image">Image (Hình ảnh)</option>
                            </select>
                        </div>

                        <div class="form-group mb-0" id="customValueContainer">
                            <label class="font-weight-bold text-dark mb-1">Giá Trị (Value)</label>
                            <input type="text" class="form-control" id="custom_value" name="value">
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3 px-4">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Đóng</button>
                        <button type="button" class="btn btn-primary font-weight-bold" onclick="saveCustomKey()" style="border-radius: 8px;">
                            <i class="fa fa-check mr-1"></i> Lưu Khóa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        $(document).ready(function() {
            // Khởi tạo bộ đếm ký tự cho Title và Description
            const titleInput = document.getElementById('metaTitleInput');
            if (titleInput) updateCharCount(titleInput, 'titleCounter', 70);

            const descInput = document.getElementById('metaDescInput');
            if (descInput) updateCharCount(descInput, 'descCounter', 165);

            // Load danh sách Custom Keys vào Tab 5
            loadCustomKeysList();
        });

        // Chuyển đổi Tab
        function switchTab(tabId) {
            $('.settings-tab-btn').removeClass('active');
            $(`.settings-tab-btn[data-tab="${tabId}"]`).addClass('active');

            $('.settings-tab-content').hide();
            $(`#tab_${tabId}`).fadeIn(150);
        }

        // Cập nhật bộ đếm ký tự
        function updateCharCount(input, counterId, maxLen) {
            const currentLen = input.value.length;
            const counter = document.getElementById(counterId);
            if (counter) {
                counter.textContent = `${currentLen}/${maxLen} ký tự`;
                if (currentLen > maxLen) {
                    counter.style.color = '#dc2626';
                } else if (currentLen >= maxLen * 0.8) {
                    counter.style.color = '#15803d';
                } else {
                    counter.style.color = '#64748b';
                }
            }
        }

        // Preview ảnh tải lên
        function previewAssetImage(input, previewImgId) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    toastr.warning('Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhẹ hơn!', 'Ảnh quá lớn');
                    input.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(`#${previewImgId}`).attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        }

        // Lưu toàn bộ cài đặt qua AJAX
        function saveAllSettings() {
            const form = $('#settingsMainForm')[0];
            const formData = new FormData(form);

            const saveBtns = $('.btn-save-main');
            saveBtns.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Đang lưu...');

            $.ajax({
                url: '{{ route("admin.api.settings.save_all") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    saveBtns.prop('disabled', false).html('<i class="fa fa-check"></i> Lưu Tất Cả Cài Đặt');
                    if (res && res.success) {
                        toastr.success(res.message, 'Thành công', { timeOut: 3000, closeButton: true });
                        loadCustomKeysList();
                    }
                },
                error: function(xhr) {
                    saveBtns.prop('disabled', false).html('<i class="fa fa-check"></i> Lưu Tất Cả Cài Đặt');
                    let errMsg = 'Có lỗi xảy ra khi lưu cài đặt!';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errMsg, 'Lỗi');
                }
            });
        }

        // Xóa Cache hệ thống
        function clearSystemCache() {
            swal({
                title: "Xóa Cache Website?",
                text: "Toàn bộ bộ nhớ đệm cấu hình và dữ liệu tạm sẽ được làm mới ngay lập tức!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#003b70",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Xác nhận xóa cache",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ route("admin.clear_cache") }}',
                        type: 'POST',
                        data: { _token: csrfToken },
                        success: function(res) {
                            toastr.success(res.message || 'Đã làm mới toàn bộ bộ nhớ đệm cache hệ thống!', 'Thành công');
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa cache hệ thống!', 'Lỗi');
                        }
                    });
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════
        // TAB 5: CUSTOM KEYS AJAX CRUD
        // ═════════════════════════════════════════════════════════════════
        function loadCustomKeysList() {
            $.ajax({
                url: '{{ route("admin.api.settings.list") }}',
                type: 'POST',
                data: { _token: csrfToken },
                success: function(res) {
                    if (res && res.data) {
                        renderCustomKeysTable(res.data);
                    }
                }
            });
        }

        function renderCustomKeysTable(data) {
            const tbody = $('#customKeysBody');
            tbody.empty();

            if (!data || data.length === 0) {
                tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">Chưa có khóa cấu hình nào</td></tr>');
                return;
            }

            data.forEach(item => {
                let displayVal = item.value || '';
                if (item.type === 'image' && item.value) {
                    displayVal = `<img src="{{ asset('storage/clients/imgs/settings') }}/${item.value}" height="32" class="border rounded p-1 bg-white"> <small class="text-muted ml-1">${item.value}</small>`;
                } else if (displayVal.length > 70) {
                    displayVal = escapeHtml(displayVal.substr(0, 70)) + '...';
                } else {
                    displayVal = escapeHtml(displayVal);
                }

                const rowHtml = `
                    <tr>
                        <td class="font-weight-bold text-muted font-monospace">#${item.id}</td>
                        <td class="font-weight-bold text-dark font-monospace">${escapeHtml(item.key)}</td>
                        <td>${displayVal}</td>
                        <td><span class="badge badge-light border text-dark font-weight-bold">${item.type}</span></td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold px-2 py-1" style="border-radius: 6px; white-space: nowrap;" onclick="editCustomKey(${item.id})">
                                    <i class="fa fa-pencil mr-1"></i> Sửa
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px; white-space: nowrap;" onclick="deleteCustomKey(${item.id}, '${escapeHtml(item.key)}')">
                                    <i class="fa fa-trash mr-1"></i> Xóa
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        }

        function openKeyModal() {
            $('#customKeyForm')[0].reset();
            $('#custom_setting_id').val('');
            $('#customKeyModalTitle').text('Thêm Khóa Cấu Hình Mới');
            $('#custom_key').prop('readonly', false);
            renderCustomDynamicInput();
            $('#customKeyModal').modal('show');
        }

        function renderCustomDynamicInput(initialVal = '') {
            const type = $('#custom_type').val();
            const container = $('#customValueContainer');
            let html = '<label class="font-weight-bold text-dark mb-1">Giá Trị (Value)</label>';

            if (['string', 'url', 'email'].includes(type)) {
                const inputType = type === 'email' ? 'email' : (type === 'url' ? 'url' : 'text');
                html += `<input type="${inputType}" class="form-control" id="custom_value" name="value" value="${escapeHtml(initialVal)}">`;
            } else if (type === 'textarea' || type === 'json') {
                html += `<textarea class="form-control" id="custom_value" name="value" rows="4">${escapeHtml(initialVal)}</textarea>`;
            } else if (['integer', 'float'].includes(type)) {
                html += `<input type="number" step="${type === 'float' ? '0.01' : '1'}" class="form-control" id="custom_value" name="value" value="${escapeHtml(initialVal)}">`;
            } else if (type === 'boolean') {
                html += `
                    <select class="form-control" id="custom_value" name="value">
                        <option value="1" ${initialVal === '1' || initialVal === 'true' ? 'selected' : ''}>Bật (True / 1)</option>
                        <option value="0" ${initialVal === '0' || initialVal === 'false' ? 'selected' : ''}>Tắt (False / 0)</option>
                    </select>
                `;
            } else if (type === 'image') {
                html += `
                    <input type="file" class="form-control-file" id="custom_value" name="value" accept="image/*">
                    <small class="text-muted d-block mt-1">Chọn file ảnh mới (Để trống nếu giữ nguyên ảnh cũ)</small>
                `;
                if (initialVal) {
                    html += `<div class="mt-2"><img src="{{ asset('storage/clients/imgs/settings') }}/${initialVal}" height="40" class="border rounded p-1"></div>`;
                }
            } else {
                html += `<input type="text" class="form-control" id="custom_value" name="value" value="${escapeHtml(initialVal)}">`;
            }

            container.html(html);
        }

        function editCustomKey(id) {
            $.get('{{ url("admin/api/settings") }}/' + id, function(res) {
                if (res && res.success) {
                    const data = res.data;
                    $('#custom_setting_id').val(data.id);
                    $('#custom_key').val(data.key);
                    $('#custom_type').val(data.type);
                    renderCustomDynamicInput(data.value);
                    $('#customKeyModalTitle').text(`Chỉnh sửa khóa: ${data.key}`);
                    $('#customKeyModal').modal('show');
                }
            });
        }

        function saveCustomKey() {
            const form = $('#customKeyForm')[0];
            const formData = new FormData(form);

            $.ajax({
                url: '{{ route("admin.api.settings.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res && res.success) {
                        $('#customKeyModal').modal('hide');
                        toastr.success(res.message, 'Thành công');
                        loadCustomKeysList();
                    }
                },
                error: function(xhr) {
                    let errMsg = 'Lỗi khi lưu khóa!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errMsg = Object.values(xhr.responseJSON.errors).map(e => e.join('<br>')).join('<br>');
                    }
                    toastr.error(errMsg, 'Lỗi');
                }
            });
        }

        function deleteCustomKey(id, keyName) {
            swal({
                title: `Xóa khóa "${keyName}"?`,
                text: "Khóa cấu hình này sẽ bị xóa khỏi cơ sở dữ liệu!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Vâng, xóa nó!",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ url("admin/api/settings") }}/' + id,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        success: function(res) {
                            if (res && res.success) {
                                toastr.success(res.message, 'Đã xóa');
                                loadCustomKeysList();
                            }
                        },
                        error: function() {
                            toastr.error('Không thể xóa khóa này!', 'Lỗi');
                        }
                    });
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
@endsection
