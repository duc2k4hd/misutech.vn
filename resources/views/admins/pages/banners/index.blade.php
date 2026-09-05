@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           BANNERS MANAGEMENT - MODERN, HIGH-CONTRAST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .banner-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .banner-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .banner-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .banner-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .banner-stat-box .stat-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .banner-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .banner-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Filter Tabs */
        .banner-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .banner-tab-btn {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 13.5px;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }
        .banner-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .banner-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .banner-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .banner-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Toolbar Container */
        .banner-toolbar-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        /* Search Input */
        .search-box-wrap {
            position: relative;
        }
        .search-box-wrap .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }
        .search-box-wrap input {
            padding-left: 38px;
            padding-right: 32px;
            height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px;
            transition: all 0.15s;
        }
        .search-box-wrap input:focus {
            border-color: #003b70;
            box-shadow: 0 0 0 3px rgba(0, 59, 112, 0.1);
        }
        .search-box-wrap .clear-search {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            display: none;
            font-size: 14px;
        }
        .search-box-wrap .clear-search:hover {
            color: #ef4444;
        }

        /* Select controls */
        .filter-select {
            height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13.5px;
            font-weight: 500;
            color: #334155;
            padding: 0 12px;
            background-color: #ffffff;
            transition: all 0.15s;
        }
        .filter-select:focus {
            border-color: #003b70;
            box-shadow: 0 0 0 3px rgba(0, 59, 112, 0.1);
        }

        /* View mode toggle */
        .view-mode-group {
            display: inline-flex;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            overflow: hidden;
            background: #f8fafc;
        }
        .view-mode-btn {
            background: transparent;
            border: none;
            padding: 8px 14px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .view-mode-btn.active {
            background: #003b70;
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        /* Action Buttons */
        .btn-add-primary {
            background: #003b70;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            height: 42px;
            padding: 0 20px;
            font-size: 13.5px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.15s;
        }
        .btn-add-primary:hover {
            background: #002b54;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 59, 112, 0.25);
        }
        .btn-refresh-tool {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            border-radius: 8px;
            height: 42px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-refresh-tool:hover {
            background: #f8fafc;
            color: #003b70;
            border-color: #94a3b8;
        }

        /* ═════════════════════════════════════════════════════════════════════
           SHOWCASE GRID CARDS
           ═════════════════════════════════════════════════════════════════════ */
        .banner-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            height: calc(100% - 24px);
            overflow: hidden;
            position: relative;
        }
        .banner-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.07);
            transform: translateY(-3px);
        }

        /* Banner Card Header */
        .banner-card-header {
            padding: 12px 16px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .banner-id-tag {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 6px;
            font-family: monospace;
        }

        /* Position Badges */
        .position-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.3px;
        }
        .position-badge.pos-hero {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .position-badge.pos-side-top {
            background: #fefce8;
            color: #a16207;
            border: 1px solid #fef08a;
        }
        .position-badge.pos-side-bottom {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #ffedd5;
        }
        .position-badge.pos-other {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
        }

        /* Banner Image Box */
        .banner-image-wrap {
            position: relative;
            background: #f8fafc;
            width: 100%;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
        }
        .banner-image-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .banner-card:hover .banner-image-wrap img {
            transform: scale(1.04);
        }
        .banner-image-wrap .no-image-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
        }
        .banner-image-wrap .no-image-placeholder i {
            font-size: 36px;
            color: #cbd5e1;
        }
        .banner-image-wrap .zoom-overlay-btn {
            position: absolute;
            right: 12px;
            bottom: 12px;
            padding: 6px 12px;
            border-radius: 6px;
            background: rgba(15, 23, 42, 0.8);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            opacity: 0;
            transform: translateY(4px);
            transition: all 0.2s ease;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .banner-image-wrap:hover .zoom-overlay-btn {
            opacity: 1;
            transform: translateY(0);
        }

        /* Banner Card Body */
        .banner-card-body {
            padding: 18px 16px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .banner-card-title {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 42px;
        }
        .banner-link-info {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12.5px;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 7px 10px;
            border-radius: 6px;
            margin-bottom: 12px;
            word-break: break-all;
        }
        .banner-link-info i {
            color: #64748b;
            flex-shrink: 0;
        }
        .banner-link-info a {
            color: #003b70;
            font-weight: 600;
            text-decoration: none;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .banner-link-info a:hover {
            text-decoration: underline;
        }

        /* Banner Card Footer */
        .banner-card-footer {
            padding: 12px 16px;
            background: #f8fafc;
            border-top: 1px solid #eef2f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        /* Action Buttons within Card */
        .btn-action-status {
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            border: 1px solid transparent;
        }
        .btn-action-status.active {
            background: #dcfce7;
            color: #15803d;
            border-color: #bbf7d0;
        }
        .btn-action-status.active:hover {
            background: #bbf7d0;
            color: #14532d;
        }
        .btn-action-status.draft {
            background: #f1f5f9;
            color: #64748b;
            border-color: #cbd5e1;
        }
        .btn-action-status.draft:hover {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-action-edit {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-action-edit:hover {
            background: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .btn-action-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            cursor: pointer;
        }
        .btn-action-delete:hover {
            background: #fee2e2;
            color: #991b1b;
            border-color: #fca5a5;
        }

        /* ═════════════════════════════════════════════════════════════════════
           TABLE VIEW STYLES
           ═════════════════════════════════════════════════════════════════════ */
        .banner-table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .modern-table thead th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .modern-table tbody td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 13.5px;
            color: #334155;
        }
        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }
        .modern-table tbody tr:hover td {
            background: #f8fafc;
        }
        .table-banner-img {
            width: 110px;
            height: 56px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .table-banner-img:hover {
            transform: scale(1.05);
            border-color: #003b70;
        }

        /* Empty State */
        .empty-state-box {
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 24px;
        }
        .empty-state-box i {
            font-size: 54px;
            color: #94a3b8;
            margin-bottom: 12px;
            display: inline-block;
        }
        .empty-state-box h5 {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
        }
        .empty-state-box p {
            font-size: 13.5px;
            color: #64748b;
            max-width: 420px;
            margin: 0 auto 18px;
        }

        /* Modal Customizations */
        .modal-clean-header {
            background: #003b70;
            color: #ffffff;
            padding: 18px 24px;
        }
        .modal-clean-header .modal-title {
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
        }
        .modal-clean-body {
            padding: 24px;
        }
        .modal-clean-footer {
            background: #f8fafc;
            padding: 16px 24px;
            border-top: 1px solid #eef2f6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Image Upload Box in Modal */
        .upload-dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }
        .upload-dropzone:hover {
            border-color: #003b70;
            background: #f1f5f9;
        }
        .upload-dropzone input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
        .upload-dropzone-content i {
            font-size: 36px;
            color: #64748b;
            margin-bottom: 8px;
        }
        .upload-preview-container {
            position: relative;
            display: inline-block;
            max-width: 100%;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            margin-top: 12px;
        }
        .upload-preview-container img {
            max-height: 180px;
            max-width: 100%;
            display: block;
            object-fit: contain;
            background: #f8fafc;
        }
        .upload-preview-container .btn-remove-preview {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(220, 38, 38, 0.9);
            color: #ffffff;
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .upload-preview-container .btn-remove-preview:hover {
            transform: scale(1.1);
        }

        /* Preset Position Badges in Modal */
        .pos-preset-btn {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #475569;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pos-preset-btn:hover {
            border-color: #003b70;
            background: #f8fafc;
            color: #003b70;
        }
        .pos-preset-btn.active {
            border-color: #003b70;
            background: #eff6ff;
            color: #003b70;
            font-weight: 700;
        }
    </style>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark">Quản lý Banner Quảng Cáo</h4>
                <span class="ml-1 text-muted">Điều chỉnh banner trang chủ (Hero Slider, Banner phụ) và các vị trí khuyến mãi</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Banners</a></li>
            </ol>
        </div>
    </div>

    <!-- 1. Top Metric Cards -->
    <div class="row">
        <!-- Tổng số Banner -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="banner-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Banner</span>
                    <div class="stat-icon-wrap" style="background: #e0e7ff; color: #4338ca;">
                        <i class="fa fa-picture-o"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ banner trong hệ thống</p>
            </div>
        </div>

        <!-- Slider Chính Trang Chủ -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="banner-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Slider Chính (VT 1)</span>
                    <div class="stat-icon-wrap" style="background: #dbeafe; color: #1e40af;">
                        <i class="fa fa-sliders"></i>
                    </div>
                </div>
                <div class="stat-value" id="statSlider">0</div>
                <p class="stat-desc">Banner trình chiếu Hero đầu trang</p>
            </div>
        </div>

        <!-- Banner Phụ & Quảng Cáo -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="banner-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Banner Phụ (VT 2+)</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #b45309;">
                        <i class="fa fa-clone"></i>
                    </div>
                </div>
                <div class="stat-value" id="statSecondary">0</div>
                <p class="stat-desc">Banner bên phải & thân trang</p>
            </div>
        </div>

        <!-- Đang Hoạt Động -->
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
            <div class="banner-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đang Hiển Thị</span>
                    <div class="stat-icon-wrap" style="background: #dcfce7; color: #15803d;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value" id="statActive">0</div>
                <p class="stat-desc">Trạng thái sẵn sàng hiển thị web</p>
            </div>
        </div>
    </div>

    <!-- 2. Status & Position Filter Tabs -->
    <div class="banner-status-tabs">
        <button type="button" class="banner-tab-btn active" data-tab="all" onclick="setTabFilter('all')">
            <i class="fa fa-th-large"></i> Tất cả <span class="tab-count" id="tabCountAll">0</span>
        </button>
        <button type="button" class="banner-tab-btn" data-tab="slider" onclick="setTabFilter('slider')">
            <i class="fa fa-sliders"></i> Slider Hero (VT 1) <span class="tab-count" id="tabCountSlider">0</span>
        </button>
        <button type="button" class="banner-tab-btn" data-tab="secondary" onclick="setTabFilter('secondary')">
            <i class="fa fa-columns"></i> Banner Phụ (VT 2-3) <span class="tab-count" id="tabCountSecondary">0</span>
        </button>
        <button type="button" class="banner-tab-btn" data-tab="other" onclick="setTabFilter('other')">
            <i class="fa fa-ellipsis-h"></i> Vị trí khác <span class="tab-count" id="tabCountOther">0</span>
        </button>
        <button type="button" class="banner-tab-btn" data-tab="active" onclick="setTabFilter('active')">
            <i class="fa fa-eye"></i> Đang hiển thị <span class="tab-count" id="tabCountActive">0</span>
        </button>
        <button type="button" class="banner-tab-btn" data-tab="draft" onclick="setTabFilter('draft')">
            <i class="fa fa-eye-slash"></i> Tạm ẩn <span class="tab-count" id="tabCountDraft">0</span>
        </button>
    </div>

    <!-- 3. Toolbar Container -->
    <div class="banner-toolbar-card">
        <div class="row align-items-center">
            <!-- Search Keyword -->
            <div class="col-xl-4 col-lg-4 col-md-6 col-12 mb-2 mb-lg-0">
                <div class="search-box-wrap">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" id="filterKeyword" class="form-control" placeholder="Tìm theo tiêu đề, link, ID banner...">
                    <i class="fa fa-times clear-search" id="clearSearchBtn" onclick="clearSearch()"></i>
                </div>
            </div>

            <!-- Position Filter -->
            <div class="col-xl-2 col-lg-2 col-md-3 col-6 mb-2 mb-lg-0">
                <select id="filterPosition" class="form-control filter-select" onchange="applyFilters()">
                    <option value="all">Tất cả vị trí</option>
                    <option value="1">Vị trí 1: Slider Hero</option>
                    <option value="2">Vị trí 2: Phụ Top</option>
                    <option value="3">Vị trí 3: Phụ Bottom</option>
                    <option value="4">Vị trí 4</option>
                    <option value="5">Vị trí 5</option>
                    <option value="6">Vị trí 6</option>
                </select>
            </div>

            <!-- Sort By -->
            <div class="col-xl-2 col-lg-2 col-md-3 col-6 mb-2 mb-lg-0">
                <select id="filterSort" class="form-control filter-select" onchange="applyFilters()">
                    <option value="position_asc">Vị trí tăng dần (1, 2...)</option>
                    <option value="position_desc">Vị trí giảm dần</option>
                    <option value="latest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="title_asc">Tiêu đề (A-Z)</option>
                </select>
            </div>

            <!-- Right Controls: View Switch, Refresh, Add -->
            <div class="col-xl-4 col-lg-4 col-md-12 col-12 d-flex align-items-center justify-content-lg-end justify-content-between gap-2 mt-2 mt-lg-0">
                <div class="view-mode-group">
                    <button type="button" class="view-mode-btn active" id="viewModeGrid" onclick="setViewMode('grid')" title="Dạng lưới thẻ">
                        <i class="fa fa-th"></i> Thẻ
                    </button>
                    <button type="button" class="view-mode-btn" id="viewModeTable" onclick="setViewMode('table')" title="Dạng bảng chi tiết">
                        <i class="fa fa-table"></i> Bảng
                    </button>
                </div>

                <button type="button" class="btn-refresh-tool" onclick="loadBanners()" title="Tải lại danh sách">
                    <i class="fa fa-refresh" id="refreshSpinner"></i> Làm mới
                </button>

                <button type="button" class="btn btn-add-primary font-weight-bold" onclick="openModal()">
                    <i class="fa fa-plus"></i>
                    <span>Thêm Banner Mới</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4. Main Content Area -->
    <!-- Grid Showcase View Container -->
    <div id="gridContainer" class="row">
        <!-- Rendered via AJAX -->
    </div>

    <!-- Table View Container (Hidden by default) -->
    <div id="tableContainer" class="banner-table-card" style="display: none;">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th style="width: 140px;">Hình ảnh</th>
                        <th>Tiêu đề Banner</th>
                        <th style="width: 200px;">Vị trí hiển thị</th>
                        <th>Đường dẫn liên kết (Link)</th>
                        <th style="width: 160px; text-align: center;">Trạng thái</th>
                        <th style="width: 180px; text-align: right;">Hành động</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Rendered via AJAX -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-state-box" style="display: none;">
        <i class="fa fa-picture-o"></i>
        <h5>Không tìm thấy banner nào</h5>
        <p>Không có banner nào khớp với điều kiện lọc hiện tại. Bạn có thể thử thay đổi bộ lọc hoặc thêm banner mới.</p>
        <button type="button" class="btn btn-add-primary" onclick="openModal()">
            <i class="fa fa-plus"></i> Thêm Banner Mới
        </button>
    </div>

    <!-- Loading Indicator -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Đang tải dữ liệu...</span>
        </div>
        <p class="text-muted mt-3 font-weight-500">Đang tải danh sách banner...</p>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         MODAL CREATE / EDIT BANNER
         ═════════════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="bannerModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-clean-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="modal-title font-weight-bold" id="modalTitle">Thêm Banner Mới</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="font-size: 22px; opacity: 0.9;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <form id="bannerForm" enctype="multipart/form-data">
                    <div class="modal-clean-body">
                        <input type="hidden" id="banner_id" name="id">

                        <div class="row">
                            <!-- Left Column: Title, Link, Position, Status -->
                            <div class="col-lg-7 col-12">
                                <!-- Tiêu đề -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1">
                                        Tiêu đề Banner <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="title" name="title" required placeholder="Ví dụ: Mua sắm nhiều Ưu đãi lớn">
                                </div>

                                <!-- Đường dẫn Link -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1">
                                        Đường dẫn liên kết (Link Đích)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-link"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0" id="link" name="link" placeholder="https://misutech.vn/khuyen-mai hoặc #">
                                    </div>
                                    <small class="text-muted">Nhập URL đầy đủ hoặc để dấu # nếu không có liên kết</small>
                                </div>

                                <!-- Vị trí hiển thị (Position) -->
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark mb-1">
                                        Vị trí hiển thị (Thứ tự) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control font-weight-bold" id="position" name="position" value="1" min="1" required placeholder="1, 2, 3...">
                                    
                                    <!-- Nút chọn nhanh vị trí -->
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        <button type="button" class="pos-preset-btn active" onclick="setFormPosition(1, this)">
                                            <i class="fa fa-sliders"></i> VT 1: Slider Hero
                                        </button>
                                        <button type="button" class="pos-preset-btn" onclick="setFormPosition(2, this)">
                                            <i class="fa fa-columns"></i> VT 2: Phụ Top
                                        </button>
                                        <button type="button" class="pos-preset-btn" onclick="setFormPosition(3, this)">
                                            <i class="fa fa-columns"></i> VT 3: Phụ Bottom
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-1">
                                        <i class="fa fa-info-circle"></i> 
                                        <strong>Vị trí 1</strong> là Slider chuyển động đầu trang; <strong>Vị trí 2 & 3</strong> là 2 banner nhỏ bên phải.
                                    </small>
                                </div>

                                <!-- Trạng thái -->
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark mb-1">Trạng thái hiển thị</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active">Hiển thị trên website</option>
                                        <option value="draft">Tạm ẩn</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Right Column: Image Upload & Preview -->
                            <div class="col-lg-5 col-12 mt-3 mt-lg-0">
                                <label class="font-weight-bold text-dark mb-1">
                                    Hình ảnh Banner <span class="text-danger" id="imageRequiredStar">*</span>
                                </label>
                                
                                <div class="upload-dropzone" id="uploadDropzone">
                                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/webp,image/gif,image/svg+xml" onchange="previewSelectedImage(this)">
                                    <div class="upload-dropzone-content" id="dropzonePrompt">
                                        <i class="fa fa-cloud-upload"></i>
                                        <p class="mb-1 font-weight-bold text-dark font-size-14">Chọn file ảnh tải lên</p>
                                        <p class="text-muted font-size-12 mb-0">JPG, PNG, WEBP, GIF (Tối đa 5MB)</p>
                                    </div>
                                </div>

                                <!-- Image Preview Container -->
                                <div id="imagePreviewContainer" style="display: none;" class="text-center mt-2">
                                    <div class="upload-preview-container">
                                        <img id="imagePreview" src="" alt="Banner Preview">
                                        <button type="button" class="btn-remove-preview" onclick="removeImagePreview()" title="Xóa ảnh này">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Gợi ý kích thước chuẩn -->
                                <div class="alert alert-light border mt-3 p-2 font-size-12 mb-0">
                                    <strong class="text-dark"><i class="fa fa-lightbulb-o text-warning mr-1"></i> Kích thước tối ưu:</strong>
                                    <ul class="mb-0 pl-3 mt-1 text-muted">
                                        <li>Slider Hero (VT 1): <strong>1200 x 500 px</strong></li>
                                        <li>Banner Phụ (VT 2, 3): <strong>400 x 240 px</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-clean-footer">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">Đóng</button>
                        <button type="submit" class="btn btn-add-primary font-weight-bold" id="saveBannerBtn">
                            <i class="fa fa-check mr-1"></i> <span>Lưu Banner</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════
         IMAGE PREVIEW / LIGHTBOX MODAL
         ═════════════════════════════════════════════════════════════════════ -->
    <div class="modal fade" id="imageLightboxModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; background: #0f172a; border: none;">
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom border-secondary">
                    <span class="text-white font-weight-bold" id="lightboxTitle">Xem ảnh banner</span>
                    <div>
                        <a href="#" id="lightboxOpenNewTab" target="_blank" class="btn btn-sm btn-outline-light mr-2 font-weight-bold">
                            <i class="fa fa-external-link mr-1"></i> Mở tab mới
                        </a>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <div class="modal-body p-0 text-center bg-black d-flex align-items-center justify-content-center" style="min-height: 300px; max-height: 80vh; overflow: auto;">
                    <img id="lightboxImage" src="" style="max-width: 100%; max-height: 75vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Setup CSRF cho Ajax
        const csrfToken = '{{ csrf_token() }}';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        // Global State
        let allBannersData = [];
        let currentViewMode = 'grid'; // 'grid' or 'table'
        let currentTabFilter = 'all'; // 'all', 'slider', 'secondary', 'other', 'active', 'draft'
        let searchTimeout = null;

        const assetBannerBasePath = '{{ asset("storage/clients/imgs/banners") }}/';

        $(document).ready(function() {
            // Load initial banner data
            loadBanners();

            // Search debounce
            $('#filterKeyword').on('input', function() {
                const val = $(this).val();
                if (val.trim() !== '') {
                    $('#clearSearchBtn').show();
                } else {
                    $('#clearSearchBtn').hide();
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 300);
            });

            // Form Submit handler
            $('#bannerForm').on('submit', function(e) {
                e.preventDefault();
                saveBanner();
            });
        });

        // Load banners from API
        function loadBanners() {
            $('#loadingState').show();
            $('#gridContainer').hide();
            $('#tableContainer').hide();
            $('#emptyState').hide();
            $('#refreshSpinner').addClass('fa-spin');

            $.ajax({
                url: '{{ route("admin.api.banners.list") }}',
                type: 'POST',
                data: {
                    _token: csrfToken
                },
                success: function(res) {
                    $('#loadingState').hide();
                    $('#refreshSpinner').removeClass('fa-spin');
                    if (res && res.data) {
                        allBannersData = res.data;
                        updateStats(res.stats || {});
                        renderBanners();
                    } else {
                        showEmptyState();
                    }
                },
                error: function(xhr) {
                    $('#loadingState').hide();
                    $('#refreshSpinner').removeClass('fa-spin');
                    toastr.error('Không thể tải danh sách banner. Vui lòng thử lại!', 'Lỗi kết nối');
                    showEmptyState();
                }
            });
        }

        // Update Stats Counters
        function updateStats(stats) {
            $('#statTotal').text(stats.total || 0);
            $('#statSlider').text(stats.slider || 0);
            $('#statSecondary').text(stats.secondary || 0);
            $('#statActive').text(stats.active || 0);

            $('#tabCountAll').text(stats.total || 0);
            $('#tabCountSlider').text(stats.slider || 0);
            $('#tabCountSecondary').text(stats.secondary || 0);
            $('#tabCountOther').text(stats.other || 0);
            $('#tabCountActive').text(stats.active || 0);
            $('#tabCountDraft').text(stats.draft || 0);
        }

        // Apply Tab Filter
        function setTabFilter(tab) {
            currentTabFilter = tab;
            $('.banner-tab-btn').removeClass('active');
            $(`.banner-tab-btn[data-tab="${tab}"]`).addClass('active');

            // Reset position dropdown if tab matches specific preset
            if (tab === 'slider') {
                $('#filterPosition').val('1');
            } else if (tab === 'all') {
                $('#filterPosition').val('all');
            }

            renderBanners();
        }

        // Clear Search
        function clearSearch() {
            $('#filterKeyword').val('');
            $('#clearSearchBtn').hide();
            applyFilters();
        }

        // Apply all search & filters
        function applyFilters() {
            renderBanners();
        }

        // Set View Mode (Grid or Table)
        function setViewMode(mode) {
            currentViewMode = mode;
            if (mode === 'grid') {
                $('#viewModeGrid').addClass('active');
                $('#viewModeTable').removeClass('active');
                $('#gridContainer').show();
                $('#tableContainer').hide();
            } else {
                $('#viewModeTable').addClass('active');
                $('#viewModeGrid').removeClass('active');
                $('#tableContainer').show();
                $('#gridContainer').hide();
            }
            renderBanners();
        }

        // Helper: Get filtered and sorted banners
        function getFilteredBanners() {
            let filtered = [...allBannersData];
            const keyword = ($('#filterKeyword').val() || '').toLowerCase().trim();
            const position = $('#filterPosition').val();
            const sort = $('#filterSort').val();

            // Tab filter logic
            if (currentTabFilter === 'slider') {
                filtered = filtered.filter(b => parseInt(b.position) === 1);
            } else if (currentTabFilter === 'secondary') {
                filtered = filtered.filter(b => [2, 3].includes(parseInt(b.position)));
            } else if (currentTabFilter === 'other') {
                filtered = filtered.filter(b => ![1, 2, 3].includes(parseInt(b.position)));
            } else if (currentTabFilter === 'active') {
                filtered = filtered.filter(b => b.status === 'active');
            } else if (currentTabFilter === 'draft') {
                filtered = filtered.filter(b => b.status === 'draft');
            }

            // Keyword filter
            if (keyword) {
                filtered = filtered.filter(b => {
                    const title = (b.title || '').toLowerCase();
                    const link = (b.link || '').toLowerCase();
                    const idStr = String(b.id);
                    return title.includes(keyword) || link.includes(keyword) || idStr.includes(keyword);
                });
            }

            // Position select filter
            if (position && position !== 'all') {
                filtered = filtered.filter(b => parseInt(b.position) === parseInt(position));
            }

            // Sort logic
            filtered.sort((a, b) => {
                const posA = a.position !== null ? parseInt(a.position) : 9999;
                const posB = b.position !== null ? parseInt(b.position) : 9999;

                if (sort === 'position_asc') {
                    return posA - posB || b.id - a.id;
                } else if (sort === 'position_desc') {
                    return posB - posA || b.id - a.id;
                } else if (sort === 'latest') {
                    return b.id - a.id;
                } else if (sort === 'oldest') {
                    return a.id - b.id;
                } else if (sort === 'title_asc') {
                    return (a.title || '').localeCompare(b.title || '');
                }
                return posA - posB;
            });

            return filtered;
        }

        // Render Banners into DOM
        function renderBanners() {
            const list = getFilteredBanners();

            if (!list || list.length === 0) {
                $('#gridContainer').hide();
                $('#tableContainer').hide();
                $('#emptyState').show();
                return;
            }

            $('#emptyState').hide();

            if (currentViewMode === 'grid') {
                $('#tableContainer').hide();
                $('#gridContainer').empty().show();

                list.forEach(b => {
                    const cardHtml = generateBannerCardHtml(b);
                    $('#gridContainer').append(cardHtml);
                });
            } else {
                $('#gridContainer').hide();
                $('#tableContainer').show();
                $('#tableBody').empty();

                list.forEach(b => {
                    const rowHtml = generateBannerTableRowHtml(b);
                    $('#tableBody').append(rowHtml);
                });
            }
        }

        // Position Badge Helper
        function getPositionBadge(pos) {
            const p = parseInt(pos);
            if (p === 1) {
                return '<span class="position-badge pos-hero"><i class="fa fa-sliders"></i> VT 1: Slider Hero</span>';
            } else if (p === 2) {
                return '<span class="position-badge pos-side-top"><i class="fa fa-columns"></i> VT 2: Phụ Top</span>';
            } else if (p === 3) {
                return '<span class="position-badge pos-side-bottom"><i class="fa fa-columns"></i> VT 3: Phụ Bottom</span>';
            } else {
                return `<span class="position-badge pos-other"><i class="fa fa-tag"></i> Vị trí ${pos || 1}</span>`;
            }
        }

        // Generate Grid Card HTML
        function generateBannerCardHtml(b) {
            const imageUrl = b.image 
                ? (b.image.startsWith('http://') || b.image.startsWith('https://') ? b.image : assetBannerBasePath + b.image)
                : null;
            
            const positionBadge = getPositionBadge(b.position);
            const isActive = b.status === 'active';

            const statusBtnHtml = isActive
                ? `<button type="button" class="btn-action-status active" onclick="toggleBannerStatus(${b.id})" title="Nhấp để ẩn banner">
                        <i class="fa fa-check-circle"></i> Hiển thị
                   </button>`
                : `<button type="button" class="btn-action-status draft" onclick="toggleBannerStatus(${b.id})" title="Nhấp để hiển thị banner">
                        <i class="fa fa-eye-slash"></i> Tạm ẩn
                   </button>`;

            const linkDisplay = b.link 
                ? `<div class="banner-link-info">
                        <i class="fa fa-link"></i>
                        <a href="${escapeHtml(b.link)}" target="_blank" title="${escapeHtml(b.link)}">${escapeHtml(b.link)}</a>
                   </div>`
                : `<div class="banner-link-info text-muted">
                        <i class="fa fa-chain-broken"></i>
                        <span>Không có liên kết</span>
                   </div>`;

            const imageContent = imageUrl
                ? `<img src="${imageUrl}" alt="${escapeHtml(b.title)}" loading="lazy">
                   <span class="zoom-overlay-btn" onclick="openLightbox('${imageUrl}', '${escapeHtml(b.title)}')">
                       <i class="fa fa-search-plus"></i> Phóng to
                   </span>`
                : `<div class="no-image-placeholder">
                       <i class="fa fa-picture-o"></i>
                       <span>Chưa có ảnh</span>
                   </div>`;

            return `
                <div class="col-xl-4 col-lg-6 col-md-6 col-12">
                    <div class="banner-card">
                        <!-- Header -->
                        <div class="banner-card-header">
                            <span class="banner-id-tag">#${b.id}</span>
                            ${positionBadge}
                        </div>

                        <!-- Image Box -->
                        <div class="banner-image-wrap" onclick="openLightbox('${imageUrl || ''}', '${escapeHtml(b.title)}')">
                            ${imageContent}
                        </div>

                        <!-- Body -->
                        <div class="banner-card-body">
                            <h6 class="banner-card-title" title="${escapeHtml(b.title)}">${escapeHtml(b.title)}</h6>
                            ${linkDisplay}
                        </div>

                        <!-- Footer -->
                        <div class="banner-card-footer">
                            <div>${statusBtnHtml}</div>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn-action-edit" onclick="editBanner(${b.id})" title="Chỉnh sửa banner">
                                    <i class="fa fa-pencil"></i> Sửa
                                </button>
                                <button type="button" class="btn-action-delete" onclick="deleteBanner(${b.id})" title="Xóa banner">
                                    <i class="fa fa-trash"></i> Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Generate Table Row HTML
        function generateBannerTableRowHtml(b) {
            const imageUrl = b.image 
                ? (b.image.startsWith('http://') || b.image.startsWith('https://') ? b.image : assetBannerBasePath + b.image)
                : null;
            
            const positionBadge = getPositionBadge(b.position);
            const isActive = b.status === 'active';

            const statusBtnHtml = isActive
                ? `<button type="button" class="btn btn-xs btn-outline-success font-weight-bold px-2 py-1" style="border-radius: 6px;" onclick="toggleBannerStatus(${b.id})" title="Nhấp để chuyển sang tạm ẩn">
                        <i class="fa fa-check-circle mr-1"></i> Hiển thị
                   </button>`
                : `<button type="button" class="btn btn-xs btn-outline-secondary font-weight-bold px-2 py-1" style="border-radius: 6px;" onclick="toggleBannerStatus(${b.id})" title="Nhấp để kích hoạt hiển thị">
                        <i class="fa fa-eye-slash mr-1"></i> Tạm ẩn
                   </button>`;

            const imgHtml = imageUrl
                ? `<img src="${imageUrl}" class="table-banner-img" alt="${escapeHtml(b.title)}" onclick="openLightbox('${imageUrl}', '${escapeHtml(b.title)}')">`
                : `<span class="badge badge-light p-2 text-muted"><i class="fa fa-picture-o"></i> Không có ảnh</span>`;

            const linkHtml = b.link 
                ? `<a href="${escapeHtml(b.link)}" target="_blank" class="text-primary font-weight-bold d-inline-flex align-items-center gap-1" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <i class="fa fa-link mr-1"></i> ${escapeHtml(b.link)}
                   </a>`
                : `<span class="text-muted font-italic">Không có link</span>`;

            return `
                <tr>
                    <td class="font-weight-bold text-muted font-monospace">#${b.id}</td>
                    <td>${imgHtml}</td>
                    <td>
                        <div class="font-weight-bold text-dark mb-1" style="font-size: 14px;">${escapeHtml(b.title)}</div>
                    </td>
                    <td>${positionBadge}</td>
                    <td>${linkHtml}</td>
                    <td style="text-align: center;">
                        ${statusBtnHtml}
                    </td>
                    <td style="text-align: right;">
                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold px-2 py-1 mr-1" style="border-radius: 6px;" onclick="editBanner(${b.id})" title="Sửa banner">
                            <i class="fa fa-pencil mr-1"></i> Sửa
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold px-2 py-1" style="border-radius: 6px;" onclick="deleteBanner(${b.id})" title="Xóa banner">
                            <i class="fa fa-trash mr-1"></i> Xóa
                        </button>
                    </td>
                </tr>
            `;
        }

        // Preset Position button click in Modal
        function setFormPosition(pos, btn) {
            $('#position').val(pos);
            $('.pos-preset-btn').removeClass('active');
            $(btn).addClass('active');
        }

        // Preview selected file before upload
        function previewSelectedImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 5 * 1024 * 1024) {
                    toastr.warning('Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhẹ hơn!', 'Ảnh quá lớn');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewContainer').show();
                    $('#dropzonePrompt').hide();
                };
                reader.readAsDataURL(file);
            }
        }

        // Remove preview
        function removeImagePreview() {
            $('#image').val('');
            $('#imagePreview').attr('src', '');
            $('#imagePreviewContainer').hide();
            $('#dropzonePrompt').show();
        }

        // Open Modal for Create
        function openModal() {
            $('#bannerForm')[0].reset();
            $('#banner_id').val('');
            $('#modalTitle').text('Thêm Banner Mới');
            $('#position').val(1);
            $('#status').val('active');
            
            // Highlight position 1 preset
            $('.pos-preset-btn').removeClass('active');
            $('.pos-preset-btn:first').addClass('active');

            // Reset image preview & require image
            removeImagePreview();
            $('#image').prop('required', true);
            $('#imageRequiredStar').show();

            $('#bannerModal').modal('show');
        }

        // Open Modal for Edit
        function editBanner(id) {
            $.get('{{ url("admin/api/banners") }}/' + id, function(res) {
                if (res && res.success) {
                    const data = res.data;
                    $('#banner_id').val(data.id);
                    $('#title').val(data.title);
                    $('#link').val(data.link || '');
                    $('#position').val(data.position || 1);
                    $('#status').val(data.status || 'active');

                    // Set position preset button state
                    $('.pos-preset-btn').removeClass('active');
                    $(`.pos-preset-btn[onclick*="${data.position}"]`).addClass('active');

                    // Set image preview (not strictly required on edit)
                    $('#image').prop('required', false);
                    $('#imageRequiredStar').hide();

                    if (data.image) {
                        const imgUrl = data.image.startsWith('http://') || data.image.startsWith('https://') 
                            ? data.image 
                            : assetBannerBasePath + data.image;
                        $('#imagePreview').attr('src', imgUrl);
                        $('#imagePreviewContainer').show();
                        $('#dropzonePrompt').hide();
                    } else {
                        removeImagePreview();
                    }

                    $('#modalTitle').text(`Chỉnh sửa Banner: ${data.title}`);
                    $('#bannerModal').modal('show');
                }
            }).fail(function() {
                toastr.error('Không tìm thấy thông tin banner!', 'Lỗi');
            });
        }

        // Save Banner (Ajax Form Submit)
        function saveBanner() {
            const form = $('#bannerForm')[0];
            if (!form.reportValidity()) return;

            const formData = new FormData(form);
            const saveBtn = $('#saveBannerBtn');
            const originalBtnHtml = saveBtn.html();

            saveBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Đang lưu...');

            $.ajax({
                url: '{{ route("admin.api.banners.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    saveBtn.prop('disabled', false).html(originalBtnHtml);
                    if (res && res.success) {
                        $('#bannerModal').modal('hide');
                        toastr.success(res.message, 'Thành công', { timeOut: 3000, closeButton: true });
                        loadBanners();
                    }
                },
                error: function(xhr) {
                    saveBtn.prop('disabled', false).html(originalBtnHtml);
                    let errorMsg = 'Có lỗi xảy ra khi lưu banner!';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).map(e => e.join('<br>')).join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    toastr.error(errorMsg, 'Lỗi', { timeOut: 5000, closeButton: true });
                }
            });
        }

        // Quick Toggle Banner Status
        function toggleBannerStatus(id) {
            $.ajax({
                url: '{{ url("admin/api/banners/toggle-status") }}/' + id,
                type: 'POST',
                data: {
                    _token: csrfToken
                },
                success: function(res) {
                    if (res && res.success) {
                        toastr.success(res.message, 'Cập nhật trạng thái', { timeOut: 2500 });
                        
                        // Update in local data
                        const found = allBannersData.find(b => b.id === id);
                        if (found) {
                            found.status = res.data.status;
                        }
                        
                        // Recount stats
                        const activeCount = allBannersData.filter(b => b.status === 'active').length;
                        const draftCount = allBannersData.filter(b => b.status === 'draft').length;
                        $('#statActive').text(activeCount);
                        $('#tabCountActive').text(activeCount);
                        $('#tabCountDraft').text(draftCount);

                        renderBanners();
                    }
                },
                error: function() {
                    toastr.error('Không thể thay đổi trạng thái banner!', 'Lỗi');
                }
            });
        }

        // Delete Banner with SweetAlert2
        function deleteBanner(id) {
            swal({
                title: "Xác nhận xóa Banner?",
                text: "Banner và tệp hình ảnh đính kèm sẽ bị xóa vĩnh viễn khỏi hệ thống!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                cancelButtonColor: "#64748b",
                confirmButtonText: "Vâng, xóa ngay!",
                cancelButtonText: "Hủy bỏ",
                closeOnConfirm: true
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ url("admin/api/banners") }}/' + id,
                        type: 'DELETE',
                        data: {
                            _token: csrfToken
                        },
                        success: function(res) {
                            if (res && res.success) {
                                toastr.success(res.message, 'Đã xóa banner');
                                loadBanners();
                            }
                        },
                        error: function() {
                            toastr.error('Không thể xóa banner. Vui lòng thử lại sau!', 'Lỗi');
                        }
                    });
                }
            });
        }

        // Lightbox Preview
        function openLightbox(imageUrl, title) {
            if (!imageUrl) return;
            $('#lightboxImage').attr('src', imageUrl);
            $('#lightboxTitle').text(title || 'Xem ảnh banner');
            $('#lightboxOpenNewTab').attr('href', imageUrl);
            $('#imageLightboxModal').modal('show');
        }

        // Safe HTML Escape Helper
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
