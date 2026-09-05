@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           CONTACTS MANAGEMENT - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .contact-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .contact-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .contact-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .contact-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .contact-stat-box .stat-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }
        .contact-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .contact-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Status Filter Tabs */
        .contact-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .contact-tab-btn {
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
        .contact-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .contact-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .contact-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .contact-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Status Badges - Clean Elegant Pastel */
        .badge-contact {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .badge-contact-pending {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-contact-contacted {
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
        }
        .badge-contact-completed {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-contact-cancelled {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        /* Contact Card Items (Feed View) */
        .contact-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .contact-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }
        .contact-card.selected {
            border-color: #003b70;
            background: #f8fafc;
        }
        .contact-card-header {
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
        .contact-code-tag {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-weight: 800;
            color: #003b70;
            background: #e0f2fe;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #bae6fd;
            transition: all 0.15s;
        }
        .contact-code-tag:hover {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
        }
        .contact-card-body {
            padding: 22px 24px;
        }
        .customer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #eff6ff;
            color: #003b70;
            font-weight: 800;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }
        .customer-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
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

        /* Message Box in Card */
        .contact-subject-highlight {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .contact-message-preview {
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 9px;
            padding: 12px 16px;
            font-size: 13.5px;
            color: #334155;
            line-height: 1.55;
            max-height: 90px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        .contact-telemetry-badge {
            font-size: 11.5px;
            color: #64748b;
            background: #f1f5f9;
            padding: 3px 9px;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            margin-right: 6px;
        }

        /* Quick Status Select */
        .quick-status-select {
            font-size: 13px;
            font-weight: 600;
            height: 36px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            padding: 4px 12px;
            color: #1e293b;
            cursor: pointer;
            transition: border-color 0.15s;
        }
        .quick-status-select:focus {
            border-color: #003b70;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 59, 112, 0.15);
        }

        /* Empty state */
        .contact-empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
        }
        .contact-empty-state i {
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
        .contact-custom-check {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #003b70;
        }

        /* Modal styling */
        .contact-modal-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px 18px;
            margin-bottom: 16px;
        }
        .contact-modal-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .contact-modal-val {
            font-size: 14.5px;
            font-weight: 600;
            color: #0f172a;
        }
        .contact-modal-msgbox {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px;
            font-size: 14px;
            color: #1e293b;
            line-height: 1.65;
            white-space: pre-wrap;
            min-height: 120px;
        }

        /* Table view styling */
        .contact-table {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .contact-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        .contact-table td {
            vertical-align: middle;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .contact-table tr:hover td {
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark mb-1">Quản Lý Yêu Cầu Liên Hệ & Tư Vấn</h4>
                <span class="text-muted" style="font-size: 13.5px;">Theo dõi, phản hồi và giải quyết các câu hỏi, yêu cầu tư vấn kỹ thuật từ khách hàng</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Liên hệ</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Metric Cards (Spacious, Clean, Minimalist) -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="contact-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Liên Hệ</span>
                    <div class="stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                        <i class="fa fa-comments-o"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ tin nhắn & yêu cầu</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="contact-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Chờ Xử Lý / Mới</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-bell-o"></i>
                    </div>
                </div>
                <div class="stat-value text-dark" id="statPending">0</div>
                <p class="stat-desc">Khách mới gửi cần phản hồi</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="contact-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đã Liên Hệ Phản Hồi</span>
                    <div class="stat-icon-wrap" style="background: #eff6ff; color: #0284c7;">
                        <i class="fa fa-phone"></i>
                    </div>
                </div>
                <div class="stat-value text-primary" id="statContacted">0</div>
                <p class="stat-desc">Đã gọi hoặc gửi email trao đổi</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="contact-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đã Giải Quyết</span>
                    <div class="stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                </div>
                <div class="stat-value text-success" id="statCompleted">0</div>
                <p class="stat-desc">Hoàn tất tư vấn & giải đáp</p>
            </div>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="contact-status-tabs">
        <button type="button" class="contact-tab-btn active" data-status="all" onclick="filterByStatus('all')">
            <span>Tất cả liên hệ</span>
            <span class="tab-count" id="tabCountAll">0</span>
        </button>
        <button type="button" class="contact-tab-btn" data-status="pending" onclick="filterByStatus('pending')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #d97706;"></span>
            <span>Chờ xử lý (Mới)</span>
            <span class="tab-count" id="tabCountPending">0</span>
        </button>
        <button type="button" class="contact-tab-btn" data-status="contacted" onclick="filterByStatus('contacted')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #0284c7;"></span>
            <span>Đã liên hệ</span>
            <span class="tab-count" id="tabCountContacted">0</span>
        </button>
        <button type="button" class="contact-tab-btn" data-status="completed" onclick="filterByStatus('completed')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #16a34a;"></span>
            <span>Đã giải quyết</span>
            <span class="tab-count" id="tabCountCompleted">0</span>
        </button>
        <button type="button" class="contact-tab-btn" data-status="cancelled" onclick="filterByStatus('cancelled')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #64748b;"></span>
            <span>Hủy / Spam</span>
            <span class="tab-count" id="tabCountCancelled">0</span>
        </button>
    </div>

    <!-- Toolbar: Search, Sorting, View Toggle & Refresh -->
    <div class="card mb-4" style="border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="searchInput" class="form-control border-left-0" 
                               placeholder="Tìm theo tên khách, số điện thoại, email, tiêu đề..." 
                               style="border-color: #cbd5e1; border-radius: 0 8px 8px 0; font-size: 13.5px;"
                               oninput="handleSearch(this.value)">
                    </div>
                </div>
                <div class="col-md-7 d-flex justify-content-md-end align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center mr-3">
                        <label class="mb-0 text-muted mr-2 font-weight-bold" style="font-size: 13px; white-space: nowrap;">Sắp xếp:</label>
                        <select id="sortSelect" class="form-control form-control-sm" style="width: 150px; border-radius: 8px; border-color: #cbd5e1;" onchange="handleSortChange(this.value)">
                            <option value="latest">Mới nhất trước</option>
                            <option value="oldest">Cũ nhất trước</option>
                        </select>
                    </div>

                    <!-- View Switcher -->
                    <div class="btn-group mr-3" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewFeed" onclick="switchView('feed')" title="Giao diện dạng Thẻ (Feed View)">
                            <i class="fa fa-th-large"></i> Dạng Thẻ
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewTable" onclick="switchView('table')" title="Giao diện dạng Bảng (Table View)">
                            <i class="fa fa-table"></i> Dạng Bảng
                        </button>
                    </div>

                    <!-- Refresh Button -->
                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 8px; padding: 6px 14px;" onclick="loadContacts()" title="Làm mới dữ liệu">
                        <i class="fa fa-refresh mr-1" id="refreshIcon"></i> Làm mới
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Action Bar -->
    <div id="bulkActionBar" class="bulk-action-bar" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fa fa-check-square-o mr-2" style="font-size: 18px;"></i>
            <span>Đã chọn <strong id="selectedCountText" class="text-warning">0</strong> liên hệ</span>
        </div>
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-light mr-2 font-weight-bold" onclick="deselectAll()">Bỏ chọn tất cả</button>
            <button type="button" class="btn btn-sm btn-danger font-weight-bold" onclick="bulkDeleteContacts()">
                <i class="fa fa-trash mr-1"></i> Xóa mục đã chọn
            </button>
        </div>
    </div>

    <!-- Feed Cards Container -->
    <div id="feedViewContainer">
        <!-- Contact Cards will be injected via JS -->
    </div>

    <!-- Table View Container -->
    <div id="tableViewContainer" style="display: none;">
        <div class="contact-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">
                            <input type="checkbox" class="contact-custom-check" id="checkAllTable" onclick="toggleSelectAll(this)">
                        </th>
                        <th>Khách Hàng</th>
                        <th>Số Điện Thoại</th>
                        <th>Email</th>
                        <th>Tiêu Đề & Lời Nhắn</th>
                        <th>Thời Gian</th>
                        <th>Trạng Thái</th>
                        <th class="text-right" style="width: 140px;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Table rows via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Chi Tiết Liên Hệ -->
    <div class="modal fade" id="contactDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <div class="modal-header py-3 px-4" style="background: #003b70; color: #ffffff;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa fa-envelope-open-o"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Chi Tiết Tin Nhắn Liên Hệ</h5>
                            <span class="text-white-50" style="font-size: 12px;" id="modalSubtitle">Mã liên hệ: #CT-0000</span>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <!-- Left Info Column -->
                        <div class="col-md-5">
                            <div class="contact-modal-section">
                                <div class="contact-modal-label">Thông tin khách hàng</div>
                                <div class="d-flex align-items-center mb-3 mt-2">
                                    <div class="customer-avatar mr-3" id="modalAvatar">N</div>
                                    <div>
                                        <div class="customer-name mb-0" id="modalCustomerName">---</div>
                                        <small class="text-muted" id="modalCreatedAt">---</small>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="contact-modal-label">Điện thoại liên hệ</div>
                                    <div class="mt-1">
                                        <a href="#" id="modalPhoneLink" class="customer-phone-link">
                                            <i class="fa fa-phone"></i> <span id="modalPhone">---</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="contact-modal-label">Địa chỉ Email</div>
                                    <div class="mt-1">
                                        <a href="#" id="modalEmailLink" class="customer-email-link text-truncate" style="max-width: 100%;">
                                            <i class="fa fa-envelope-o"></i> <span id="modalEmail">---</span>
                                        </a>
                                    </div>
                                </div>
                                <div>
                                    <div class="contact-modal-label">Thông số kỹ thuật & Nguồn</div>
                                    <div class="mt-1" id="modalTelemetry">
                                        <!-- IP, device, duration -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Content Column -->
                        <div class="col-md-7">
                            <div class="mb-3">
                                <div class="contact-modal-label">Chủ đề / Yêu cầu tư vấn</div>
                                <div class="font-weight-bold text-dark mt-1" style="font-size: 16px;" id="modalSubject">---</div>
                            </div>

                            <div class="mb-3">
                                <div class="contact-modal-label">Nội dung tin nhắn</div>
                                <div class="contact-modal-msgbox mt-1" id="modalMessage">---</div>
                            </div>

                            <!-- Update Status Box -->
                            <div class="p-3" style="background: #f1f5f9; border-radius: 9px; border: 1px solid #cbd5e1;">
                                <label class="font-weight-bold text-dark mb-2" style="font-size: 13px;">
                                    <i class="fa fa-exchange mr-1"></i> Cập nhật trạng thái xử lý:
                                </label>
                                <div class="d-flex gap-2">
                                    <select id="modalStatusSelect" class="form-control mr-2" style="border-radius: 8px; font-weight: 600;">
                                        <option value="pending">Chờ xử lý (Mới)</option>
                                        <option value="contacted">Đã liên hệ phản hồi</option>
                                        <option value="completed">Đã giải quyết xong</option>
                                        <option value="cancelled">Hủy / Đánh dấu Spam</option>
                                    </select>
                                    <button type="button" class="btn btn-primary px-3 font-weight-bold" style="border-radius: 8px; white-space: nowrap;" onclick="saveModalStatus()">
                                        Lưu thay đổi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 bg-light d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteCurrentModalContact()">
                            <i class="fa fa-trash mr-1"></i> Xóa tin nhắn này
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Đóng</button>
                        <a href="#" id="btnModalDirectCall" class="btn btn-success btn-sm font-weight-bold">
                            <i class="fa fa-phone mr-1"></i> Gọi ngay
                        </a>
                        <a href="#" id="btnModalDirectEmail" class="btn btn-info btn-sm font-weight-bold">
                            <i class="fa fa-envelope mr-1"></i> Gửi Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // ═════════════════════════════════════════════════════════════════════
        // STATE MANAGEMENT
        // ═════════════════════════════════════════════════════════════════════
        let contactsData = [];
        let filteredContacts = [];
        let currentStatusFilter = 'all';
        let currentKeyword = '';
        let currentSort = 'latest';
        let currentView = 'feed'; // 'feed' | 'table'
        let selectedContactIds = new Set();
        let searchTimeout = null;
        let activeModalContactId = null;

        const csrfToken = '{{ csrf_token() }}';

        // ═════════════════════════════════════════════════════════════════════
        // INITIALIZATION
        // ═════════════════════════════════════════════════════════════════════
        $(document).ready(function() {
            loadContacts();
        });

        // ═════════════════════════════════════════════════════════════════════
        // LOAD DATA VIA AJAX (POST / GET compatible)
        // ═════════════════════════════════════════════════════════════════════
        function loadContacts() {
            const icon = $('#refreshIcon');
            icon.addClass('fa-spin');

            $.ajax({
                url: '{{ route("admin.api.contacts.list") }}',
                type: 'POST',
                data: {
                    keyword: currentKeyword,
                    status: currentStatusFilter,
                    sort: currentSort,
                    _token: csrfToken
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    contactsData = res.data || [];
                    updateStats(res.stats || {});
                    applyClientFilterAndRender();
                },
                error: function(xhr) {
                    console.error('Lỗi tải danh sách liên hệ:', xhr);
                    toastr.error('Không thể tải danh sách liên hệ. Vui lòng thử lại!', 'Lỗi');
                },
                complete: function() {
                    setTimeout(() => icon.removeClass('fa-spin'), 300);
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // UPDATE METRICS & TAB COUNTS
        // ═════════════════════════════════════════════════════════════════════
        function updateStats(stats) {
            $('#statTotal').text(stats.total || 0);
            $('#statPending').text(stats.pending || 0);
            $('#statContacted').text(stats.contacted || 0);
            $('#statCompleted').text(stats.completed || 0);

            $('#tabCountAll').text(stats.total || 0);
            $('#tabCountPending').text(stats.pending || 0);
            $('#tabCountContacted').text(stats.contacted || 0);
            $('#tabCountCompleted').text(stats.completed || 0);
            $('#tabCountCancelled').text(stats.cancelled || 0);
        }

        // ═════════════════════════════════════════════════════════════════════
        // FILTERING, SEARCHING & SORTING
        // ═════════════════════════════════════════════════════════════════════
        function filterByStatus(status) {
            currentStatusFilter = status;
            $('.contact-tab-btn').removeClass('active');
            $(`.contact-tab-btn[data-status="${status}"]`).addClass('active');
            applyClientFilterAndRender();
        }

        function handleSearch(val) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentKeyword = val.trim().toLowerCase();
                applyClientFilterAndRender();
            }, 250);
        }

        function handleSortChange(val) {
            currentSort = val;
            applyClientFilterAndRender();
        }

        function switchView(view) {
            currentView = view;
            if (view === 'feed') {
                $('#btnViewFeed').addClass('active');
                $('#btnViewTable').removeClass('active');
                $('#feedViewContainer').show();
                $('#tableViewContainer').hide();
            } else {
                $('#btnViewTable').addClass('active');
                $('#btnViewFeed').removeClass('active');
                $('#feedViewContainer').hide();
                $('#tableViewContainer').show();
            }
            renderView();
        }

        function applyClientFilterAndRender() {
            filteredContacts = contactsData.filter(item => {
                // Status check
                if (currentStatusFilter !== 'all' && item.status !== currentStatusFilter) {
                    return false;
                }
                // Keyword check
                if (currentKeyword) {
                    const name = (item.name || '').toLowerCase();
                    const phone = (item.phone || '').toLowerCase();
                    const email = (item.email || '').toLowerCase();
                    const subject = (item.subject || '').toLowerCase();
                    const message = (item.message || '').toLowerCase();
                    if (!name.includes(currentKeyword) && 
                        !phone.includes(currentKeyword) && 
                        !email.includes(currentKeyword) && 
                        !subject.includes(currentKeyword) && 
                        !message.includes(currentKeyword)) {
                        return false;
                    }
                }
                return true;
            });

            // Sorting
            filteredContacts.sort((a, b) => {
                const dateA = new Date(a.created_at || 0).getTime();
                const dateB = new Date(b.created_at || 0).getTime();
                return currentSort === 'oldest' ? dateA - dateB : dateB - dateA;
            });

            renderView();
        }

        // ═════════════════════════════════════════════════════════════════════
        // RENDER VIEWS
        // ═════════════════════════════════════════════════════════════════════
        function renderView() {
            updateBulkActionBar();

            if (currentView === 'feed') {
                renderFeedView();
            } else {
                renderTableView();
            }
        }

        // Helper: Status badge generator
        function getStatusBadge(status) {
            switch(status) {
                case 'pending':
                    return `<span class="badge-contact badge-contact-pending"><i class="fa fa-clock-o"></i> Chờ xử lý</span>`;
                case 'contacted':
                    return `<span class="badge-contact badge-contact-contacted"><i class="fa fa-phone"></i> Đã liên hệ</span>`;
                case 'completed':
                    return `<span class="badge-contact badge-contact-completed"><i class="fa fa-check-circle"></i> Đã giải quyết</span>`;
                case 'cancelled':
                    return `<span class="badge-contact badge-contact-cancelled"><i class="fa fa-ban"></i> Đã hủy / Spam</span>`;
                default:
                    return `<span class="badge-contact badge-contact-pending">${status}</span>`;
            }
        }

        // Helper: Format Date
        function formatDate(dateStr) {
            if (!dateStr) return '---';
            const d = new Date(dateStr);
            const pad = (n) => (n < 10 ? '0' + n : n);
            return `${pad(d.getHours())}:${pad(d.getMinutes())} - ${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()}`;
        }

        // Helper: Customer Initial
        function getInitial(name) {
            if (!name) return 'K';
            const parts = name.trim().split(' ');
            return parts[parts.length - 1].charAt(0).toUpperCase() || 'K';
        }

        // Render Feed (Card) View
        function renderFeedView() {
            const container = $('#feedViewContainer');
            container.empty();

            if (filteredContacts.length === 0) {
                container.html(`
                    <div class="contact-empty-state">
                        <i class="fa fa-inbox"></i>
                        <h5 class="font-weight-bold text-dark">Không tìm thấy yêu cầu liên hệ nào</h5>
                        <p class="text-muted mb-0">Thử thay đổi bộ lọc trạng thái hoặc từ khóa tìm kiếm</p>
                    </div>
                `);
                return;
            }

            filteredContacts.forEach(item => {
                const isSelected = selectedContactIds.has(item.id);
                const code = `#CT-${String(item.id).padStart(4, '0')}`;
                const initial = getInitial(item.name);
                const formattedTime = formatDate(item.created_at);

                const cardHtml = `
                    <div class="contact-card ${isSelected ? 'selected' : ''}" id="contact-card-${item.id}">
                        <div class="contact-card-header">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <input type="checkbox" class="contact-custom-check mr-2" 
                                       onchange="toggleSelectContact(${item.id}, this)" 
                                       ${isSelected ? 'checked' : ''}>
                                <span class="contact-code-tag mr-2" onclick="showContactModal(${item.id})" title="Nhấn để xem chi tiết">
                                    <i class="fa fa-hashtag"></i> ${code}
                                </span>
                                ${getStatusBadge(item.status)}
                                <span class="text-muted ml-2" style="font-size: 12.5px;">
                                    <i class="fa fa-clock-o mr-1"></i> ${formattedTime}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                ${item.phone ? `
                                    <a href="tel:${item.phone}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;" title="Gọi điện cho khách">
                                        <i class="fa fa-phone"></i>
                                    </a>
                                ` : ''}
                                ${item.email ? `
                                    <a href="mailto:${item.email}" class="btn btn-sm btn-outline-info" style="border-radius: 6px;" title="Gửi email cho khách">
                                        <i class="fa fa-envelope-o"></i>
                                    </a>
                                ` : ''}
                                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px;" onclick="deleteContact(${item.id})" title="Xóa liên hệ">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="contact-card-body">
                            <div class="row align-items-center">
                                <!-- Khách hàng -->
                                <div class="col-lg-4 col-md-5 mb-3 mb-md-0">
                                    <div class="d-flex align-items-start">
                                        <div class="customer-avatar mr-3">${initial}</div>
                                        <div>
                                            <div class="customer-name">${item.name || 'Khách vãng lai'}</div>
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                ${item.phone ? `
                                                    <a href="tel:${item.phone}" class="customer-phone-link">
                                                        <i class="fa fa-phone"></i> ${item.phone}
                                                    </a>
                                                ` : '<span class="text-muted font-italic" style="font-size: 12px;">Không có SĐT</span>'}
                                                ${item.email ? `
                                                    <a href="mailto:${item.email}" class="customer-email-link" title="${item.email}">
                                                        <i class="fa fa-envelope-o"></i> ${item.email}
                                                    </a>
                                                ` : ''}
                                            </div>
                                            ${item.ip_address ? `
                                                <div class="contact-telemetry-badge">
                                                    <i class="fa fa-globe"></i> IP: ${item.ip_address}
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tiêu đề & Nội dung -->
                                <div class="col-lg-5 col-md-4 mb-3 mb-md-0">
                                    <div class="contact-subject-highlight">
                                        <i class="fa fa-commenting-o text-primary"></i>
                                        <span class="text-truncate" title="${item.subject || ''}">${item.subject || '(Không có tiêu đề)'}</span>
                                    </div>
                                    <div class="contact-message-preview" title="Nhấn để xem toàn bộ tin nhắn" onclick="showContactModal(${item.id})" style="cursor: pointer;">
                                        ${item.message || '<em class="text-muted">Không có nội dung lời nhắn.</em>'}
                                    </div>
                                </div>

                                <!-- Quick Status & Action -->
                                <div class="col-lg-3 col-md-3 text-md-right d-flex flex-column align-items-md-end justify-content-center">
                                    <div class="mb-2 w-100" style="max-width: 200px;">
                                        <select class="quick-status-select w-100" onchange="quickUpdateStatus(${item.id}, this.value)">
                                            <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                                            <option value="contacted" ${item.status === 'contacted' ? 'selected' : ''}>Đã liên hệ</option>
                                            <option value="completed" ${item.status === 'completed' ? 'selected' : ''}>Đã giải quyết</option>
                                            <option value="cancelled" ${item.status === 'cancelled' ? 'selected' : ''}>Hủy / Spam</option>
                                        </select>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary font-weight-bold w-100" style="max-width: 200px; border-radius: 8px; padding: 7px 12px;" onclick="showContactModal(${item.id})">
                                        <i class="fa fa-eye mr-1"></i> Xem chi tiết
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.append(cardHtml);
            });
        }

        // Render Table View
        function renderTableView() {
            const tbody = $('#tableBody');
            tbody.empty();

            if (filteredContacts.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa fa-inbox mb-2" style="font-size: 32px;"></i>
                            <div class="font-weight-bold">Không tìm thấy yêu cầu liên hệ nào</div>
                        </td>
                    </tr>
                `);
                return;
            }

            const allSelected = filteredContacts.length > 0 && filteredContacts.every(item => selectedContactIds.has(item.id));
            $('#checkAllTable').prop('checked', allSelected);

            filteredContacts.forEach(item => {
                const isSelected = selectedContactIds.has(item.id);
                const code = `#CT-${String(item.id).padStart(4, '0')}`;
                const formattedTime = formatDate(item.created_at);

                const rowHtml = `
                    <tr class="${isSelected ? 'table-active' : ''}">
                        <td class="text-center">
                            <input type="checkbox" class="contact-custom-check" 
                                   onchange="toggleSelectContact(${item.id}, this)" 
                                   ${isSelected ? 'checked' : ''}>
                        </td>
                        <td>
                            <div class="font-weight-bold text-dark">${item.name || 'Khách vãng lai'}</div>
                            <small class="text-primary font-weight-bold">${code}</small>
                        </td>
                        <td>
                            ${item.phone ? `
                                <a href="tel:${item.phone}" class="customer-phone-link">
                                    <i class="fa fa-phone"></i> ${item.phone}
                                </a>
                            ` : '<span class="text-muted">---</span>'}
                        </td>
                        <td>
                            ${item.email ? `
                                <a href="mailto:${item.email}" class="text-muted font-weight-500" title="${item.email}">
                                    ${item.email}
                                </a>
                            ` : '<span class="text-muted">---</span>'}
                        </td>
                        <td style="max-width: 260px;">
                            <div class="font-weight-bold text-dark text-truncate" title="${item.subject || ''}">${item.subject || '(Không tiêu đề)'}</div>
                            <div class="text-muted text-truncate" style="font-size: 12.5px;" title="${item.message || ''}">${item.message || ''}</div>
                        </td>
                        <td>
                            <span class="text-muted" style="font-size: 12.5px;">${formattedTime}</span>
                        </td>
                        <td>
                            <select class="quick-status-select" style="height: 30px; font-size: 12px; padding: 2px 8px;" onchange="quickUpdateStatus(${item.id}, this.value)">
                                <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>Chờ xử lý</option>
                                <option value="contacted" ${item.status === 'contacted' ? 'selected' : ''}>Đã liên hệ</option>
                                <option value="completed" ${item.status === 'completed' ? 'selected' : ''}>Đã giải quyết</option>
                                <option value="cancelled" ${item.status === 'cancelled' ? 'selected' : ''}>Hủy / Spam</option>
                            </select>
                        </td>
                        <td class="text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-1" style="border-radius: 6px; padding: 4px 8px;" onclick="showContactModal(${item.id})" title="Xem chi tiết">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 8px;" onclick="deleteContact(${item.id})" title="Xóa liên hệ">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // SELECTION & BULK ACTIONS
        // ═════════════════════════════════════════════════════════════════════
        function toggleSelectContact(id, checkbox) {
            if (checkbox.checked) {
                selectedContactIds.add(id);
            } else {
                selectedContactIds.delete(id);
            }
            updateBulkActionBar();
            $(`#contact-card-${id}`).toggleClass('selected', checkbox.checked);
        }

        function toggleSelectAll(masterCheckbox) {
            const checked = masterCheckbox.checked;
            filteredContacts.forEach(item => {
                if (checked) {
                    selectedContactIds.add(item.id);
                } else {
                    selectedContactIds.delete(item.id);
                }
            });
            renderView();
        }

        function deselectAll() {
            selectedContactIds.clear();
            renderView();
        }

        function updateBulkActionBar() {
            const count = selectedContactIds.size;
            $('#selectedCountText').text(count);
            if (count > 0) {
                $('#bulkActionBar').slideDown(150);
            } else {
                $('#bulkActionBar').slideUp(150);
            }
        }

        // ═════════════════════════════════════════════════════════════════════
        // STATUS UPDATE ACTIONS
        // ═════════════════════════════════════════════════════════════════════
        function quickUpdateStatus(id, newStatus) {
            $.ajax({
                url: `/admin/api/contacts/${id}/status`,
                type: 'PUT',
                data: {
                    status: newStatus,
                    _token: csrfToken
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    toastr.success('Cập nhật trạng thái thành công!', 'Thông báo');
                    // Cập nhật mảng local
                    const item = contactsData.find(c => c.id === id);
                    if (item) {
                        item.status = newStatus;
                    }
                    // Tải lại thống kê
                    loadContacts();
                },
                error: function(xhr) {
                    console.error('Lỗi cập nhật trạng thái:', xhr);
                    toastr.error('Không thể cập nhật trạng thái. Vui lòng thử lại!', 'Lỗi');
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // MODAL DETAIL & ACTIONS
        // ═════════════════════════════════════════════════════════════════════
        function showContactModal(id) {
            activeModalContactId = id;
            const item = contactsData.find(c => c.id === id);
            if (!item) return;

            const code = `#CT-${String(item.id).padStart(4, '0')}`;
            $('#modalSubtitle').text(`Mã liên hệ: ${code}`);
            $('#modalAvatar').text(getInitial(item.name));
            $('#modalCustomerName').text(item.name || 'Khách vãng lai');
            $('#modalCreatedAt').text(formatDate(item.created_at));

            // Phone
            if (item.phone) {
                $('#modalPhone').text(item.phone);
                $('#modalPhoneLink').attr('href', `tel:${item.phone}`).show();
                $('#btnModalDirectCall').attr('href', `tel:${item.phone}`).show();
            } else {
                $('#modalPhone').text('Không cung cấp');
                $('#modalPhoneLink').attr('href', '#');
                $('#btnModalDirectCall').hide();
            }

            // Email
            if (item.email) {
                $('#modalEmail').text(item.email);
                $('#modalEmailLink').attr('href', `mailto:${item.email}`).show();
                $('#btnModalDirectEmail').attr('href', `mailto:${item.email}`).show();
            } else {
                $('#modalEmail').text('Không cung cấp');
                $('#modalEmailLink').attr('href', '#');
                $('#btnModalDirectEmail').hide();
            }

            // Telemetry
            let telemetryHtml = '';
            if (item.ip_address) {
                telemetryHtml += `<div class="contact-telemetry-badge"><i class="fa fa-globe"></i> IP: ${item.ip_address}</div>`;
            }
            if (item.device_type) {
                telemetryHtml += `<div class="contact-telemetry-badge"><i class="fa fa-desktop"></i> Thiết bị: ${item.device_type}</div>`;
            }
            if (item.duration_seconds) {
                telemetryHtml += `<div class="contact-telemetry-badge"><i class="fa fa-clock-o"></i> Trên web: ${item.duration_seconds}s</div>`;
            }
            if (!telemetryHtml) {
                telemetryHtml = '<span class="text-muted" style="font-size: 12px;">Không có dữ liệu telemetry</span>';
            }
            $('#modalTelemetry').html(telemetryHtml);

            // Message & Subject
            $('#modalSubject').text(item.subject || '(Không có chủ đề)');
            $('#modalMessage').text(item.message || '(Không có nội dung lời nhắn)');

            // Status select
            $('#modalStatusSelect').val(item.status);

            $('#contactDetailModal').modal('show');
        }

        function saveModalStatus() {
            if (!activeModalContactId) return;
            const newStatus = $('#modalStatusSelect').val();
            quickUpdateStatus(activeModalContactId, newStatus);
            $('#contactDetailModal').modal('hide');
        }

        function deleteCurrentModalContact() {
            if (!activeModalContactId) return;
            $('#contactDetailModal').modal('hide');
            deleteContact(activeModalContactId);
        }

        // ═════════════════════════════════════════════════════════════════════
        // DELETE & BULK DELETE
        // ═════════════════════════════════════════════════════════════════════
        function deleteContact(id) {
            Swal.fire({
                title: 'Xóa yêu cầu liên hệ này?',
                text: 'Hành động này sẽ xóa vĩnh viễn tin nhắn liên hệ khỏi hệ thống.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/api/contacts/${id}`,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        dataType: 'json',
                        success: function() {
                            toastr.success('Đã xóa liên hệ thành công!', 'Thông báo');
                            selectedContactIds.delete(id);
                            loadContacts();
                        },
                        error: function(xhr) {
                            console.error('Lỗi khi xóa liên hệ:', xhr);
                            toastr.error('Không thể xóa liên hệ. Vui lòng thử lại!', 'Lỗi');
                        }
                    });
                }
            });
        }

        function bulkDeleteContacts() {
            const ids = Array.from(selectedContactIds);
            if (ids.length === 0) return;

            Swal.fire({
                title: `Xác nhận xóa ${ids.length} liên hệ?`,
                text: 'Tất cả các tin nhắn liên hệ đã chọn sẽ bị xóa vĩnh viễn.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: `Xóa ${ids.length} mục`,
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.api.contacts.bulk.destroy") }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                            _token: csrfToken
                        },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        dataType: 'json',
                        success: function(res) {
                            toastr.success(res.message || 'Đã xóa các liên hệ được chọn!', 'Thông báo');
                            selectedContactIds.clear();
                            loadContacts();
                        },
                        error: function(xhr) {
                            console.error('Lỗi xóa hàng loạt liên hệ:', xhr);
                            toastr.error('Không thể xóa danh sách liên hệ đã chọn!', 'Lỗi');
                        }
                    });
                }
            });
        }
    </script>
@endsection
