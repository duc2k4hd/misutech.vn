@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           QUOTES MANAGEMENT - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .quote-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .quote-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .quote-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .quote-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .quote-stat-box .stat-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .quote-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .quote-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Status Filter Tabs */
        .quote-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .quote-tab-btn {
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
        .quote-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .quote-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .quote-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .quote-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Status Badges - Clean Elegant Pastel */
        .badge-quote {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .badge-quote-draft {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .badge-quote-sent {
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
        }
        .badge-quote-confirmed {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-quote-completed {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .badge-quote-cancelled {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* Quote Card Items (Feed View) */
        .quote-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .quote-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }
        .quote-card.selected {
            border-color: #003b70;
            background: #f8fafc;
        }
        .quote-card-header {
            padding: 14px 22px;
            background: #f8fafc;
            border-bottom: 1px solid #eef2f6;
            border-top-left-radius: 11px;
            border-top-right-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .quote-code-tag {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-weight: 800;
            color: #003b70;
            background: #e0f2fe;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 13.5px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #bae6fd;
            transition: all 0.15s;
        }
        .quote-code-tag:hover {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
        }
        .quote-card-body {
            padding: 22px 24px;
        }
        .customer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #003b70;
            font-weight: 800;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #e2e8f0;
        }
        .customer-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .customer-company {
            font-size: 13.5px;
            color: #475569;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 10px;
        }
        .customer-phone-link {
            font-size: 13px;
            font-weight: 700;
            color: #0369a1;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid #bae6fd;
            transition: all 0.15s;
        }
        .customer-phone-link:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            text-decoration: none;
        }
        .customer-email-link {
            font-size: 12.5px;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: 5px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            transition: all 0.15s;
        }
        .customer-email-link:hover {
            color: #0f172a;
            border-color: #cbd5e1;
            text-decoration: none;
        }

        /* Items Preview in Card */
        .quote-items-preview {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 10px;
            padding: 14px 18px;
        }
        .quote-items-preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .quote-items-preview-item {
            font-size: 13.5px;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px;
            gap: 12px;
        }
        .quote-items-preview-item:last-child {
            margin-bottom: 0;
        }
        .quote-item-name {
            font-weight: 600;
            color: #0f172a;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 280px;
        }
        .quote-item-qty {
            font-weight: 700;
            color: #475569;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            flex-shrink: 0;
        }

        /* Total Amount in Card */
        .quote-amount-label {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .quote-amount-highlight {
            font-size: 23px;
            font-weight: 800;
            color: #003b70;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        /* Quick Status Select */
        .quick-status-select {
            font-size: 13px;
            font-weight: 600;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 4px 12px;
            color: #1e293b;
            cursor: pointer;
        }
        .quick-status-select:focus {
            border-color: #003b70;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 59, 112, 0.15);
        }

        /* Empty state */
        .quote-empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
        }
        .quote-empty-state i {
            font-size: 44px;
            color: #94a3b8;
            margin-bottom: 14px;
            display: inline-block;
        }

        /* Bulk action bar */
        .bulk-action-bar {
            background: #0f172a;
            color: #ffffff;
            border-radius: 10px;
            padding: 12px 22px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
            animation: slideDown 0.2s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Checkbox styling */
        .quote-custom-check {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #003b70;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark mb-1">Quản Lý Báo Giá Dự Án & Thiết Bị</h4>
                <span class="text-muted" style="font-size: 13.5px;">Theo dõi, liên hệ và xử lý các yêu cầu bảng báo giá trực tuyến từ khách hàng</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Báo giá</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Metric Cards (Spacious, Clean, Minimalist) -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="quote-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Báo Giá</span>
                    <div class="stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                        <i class="fa fa-file-text-o"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ bảng giá đã lập</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="quote-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Chờ Xử Lý / Mới</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="stat-value text-dark" id="statDraft">0</div>
                <p class="stat-desc">Khách vừa lập cần liên hệ</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="quote-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đã Chốt / Thành Công</span>
                    <div class="stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                </div>
                <div class="stat-value text-success" id="statConfirmed">0</div>
                <p class="stat-desc">Khách đã chốt đơn / đặt cọc</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="quote-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Doanh Số Ước Tính</span>
                    <div class="stat-icon-wrap" style="background: #e0f2fe; color: #0284c7;">
                        <i class="fa fa-money"></i>
                    </div>
                </div>
                <div class="stat-value text-primary" id="statTotalAmount">0 đ</div>
                <p class="stat-desc">Giá trị lũy kế tất cả báo giá</p>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0" style="border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div class="card-body p-4">
                    
                    <!-- Status Filter Tabs -->
                    <div class="quote-status-tabs">
                        <button type="button" class="quote-tab-btn active" data-status="all" onclick="filterByStatus('all', this)">
                            Tất cả <span class="tab-count" id="tabCountAll">0</span>
                        </button>
                        <button type="button" class="quote-tab-btn" data-status="draft" onclick="filterByStatus('draft', this)">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#64748b;"></span> Mới lập <span class="tab-count" id="tabCountDraft">0</span>
                        </button>
                        <button type="button" class="quote-tab-btn" data-status="sent" onclick="filterByStatus('sent', this)">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#0284c7;"></span> Đã gửi khách <span class="tab-count" id="tabCountSent">0</span>
                        </button>
                        <button type="button" class="quote-tab-btn" data-status="confirmed" onclick="filterByStatus('confirmed', this)">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#059669;"></span> Đã chốt đơn <span class="tab-count" id="tabCountConfirmed">0</span>
                        </button>
                        <button type="button" class="quote-tab-btn" data-status="completed" onclick="filterByStatus('completed', this)">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#16a34a;"></span> Hoàn tất <span class="tab-count" id="tabCountCompleted">0</span>
                        </button>
                        <button type="button" class="quote-tab-btn" data-status="cancelled" onclick="filterByStatus('cancelled', this)">
                            <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#dc2626;"></span> Đã hủy <span class="tab-count" id="tabCountCancelled">0</span>
                        </button>
                    </div>

                    <!-- Search & Action Toolbar -->
                    <div class="row align-items-center mb-4">
                        <div class="col-lg-5 col-md-6 mb-3 mb-md-0">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 10px 0 0 10px; border-color: #cbd5e1; padding: 0 14px;">
                                        <i class="fa fa-search text-muted"></i>
                                    </span>
                                </div>
                                <input type="text" id="quoteSearchInput" class="form-control border-left-0" style="height: 44px; border-radius: 0 10px 10px 0; border-color: #cbd5e1; font-size: 13.5px;" placeholder="Tìm theo Mã báo giá, Tên khách, SĐT, Công ty, Thiết bị..." oninput="debounceSearch()">
                            </div>
                        </div>

                        <div class="col-lg-7 col-md-6 d-flex justify-content-md-end align-items-center flex-wrap" style="gap: 12px;">
                            <select id="quoteSortSelect" class="form-control" style="width: auto; height: 44px; border-radius: 10px; border-color: #cbd5e1; font-size: 13px; font-weight: 500;" onchange="loadQuotes()">
                                <option value="latest">Mới nhất trước</option>
                                <option value="oldest">Cũ nhất trước</option>
                                <option value="amount_desc">Giá trị: Cao → Thấp</option>
                                <option value="amount_asc">Giá trị: Thấp → Cao</option>
                            </select>

                            <div class="btn-group" role="group">
                                <button type="button" id="btnViewFeed" class="btn btn-outline-secondary active" style="height: 44px; padding: 0 16px; font-size: 13px; font-weight: 600; border-radius: 10px 0 0 10px;" onclick="switchViewMode('feed')" title="Dạng Thẻ trực quan">
                                    <i class="fa fa-th-large mr-1"></i> Dạng Thẻ
                                </button>
                                <button type="button" id="btnViewTable" class="btn btn-outline-secondary" style="height: 44px; padding: 0 16px; font-size: 13px; font-weight: 600; border-radius: 0 10px 10px 0;" onclick="switchViewMode('table')" title="Dạng Bảng">
                                    <i class="fa fa-table mr-1"></i> Dạng Bảng
                                </button>
                            </div>

                            <button type="button" class="btn btn-outline-primary" style="height: 44px; border-radius: 10px; padding: 0 18px; font-weight: 600; font-size: 13px;" onclick="loadQuotes()" title="Tải lại dữ liệu">
                                <i class="fa fa-refresh mr-1"></i> Làm mới
                            </button>
                        </div>
                    </div>

                    <!-- Bulk Action Bar (Shows when 1+ selected) -->
                    <div id="bulkActionBar" class="bulk-action-bar d-none">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-check-square-o mr-2 text-warning" style="font-size: 18px;"></i>
                            <span style="font-size: 14px;">Đang chọn <strong id="selectedQuotesCount" class="text-warning">0</strong> bảng báo giá</span>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <button type="button" class="btn btn-danger btn-sm px-3" onclick="bulkDeleteQuotes()">
                                <i class="fa fa-trash mr-1"></i> Xóa các mục đã chọn
                            </button>
                            <button type="button" class="btn btn-outline-light btn-sm px-3" onclick="deselectAllQuotes()">
                                Bỏ chọn
                            </button>
                        </div>
                    </div>

                    <!-- Feed View (Cards) Container -->
                    <div id="quotesFeedContainer">
                        <!-- Render via JS -->
                    </div>

                    <!-- Table View Container -->
                    <div id="quotesTableContainer" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover border" style="border-radius: 10px; overflow: hidden;">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="40"><input type="checkbox" class="quote-custom-check" id="checkAllTable" onchange="toggleSelectAll(this)"></th>
                                        <th>Mã Báo Giá</th>
                                        <th>Khách Hàng & Công Ty</th>
                                        <th>Số Điện Thoại</th>
                                        <th>Thiết Bị Báo Giá</th>
                                        <th>Tổng Tiền</th>
                                        <th>Ngày Lập</th>
                                        <th>Trạng Thái</th>
                                        <th class="text-right" width="140">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody id="quotesTableBody">
                                    <!-- Render via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi Tiết Báo Giá -->
    <div class="modal fade" id="quoteDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header bg-dark text-white px-4 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge badge-light font-weight-bold text-dark mr-2" id="modalQuoteCode" style="font-size: 13px;">BG-000000</span>
                        <span class="text-white-50" style="font-size: 13px;">Chi tiết bảng báo giá</span>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" style="opacity: 0.8;"><span>&times;</span></button>
                </div>
                
                <div class="modal-body p-4" id="printableQuoteArea">
                    <!-- Top Customer & Status Details -->
                    <div class="row mb-4">
                        <div class="col-md-7">
                            <div class="p-3 bg-light rounded" style="border: 1px solid #e2e8f0;">
                                <h6 class="font-weight-bold text-dark mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                    <i class="fa fa-user mr-1 text-primary"></i> Thông tin khách hàng & Doanh nghiệp
                                </h6>
                                <div class="row">
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Họ và tên:</small>
                                        <strong class="text-dark" id="qCustomerName">---</strong>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Số điện thoại:</small>
                                        <strong class="text-primary" id="qCustomerPhone">---</strong>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Công ty / Tổ chức:</small>
                                        <span class="text-dark font-weight-bold" id="qCustomerCompany">---</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Email:</small>
                                        <span class="text-dark" id="qCustomerEmail">---</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Mã số thuế:</small>
                                        <span class="text-dark font-weight-bold" id="qCustomerTax">---</span>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <small class="text-muted d-block">Địa chỉ:</small>
                                        <span class="text-dark" id="qCustomerAddress">---</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 mt-3 mt-md-0">
                            <div class="p-3 bg-light rounded" style="border: 1px solid #e2e8f0;">
                                <h6 class="font-weight-bold text-dark mb-3 text-uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                                    <i class="fa fa-info-circle mr-1 text-info"></i> Thông tin hệ thống & Trạng thái
                                </h6>
                                <div class="row">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Ngày tạo:</small>
                                        <strong class="text-dark" id="qCreatedAt">---</strong>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Thiết bị truy cập:</small>
                                        <span class="badge badge-secondary" id="qDeviceType">Desktop</span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Địa chỉ IP:</small>
                                        <code id="qIpAddress">127.0.0.1</code>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted d-block">Thời gian onsite:</small>
                                        <span class="text-dark font-weight-bold" id="qDuration">---</span>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label class="small text-muted font-weight-bold mb-1">Cập nhật trạng thái xử lý:</label>
                                        <div class="d-flex" style="gap: 8px;">
                                            <select class="form-control form-control-sm" id="qStatusSelect" style="border-radius: 6px; font-weight: 600;">
                                                <option value="draft">Mới lập (Draft)</option>
                                                <option value="sent">Đã gửi báo giá cho khách</option>
                                                <option value="confirmed">Khách đã chốt đơn / Đặt cọc</option>
                                                <option value="completed">Đã hoàn tất đơn hàng</option>
                                                <option value="cancelled">Hủy báo giá</option>
                                            </select>
                                            <button type="button" class="btn btn-primary btn-sm px-3" onclick="saveQuoteStatus()">Lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h6 class="font-weight-bold text-dark mb-2 text-uppercase" style="font-size: 13px; letter-spacing: 0.5px;">
                        <i class="fa fa-list mr-1 text-primary"></i> Danh mục thiết bị trong bảng báo giá
                    </h6>
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" style="border-radius: 8px; overflow: hidden;">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40" class="text-center">STT</th>
                                    <th>Tên Thiết Bị / Model</th>
                                    <th width="120">Thương Hiệu</th>
                                    <th width="140" class="text-right">Đơn Giá</th>
                                    <th width="90" class="text-center">Số Lượng</th>
                                    <th width="150" class="text-right">Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody id="qItemsBody">
                                <!-- Render via JS -->
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold text-muted">Tổng tiền hàng:</td>
                                    <td class="text-right font-weight-bold text-dark" id="qSubtotal">0 đ</td>
                                </tr>
                                <tr id="rowDiscount" class="d-none">
                                    <td colspan="5" class="text-right text-danger font-weight-bold">Chiết khấu (<span id="qDiscountPercent">0</span>%):</td>
                                    <td class="text-right text-danger font-weight-bold" id="qDiscountAmount">-0 đ</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold text-muted">Thuế VAT (<span id="qVatPercent">0</span>%):</td>
                                    <td class="text-right font-weight-bold text-dark" id="qVatAmount">+0 đ</td>
                                </tr>
                                <tr style="background: #f1f5f9;">
                                    <td colspan="5" class="text-right font-weight-bold text-dark" style="font-size: 15px;">TỔNG CỘNG BÁO GIÁ:</td>
                                    <td class="text-right font-weight-bold text-primary" style="font-size: 18px;" id="qTotalAmount">0 đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Internal Notes -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-dark small mb-1">Ghi chú nội bộ của tư vấn viên / NVKD:</label>
                        <textarea class="form-control" id="qNotesInput" rows="2" placeholder="Ghi chú về tiến độ trao đổi, yêu cầu kỹ thuật đặc biệt, thời hạn giao hàng..." style="border-radius: 8px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success btn-sm" onclick="printQuoteDetail()">
                        <i class="fa fa-print mr-1"></i> In / Xuất PDF
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveQuoteStatus()">Lưu Thay Đổi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let quotesData = [];
        let currentFilterStatus = 'all';
        let currentSearchQuery = '';
        let currentViewMode = 'feed';
        let selectedQuoteIds = new Set();
        let searchTimeout = null;
        let activeQuoteId = null;

        $(document).ready(function() {
            loadQuotes();
        });

        function formatMoney(amount) {
            let num = Math.round(Number(amount) || 0);
            return new Intl.NumberFormat('vi-VN').format(num) + ' đ';
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentSearchQuery = $('#quoteSearchInput').val().trim();
                loadQuotes();
            }, 300);
        }

        function filterByStatus(status, btnElem) {
            currentFilterStatus = status;
            $('.quote-tab-btn').removeClass('active');
            $(btnElem).addClass('active');
            loadQuotes();
        }

        function switchViewMode(mode) {
            currentViewMode = mode;
            if (mode === 'feed') {
                $('#btnViewFeed').addClass('active');
                $('#btnViewTable').removeClass('active');
                $('#quotesFeedContainer').removeClass('d-none');
                $('#quotesTableContainer').addClass('d-none');
            } else {
                $('#btnViewTable').addClass('active');
                $('#btnViewFeed').removeClass('active');
                $('#quotesTableContainer').removeClass('d-none');
                $('#quotesFeedContainer').addClass('d-none');
            }
            renderQuotes();
        }

        function loadQuotes() {
            const sort = $('#quoteSortSelect').val();
            
            $.ajax({
                url: "{{ route('admin.api.quotes.list') }}",
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: currentFilterStatus,
                    keyword: currentSearchQuery,
                    sort: sort
                },
                success: function(res) {
                    quotesData = res.data || [];
                    
                    // Update stats
                    if (res.stats) {
                        $('#statTotal').text(res.stats.total || 0);
                        $('#statDraft').text(res.stats.draft || 0);
                        $('#statConfirmed').text((res.stats.confirmed || 0) + (res.stats.completed || 0));
                        $('#statTotalAmount').text(formatMoney(res.stats.total_amount || 0));

                        // Tab counts
                        $('#tabCountAll').text(res.stats.total || 0);
                        $('#tabCountDraft').text(res.stats.draft || 0);
                        $('#tabCountSent').text(res.stats.sent || 0);
                        $('#tabCountConfirmed').text(res.stats.confirmed || 0);
                        $('#tabCountCompleted').text(res.stats.completed || 0);
                        $('#tabCountCancelled').text(res.stats.cancelled || 0);
                    }

                    selectedQuoteIds.clear();
                    updateBulkActionBar();
                    renderQuotes();
                },
                error: function(xhr) {
                    console.error('Lỗi khi tải danh sách báo giá:', xhr.status, xhr.responseText);
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Lỗi khi tải danh sách báo giá từ server');
                    }
                }
            });
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'draft':
                    return `<span class="badge-quote badge-quote-draft"><i class="fa fa-pencil-square-o"></i> Mới lập</span>`;
                case 'sent':
                    return `<span class="badge-quote badge-quote-sent"><i class="fa fa-paper-plane-o"></i> Đã gửi khách</span>`;
                case 'confirmed':
                    return `<span class="badge-quote badge-quote-confirmed"><i class="fa fa-check-circle"></i> Đã chốt đơn</span>`;
                case 'completed':
                    return `<span class="badge-quote badge-quote-completed"><i class="fa fa-handshake-o"></i> Hoàn tất</span>`;
                case 'cancelled':
                    return `<span class="badge-quote badge-quote-cancelled"><i class="fa fa-times-circle"></i> Đã hủy</span>`;
                default:
                    return `<span class="badge-quote badge-quote-draft">${status}</span>`;
            }
        }

        function copyQuoteCode(code, event) {
            if (event) event.stopPropagation();
            navigator.clipboard.writeText(code).then(function() {
                if (typeof toastr !== 'undefined') {
                    toastr.success('Đã copy mã báo giá: #' + code);
                }
            });
        }

        function renderQuotes() {
            if (quotesData.length === 0) {
                const emptyHtml = `
                    <div class="quote-empty-state">
                        <i class="fa fa-folder-open-o"></i>
                        <h6 class="font-weight-bold text-dark mb-1" style="font-size: 16px;">Không tìm thấy báo giá nào</h6>
                        <p class="small text-muted mb-0">Thử thay đổi bộ lọc trạng thái hoặc từ khóa tìm kiếm</p>
                    </div>
                `;
                $('#quotesFeedContainer').html(emptyHtml);
                $('#quotesTableBody').html(`<tr><td colspan="9" class="text-center py-4 text-muted">Không có bản ghi báo giá nào</td></tr>`);
                return;
            }

            // Render Feed View
            let feedHtml = '';
            // Render Table View
            let tableHtml = '';

            quotesData.forEach(function(q) {
                const isSelected = selectedQuoteIds.has(q.id);
                const initial = (q.customer_name || 'K').trim().charAt(0).toUpperCase();
                const createdAtStr = new Date(q.created_at).toLocaleString('vi-VN', {
                    day: '2-digit', month: '2-digit', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                // Items summary list
                let itemsSummaryHtml = '';
                if (q.items && q.items.length > 0) {
                    q.items.slice(0, 3).forEach(function(item) {
                        itemsSummaryHtml += `
                            <div class="quote-items-preview-item">
                                <span class="quote-item-name" title="${item.product_name}">• ${item.product_name}</span>
                                <span class="quote-item-qty">x ${item.quantity} ${item.unit || 'Cái'}</span>
                            </div>
                        `;
                    });
                    if (q.items.length > 3) {
                        itemsSummaryHtml += `<div class="text-muted small mt-2 font-weight-bold" style="font-size: 11.5px;">+ ${q.items.length - 3} thiết bị khác...</div>`;
                    }
                } else {
                    itemsSummaryHtml = `<span class="text-muted small">Chưa có danh mục thiết bị</span>`;
                }

                // Feed Card HTML
                feedHtml += `
                    <div class="quote-card ${isSelected ? 'selected' : ''}" id="quote-card-${q.id}">
                        <div class="quote-card-header">
                            <div class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                                <input type="checkbox" class="quote-custom-check quote-item-check" value="${q.id}" ${isSelected ? 'checked' : ''} onchange="toggleQuoteSelection(${q.id}, this)">
                                <span class="quote-code-tag" onclick="copyQuoteCode('${q.quote_code}', event)" title="Nhấn để copy mã báo giá">
                                    <i class="fa fa-hashtag" style="font-size: 11px;"></i>${q.quote_code}
                                    <i class="fa fa-copy ml-1" style="font-size: 11px; opacity: 0.7;"></i>
                                </span>
                                ${getStatusBadge(q.status)}
                            </div>
                            <div class="d-flex align-items-center flex-wrap" style="gap: 16px;">
                                <span class="text-muted" style="font-size: 13px;"><i class="fa fa-clock-o mr-1"></i>${createdAtStr}</span>
                                <select class="quick-status-select" onchange="quickUpdateStatus(${q.id}, this.value)" title="Đổi trạng thái nhanh">
                                    <option value="draft" ${q.status === 'draft' ? 'selected' : ''}>⏳ Mới lập</option>
                                    <option value="sent" ${q.status === 'sent' ? 'selected' : ''}>📨 Đã gửi khách</option>
                                    <option value="confirmed" ${q.status === 'confirmed' ? 'selected' : ''}>✅ Đã chốt đơn</option>
                                    <option value="completed" ${q.status === 'completed' ? 'selected' : ''}>🤝 Hoàn tất</option>
                                    <option value="cancelled" ${q.status === 'cancelled' ? 'selected' : ''}>❌ Đã hủy</option>
                                </select>
                            </div>
                        </div>

                        <div class="quote-card-body">
                            <div class="row align-items-center" style="row-gap: 16px;">
                                <!-- Customer Info -->
                                <div class="col-lg-4 col-md-5">
                                    <div class="d-flex align-items-start">
                                        <div class="customer-avatar mr-3">${initial}</div>
                                        <div>
                                            <div class="customer-title">${q.customer_name || 'Khách vãng lai'}</div>
                                            ${q.customer_company ? `<div class="customer-company"><i class="fa fa-building-o text-muted"></i> ${q.customer_company}</div>` : ''}
                                            <div class="d-flex align-items-center flex-wrap" style="gap: 8px; margin-top: 6px;">
                                                ${q.customer_phone ? `<a href="tel:${q.customer_phone}" class="customer-phone-link"><i class="fa fa-phone"></i> ${q.customer_phone}</a>` : '<span class="text-muted small">Chưa có SĐT</span>'}
                                                ${q.customer_email ? `<a href="mailto:${q.customer_email}" class="customer-email-link"><i class="fa fa-envelope-o"></i> ${q.customer_email}</a>` : ''}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Devices Preview -->
                                <div class="col-lg-5 col-md-4">
                                    <div class="quote-items-preview">
                                        <div class="quote-items-preview-header">
                                            <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Thiết bị báo giá</span>
                                            <span class="badge badge-light text-dark font-weight-bold" style="font-size: 11px; border: 1px solid #e2e8f0;">${q.items_count || (q.items ? q.items.length : 0)} món</span>
                                        </div>
                                        ${itemsSummaryHtml}
                                    </div>
                                </div>

                                <!-- Total & Actions -->
                                <div class="col-lg-3 col-md-3 text-lg-right">
                                    <div class="quote-amount-label">Tổng thanh toán</div>
                                    <div class="quote-amount-highlight">${formatMoney(q.total_amount)}</div>
                                    <div class="d-flex justify-content-lg-end align-items-center" style="gap: 8px;">
                                        <button type="button" class="btn btn-primary btn-sm px-3 font-weight-bold" style="border-radius: 8px;" onclick="viewQuoteDetail(${q.id})">
                                            <i class="fa fa-eye mr-1"></i> Xem chi tiết
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm px-2" style="border-radius: 8px;" onclick="deleteQuote(${q.id})" title="Xóa báo giá">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Table Row HTML
                tableHtml += `
                    <tr class="${isSelected ? 'table-active' : ''}">
                        <td><input type="checkbox" class="quote-custom-check quote-item-check" value="${q.id}" ${isSelected ? 'checked' : ''} onchange="toggleQuoteSelection(${q.id}, this)"></td>
                        <td>
                            <span class="quote-code-tag" onclick="copyQuoteCode('${q.quote_code}', event)">#${q.quote_code}</span>
                        </td>
                        <td>
                            <strong class="text-dark">${q.customer_name || 'Khách vãng lai'}</strong>
                            ${q.customer_company ? `<br><small class="text-muted"><i class="fa fa-building-o mr-1"></i>${q.customer_company}</small>` : ''}
                        </td>
                        <td>
                            ${q.customer_phone ? `<a href="tel:${q.customer_phone}" class="customer-phone-link"><i class="fa fa-phone"></i> ${q.customer_phone}</a>` : '---'}
                        </td>
                        <td>
                            <span class="badge badge-light font-weight-bold" style="border: 1px solid #e2e8f0;">${q.items_count || (q.items ? q.items.length : 0)} món</span>
                        </td>
                        <td>
                            <strong class="text-primary font-weight-bold" style="font-size: 14.5px;">${formatMoney(q.total_amount)}</strong>
                        </td>
                        <td>
                            <small class="text-muted">${createdAtStr}</small>
                        </td>
                        <td>
                            ${getStatusBadge(q.status)}
                        </td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-primary mr-1" onclick="viewQuoteDetail(${q.id})" title="Xem chi tiết"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-xs btn-danger" onclick="deleteQuote(${q.id})" title="Xóa"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
            });

            $('#quotesFeedContainer').html(feedHtml);
            $('#quotesTableBody').html(tableHtml);
        }

        function toggleQuoteSelection(id, checkbox) {
            if (checkbox.checked) {
                selectedQuoteIds.add(id);
                $(`#quote-card-${id}`).addClass('selected');
            } else {
                selectedQuoteIds.delete(id);
                $(`#quote-card-${id}`).removeClass('selected');
            }
            updateBulkActionBar();
        }

        function toggleSelectAll(masterCheckbox) {
            const isChecked = masterCheckbox.checked;
            quotesData.forEach(function(q) {
                if (isChecked) {
                    selectedQuoteIds.add(q.id);
                    $(`#quote-card-${q.id}`).addClass('selected');
                } else {
                    selectedQuoteIds.delete(q.id);
                    $(`#quote-card-${q.id}`).removeClass('selected');
                }
            });
            $('.quote-item-check').prop('checked', isChecked);
            updateBulkActionBar();
        }

        function deselectAllQuotes() {
            selectedQuoteIds.clear();
            $('.quote-item-check').prop('checked', false);
            $('#checkAllTable').prop('checked', false);
            $('.quote-card').removeClass('selected');
            updateBulkActionBar();
        }

        function updateBulkActionBar() {
            const count = selectedQuoteIds.size;
            $('#selectedQuotesCount').text(count);
            if (count > 0) {
                $('#bulkActionBar').removeClass('d-none');
            } else {
                $('#bulkActionBar').addClass('d-none');
            }
        }

        function quickUpdateStatus(id, newStatus) {
            $.ajax({
                url: `{{ url('admin/api/quotes') }}/${id}/status`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success('Đã cập nhật trạng thái báo giá');
                        const item = quotesData.find(q => q.id === id);
                        if (item) item.status = newStatus;
                        loadQuotes();
                    } else {
                        toastr.error(res.message || 'Lỗi cập nhật');
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra khi cập nhật trạng thái');
                }
            });
        }

        function viewQuoteDetail(id) {
            activeQuoteId = id;
            $.ajax({
                url: `{{ url('admin/api/quotes') }}/${id}`,
                type: 'GET',
                success: function(res) {
                    const q = res.data;
                    if (!q) return;

                    $('#modalQuoteCode').text(`#${q.quote_code}`);
                    $('#qCustomerName').text(q.customer_name || 'Khách vãng lai');
                    $('#qCustomerPhone').text(q.customer_phone || '---');
                    $('#qCustomerEmail').text(q.customer_email || '---');
                    $('#qCustomerCompany').text(q.customer_company || '---');
                    $('#qCustomerTax').text(q.customer_tax_code || '---');
                    $('#qCustomerAddress').text(q.customer_address || '---');

                    const createdAt = new Date(q.created_at).toLocaleString('vi-VN');
                    $('#qCreatedAt').text(createdAt);
                    $('#qIpAddress').text(q.ip_address || '127.0.0.1');
                    $('#qDeviceType').text((q.device_type || 'Desktop').toUpperCase());
                    $('#qDuration').text(q.duration_seconds ? `${q.duration_seconds} giây` : '---');

                    $('#qStatusSelect').val(q.status || 'draft');
                    $('#qNotesInput').val(q.notes || '');

                    // Render Items Table
                    let itemsHtml = '';
                    if (q.items && q.items.length > 0) {
                        q.items.forEach(function(item, idx) {
                            itemsHtml += `
                                <tr>
                                    <td class="text-center font-weight-bold text-muted">${idx + 1}</td>
                                    <td>
                                        <strong class="text-dark">${item.product_name}</strong>
                                        ${item.product_sku ? `<div class="text-muted small">SKU: ${item.product_sku}</div>` : ''}
                                    </td>
                                    <td><span class="badge badge-light" style="border: 1px solid #e2e8f0;">${item.brand_name || 'Chính hãng'}</span></td>
                                    <td class="text-right">${formatMoney(item.unit_price)}</td>
                                    <td class="text-center font-weight-bold">${item.quantity} ${item.unit || 'Cái'}</td>
                                    <td class="text-right font-weight-bold text-dark">${formatMoney(item.total_price)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        itemsHtml = `<tr><td colspan="6" class="text-center py-3 text-muted">Không có thiết bị trong báo giá</td></tr>`;
                    }
                    $('#qItemsBody').html(itemsHtml);

                    // Calculation breakdown
                    $('#qSubtotal').text(formatMoney(q.subtotal || q.total_amount));
                    if (q.discount_percent && q.discount_percent > 0) {
                        $('#rowDiscount').removeClass('d-none');
                        $('#qDiscountPercent').text(q.discount_percent);
                        const discountVal = (q.subtotal || 0) * (q.discount_percent / 100);
                        $('#qDiscountAmount').text('-' + formatMoney(discountVal));
                    } else {
                        $('#rowDiscount').addClass('d-none');
                    }

                    $('#qVatPercent').text(q.vat_percent || 0);
                    const subAfterDiscount = (q.subtotal || q.total_amount) - ((q.subtotal || 0) * ((q.discount_percent || 0) / 100));
                    const vatVal = subAfterDiscount * ((q.vat_percent || 0) / 100);
                    $('#qVatAmount').text('+' + formatMoney(vatVal));
                    $('#qTotalAmount').text(formatMoney(q.total_amount));

                    $('#quoteDetailModal').modal('show');
                },
                error: function() {
                    toastr.error('Không thể tải chi tiết báo giá');
                }
            });
        }

        function saveQuoteStatus() {
            if (!activeQuoteId) return;
            const newStatus = $('#qStatusSelect').val();
            const notes = $('#qNotesInput').val();

            $.ajax({
                url: `{{ url('admin/api/quotes') }}/${activeQuoteId}/status`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus,
                    notes: notes
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success('Cập nhật trạng thái và ghi chú thành công!');
                        $('#quoteDetailModal').modal('hide');
                        loadQuotes();
                    } else {
                        toastr.error(res.message || 'Lỗi cập nhật');
                    }
                },
                error: function() {
                    toastr.error('Có lỗi xảy ra khi lưu trạng thái');
                }
            });
        }

        function printQuoteDetail() {
            const printContent = document.getElementById('printableQuoteArea').innerHTML;
            const printWindow = window.open('', '_blank', 'width=900,height=700');
            printWindow.document.write(`
                <html>
                <head>
                    <title>In Báo Giá - Misutech</title>
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
                    <style>
                        body { padding: 30px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
                        .table-bordered th, .table-bordered td { border: 1px solid #dee2e6 !important; }
                        @media print {
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="mb-4 text-center">
                        <h3 class="font-weight-bold text-dark">BẢNG BÁO GIÁ THIẾT BỊ TỰ ĐỘNG HÓA</h3>
                        <p class="text-muted">CÔNG TY TNHH KỸ THUẬT & THIẾT BỊ CÔNG NGHIỆP MISUTECH</p>
                    </div>
                    ${printContent}
                    <script>
                        window.onload = function() { window.print(); }
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        function deleteQuote(id) {
            swal({
                title: "Xóa báo giá này?",
                text: "Bạn có chắc chắn muốn xóa bản ghi báo giá này khỏi hệ thống?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                confirmButtonText: "Đồng ý xóa",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/api/quotes') }}/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                loadQuotes();
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function() {
                            toastr.error('Có lỗi xảy ra khi xóa báo giá');
                        }
                    });
                }
            });
        }

        function bulkDeleteQuotes() {
            const ids = Array.from(selectedQuoteIds);
            if (ids.length === 0) return;

            swal({
                title: `Xóa ${ids.length} báo giá đã chọn?`,
                text: "Hành động này sẽ xóa toàn bộ các bản ghi báo giá được chọn!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#dc2626",
                confirmButtonText: "Xóa tất cả",
                cancelButtonText: "Hủy"
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.api.quotes.bulk.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            ids: ids
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                selectedQuoteIds.clear();
                                loadQuotes();
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function() {
                            toastr.error('Có lỗi xảy ra khi xóa hàng loạt');
                        }
                    });
                }
            });
        }
    </script>
@endsection
