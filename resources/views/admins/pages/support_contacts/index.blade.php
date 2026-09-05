@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           SUPPORT CONTACTS MANAGEMENT - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .support-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .support-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .support-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .support-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .support-stat-box .stat-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }
        .support-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .support-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Department Filter Tabs */
        .support-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .support-tab-btn {
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
        .support-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .support-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .support-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .support-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Badges */
        .badge-dept {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .badge-dept-sale {
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
        }
        .badge-dept-technical {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        .badge-dept-warranty {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .badge-dept-other {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        /* Support Staff Cards (Grid View) */
        .support-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
            display: flex;
            flex-direction: column;
            height: calc(100% - 24px);
        }
        .support-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        .support-card-header {
            padding: 16px 20px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .support-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .support-card-footer {
            padding: 12px 20px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .staff-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        .staff-avatar-initial {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #eff6ff;
            color: #003b70;
            font-weight: 800;
            font-size: 19px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #dbeafe;
        }
        .staff-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 3px;
        }
        .staff-department {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .staff-note {
            font-size: 13px;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 8px;
            padding: 10px 14px;
            margin: 14px 0;
            line-height: 1.5;
            flex-grow: 1;
        }

        /* Action Phone / Zalo Buttons */
        .btn-call-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
            flex: 1;
        }
        .btn-call-pill:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
            text-decoration: none;
        }

        .btn-zalo-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s;
            flex: 1;
        }
        .btn-zalo-pill:hover {
            background: #16a34a;
            color: #ffffff;
            border-color: #16a34a;
            text-decoration: none;
        }

        /* Interactive status toggle pills */
        .toggle-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid transparent;
            user-select: none;
        }
        .toggle-pill.active {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }
        .toggle-pill.inactive {
            background: #f1f5f9;
            color: #64748b;
            border-color: #cbd5e1;
        }
        .toggle-pill:hover {
            opacity: 0.85;
            transform: scale(1.03);
        }

        /* Order Tag */
        .order-tag {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-weight: 700;
            font-size: 12px;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 2px 7px;
            border-radius: 5px;
        }

        /* Empty state */
        .support-empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            grid-column: 1 / -1;
        }
        .support-empty-state i {
            font-size: 44px;
            color: #94a3b8;
            margin-bottom: 14px;
            display: inline-block;
        }

        /* Table view styling */
        .support-table {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .support-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        .support-table td {
            vertical-align: middle;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .support-table tr:hover td {
            background: #f8fafc;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark mb-1">Quản Lý Hotline & Nhân Viên Tư Vấn</h4>
                <span class="text-muted" style="font-size: 13.5px;">Quản lý đội ngũ chuyên viên bán hàng, bảo hành kỹ thuật và danh sách popup liên hệ nhanh</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Support Contacts</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Metric Cards (Spacious, Clean, Minimalist) -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="support-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Đầu Mối</span>
                    <div class="stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ nhân sự & hotline</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="support-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Kinh Doanh / Báo Giá</span>
                    <div class="stat-icon-wrap" style="background: #eff6ff; color: #0284c7;">
                        <i class="fa fa-phone"></i>
                    </div>
                </div>
                <div class="stat-value text-primary" id="statSale">0</div>
                <p class="stat-desc">Tư vấn bán hàng & báo giá nhanh</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="support-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Kỹ Thuật & Bảo Hành</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-wrench"></i>
                    </div>
                </div>
                <div class="stat-value text-dark" id="statTechnical">0</div>
                <p class="stat-desc">Hỗ trợ cài đặt, lập trình & sửa chữa</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="support-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đang Hiện Popup</span>
                    <div class="stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa fa-bullhorn"></i>
                    </div>
                </div>
                <div class="stat-value text-success" id="statPopup">0</div>
                <p class="stat-desc">Hiển thị trên popup góc web</p>
            </div>
        </div>
    </div>

    <!-- Department Filter Tabs -->
    <div class="support-status-tabs">
        <button type="button" class="support-tab-btn active" data-dept="all" onclick="filterByDept('all')">
            <span>Tất cả đầu mối</span>
            <span class="tab-count" id="tabCountAll">0</span>
        </button>
        <button type="button" class="support-tab-btn" data-dept="sale" onclick="filterByDept('sale')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #0284c7;"></span>
            <span>Kinh Doanh / Báo Giá</span>
            <span class="tab-count" id="tabCountSale">0</span>
        </button>
        <button type="button" class="support-tab-btn" data-dept="technical" onclick="filterByDept('technical')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #d97706;"></span>
            <span>Tư Vấn Kỹ Thuật / Lập Trình</span>
            <span class="tab-count" id="tabCountTechnical">0</span>
        </button>
        <button type="button" class="support-tab-btn" data-dept="warranty" onclick="filterByDept('warranty')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #16a34a;"></span>
            <span>Dịch Vụ & Bảo Hành</span>
            <span class="tab-count" id="tabCountWarranty">0</span>
        </button>
        <button type="button" class="support-tab-btn" data-dept="other" onclick="filterByDept('other')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #64748b;"></span>
            <span>Vị trí khác</span>
            <span class="tab-count" id="tabCountOther">0</span>
        </button>
    </div>

    <!-- Toolbar: Search, Sorting, View Toggle & Add Button -->
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
                               placeholder="Tìm theo tên nhân viên, SĐT, Zalo, phòng ban, ghi chú..." 
                               style="border-color: #cbd5e1; border-radius: 0 8px 8px 0; font-size: 13.5px;"
                               oninput="handleSearch(this.value)">
                    </div>
                </div>
                <div class="col-md-7 d-flex justify-content-md-end align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center mr-3">
                        <label class="mb-0 text-muted mr-2 font-weight-bold" style="font-size: 13px; white-space: nowrap;">Sắp xếp:</label>
                        <select id="sortSelect" class="form-control form-control-sm" style="width: 170px; border-radius: 8px; border-color: #cbd5e1;" onchange="handleSortChange(this.value)">
                            <option value="sort_order">Thứ tự ưu tiên (Mặc định)</option>
                            <option value="latest">Mới tạo trước</option>
                            <option value="name">Tên nhân viên (A-Z)</option>
                        </select>
                    </div>

                    <!-- View Switcher -->
                    <div class="btn-group mr-3" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewGrid" onclick="switchView('grid')" title="Giao diện dạng Thẻ (Grid View)">
                            <i class="fa fa-th-large"></i> Dạng Thẻ
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewTable" onclick="switchView('table')" title="Giao diện dạng Bảng (Table View)">
                            <i class="fa fa-table"></i> Dạng Bảng
                        </button>
                    </div>

                    <!-- Refresh Button -->
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" style="border-radius: 8px; padding: 6px 14px;" onclick="loadContacts()" title="Làm mới">
                        <i class="fa fa-refresh mr-1" id="refreshIcon"></i> Làm mới
                    </button>

                    <!-- Add Contact Button -->
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px; padding: 6px 16px;" onclick="openModal()">
                        <i class="fa fa-plus mr-1"></i> + Thêm Nhân Viên Mới
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid View Container -->
    <div id="gridViewContainer" class="row">
        <!-- Cards injected via JS -->
    </div>

    <!-- Table View Container -->
    <div id="tableViewContainer" style="display: none;">
        <div class="support-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">Thứ tự</th>
                        <th>Chuyên Viên / Đầu Mối</th>
                        <th>Số Điện Thoại</th>
                        <th>Zalo Chat</th>
                        <th>Phòng Ban / Vị Trí</th>
                        <th>Nhóm</th>
                        <th class="text-center">Hiện Popup</th>
                        <th class="text-center">Trạng Thái</th>
                        <th class="text-right" style="width: 130px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Rows via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Thêm / Chỉnh Sửa Nhân Viên Tư Vấn -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <div class="modal-header py-3 px-4" style="background: #003b70; color: #ffffff;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Thêm Nhân Viên Tư Vấn Mới</h5>
                            <span class="text-white-50" style="font-size: 12px;">Thông tin hiển thị trên trang liên hệ và popup tư vấn nhanh</span>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form id="contactForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="contactId" name="id">
                    
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Tên chuyên viên / Đầu mối Hotline <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contactName" name="name" 
                                       placeholder="Ví dụ: Ms Mai Chi, Mr Hoàng Đức, Hotline Kỹ thuật..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Số điện thoại gọi (Call) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contactPhone" name="phone" 
                                       placeholder="Ví dụ: 0325194688" 
                                       style="border-radius: 8px; border-color: #cbd5e1;" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Số điện thoại Zalo <span class="text-muted font-weight-normal">(Để trống sẽ lấy số Call)</span>
                                </label>
                                <input type="text" class="form-control" id="contactZalo" name="zalo_phone" 
                                       placeholder="Ví dụ: 0325194688" 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Nhóm phân loại <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="contactDeptType" name="department_type" 
                                        style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;" 
                                        required onchange="onDeptTypeChange(this.value)">
                                    <option value="sale">Phòng Kinh Doanh / Báo Giá (Sale)</option>
                                    <option value="technical">Tư Vấn Kỹ Thuật / Lập Trình (Technical)</option>
                                    <option value="warranty">Dịch Vụ & Bảo Hành (Warranty)</option>
                                    <option value="other">Vị trí khác (Tùy chỉnh)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Tên phòng ban / Vị trí hiển thị <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="contactDept" name="department" 
                                       value="Phòng Kinh Doanh / Báo Giá" 
                                       style="border-radius: 8px; border-color: #cbd5e1;" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Mô tả vai trò / Khu vực hỗ trợ <span class="text-muted font-weight-normal">(Tùy chọn)</span>
                                </label>
                                <input type="text" class="form-control" id="contactNote" name="note" 
                                       placeholder="Ví dụ: Tư vấn bán hàng & báo giá nhanh 24/7..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Thứ tự ưu tiên (Nhỏ đứng trước)</label>
                                <input type="number" class="form-control" id="contactSortOrder" name="sort_order" 
                                       value="0" style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Hiện Popup nổi trên web</label>
                                <select class="form-control" id="contactShowPopup" name="show_in_popup" style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;">
                                    <option value="1" selected>✓ Có (Hiển thị popup)</option>
                                    <option value="0">✗ Không (Ẩn khỏi popup)</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Trạng thái hoạt động</label>
                                <select class="form-control" id="contactIsActive" name="is_active" style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;">
                                    <option value="1" selected>● Đang hoạt động</option>
                                    <option value="0">○ Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-0 mt-2">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Ảnh đại diện / Avatar (Tùy chọn)</label>
                            <input type="file" class="form-control-file" id="contactAvatar" name="avatar_file" accept="image/*" onchange="previewAvatar(this)">
                            <div id="avatarPreviewWrap" class="mt-2 align-items-center" style="display: none;">
                                <img id="avatarPreview" src="" class="staff-avatar mr-2" alt="Preview">
                                <small class="text-muted">Ảnh đang được chọn</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-3 px-4 bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold" id="btnSaveContact">
                            <i class="fa fa-check mr-1"></i> Lưu thông tin
                        </button>
                    </div>
                </form>
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
        let currentDeptFilter = 'all';
        let currentKeyword = '';
        let currentSort = 'sort_order';
        let currentView = 'grid'; // 'grid' | 'table'
        let searchTimeout = null;

        const csrfToken = '{{ csrf_token() }}';

        // ═════════════════════════════════════════════════════════════════════
        // INITIALIZATION
        // ═════════════════════════════════════════════════════════════════════
        $(document).ready(function() {
            loadContacts();

            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                saveContact();
            });
        });

        // ═════════════════════════════════════════════════════════════════════
        // LOAD DATA VIA AJAX (POST / GET compatible)
        // ═════════════════════════════════════════════════════════════════════
        function loadContacts() {
            const icon = $('#refreshIcon');
            icon.addClass('fa-spin');

            $.ajax({
                url: '{{ route("admin.api.support_contacts.list") }}',
                type: 'POST',
                data: {
                    keyword: currentKeyword,
                    department_type: currentDeptFilter,
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
                    console.error('Lỗi tải danh sách chuyên viên:', xhr);
                    toastr.error('Không thể tải danh sách chuyên viên hỗ trợ!', 'Lỗi');
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
            $('#statSale').text(stats.sale || 0);
            $('#statTechnical').text(stats.technical || 0);
            $('#statPopup').text(stats.popup || 0);

            $('#tabCountAll').text(stats.total || 0);
            $('#tabCountSale').text(stats.sale || 0);
            
            // Count for individual tabs from full dataset
            let technicalCount = 0;
            let warrantyCount = 0;
            let otherCount = 0;
            contactsData.forEach(item => {
                if (item.department_type === 'technical') technicalCount++;
                else if (item.department_type === 'warranty') warrantyCount++;
                else if (item.department_type === 'other') otherCount++;
            });

            $('#tabCountTechnical').text(technicalCount);
            $('#tabCountWarranty').text(warrantyCount);
            $('#tabCountOther').text(otherCount);
        }

        // ═════════════════════════════════════════════════════════════════════
        // FILTERING, SEARCHING & SORTING
        // ═════════════════════════════════════════════════════════════════════
        function filterByDept(dept) {
            currentDeptFilter = dept;
            $('.support-tab-btn').removeClass('active');
            $(`.support-tab-btn[data-dept="${dept}"]`).addClass('active');
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
            if (view === 'grid') {
                $('#btnViewGrid').addClass('active');
                $('#btnViewTable').removeClass('active');
                $('#gridViewContainer').show();
                $('#tableViewContainer').hide();
            } else {
                $('#btnViewTable').addClass('active');
                $('#btnViewGrid').removeClass('active');
                $('#gridViewContainer').hide();
                $('#tableViewContainer').show();
            }
            renderView();
        }

        function applyClientFilterAndRender() {
            filteredContacts = contactsData.filter(item => {
                // Department check
                if (currentDeptFilter !== 'all' && item.department_type !== currentDeptFilter) {
                    return false;
                }
                // Keyword check
                if (currentKeyword) {
                    const name = (item.name || '').toLowerCase();
                    const phone = (item.phone || '').toLowerCase();
                    const zalo = (item.zalo_phone || '').toLowerCase();
                    const dept = (item.department || '').toLowerCase();
                    const note = (item.note || '').toLowerCase();
                    if (!name.includes(currentKeyword) && 
                        !phone.includes(currentKeyword) && 
                        !zalo.includes(currentKeyword) && 
                        !dept.includes(currentKeyword) && 
                        !note.includes(currentKeyword)) {
                        return false;
                    }
                }
                return true;
            });

            // Sorting
            filteredContacts.sort((a, b) => {
                if (currentSort === 'latest') {
                    return new Date(b.created_at || 0) - new Date(a.created_at || 0);
                } else if (currentSort === 'name') {
                    return (a.name || '').localeCompare(b.name || '');
                } else {
                    return (a.sort_order || 0) - (b.sort_order || 0);
                }
            });

            renderView();
        }

        // ═════════════════════════════════════════════════════════════════════
        // RENDER VIEWS
        // ═════════════════════════════════════════════════════════════════════
        function renderView() {
            if (currentView === 'grid') {
                renderGridView();
            } else {
                renderTableView();
            }
        }

        // Helper: Dept badge generator
        function getDeptBadge(type) {
            switch(type) {
                case 'sale':
                    return `<span class="badge-dept badge-dept-sale"><i class="fa fa-shopping-cart"></i> Sale / Báo giá</span>`;
                case 'technical':
                    return `<span class="badge-dept badge-dept-technical"><i class="fa fa-code"></i> Kỹ thuật / Lập trình</span>`;
                case 'warranty':
                    return `<span class="badge-dept badge-dept-warranty"><i class="fa fa-shield"></i> Bảo hành / Dịch vụ</span>`;
                case 'other':
                default:
                    return `<span class="badge-dept badge-dept-other"><i class="fa fa-user"></i> Khác</span>`;
            }
        }

        // Helper: Customer Initial
        function getInitial(name) {
            if (!name) return 'S';
            const parts = name.trim().split(' ');
            return parts[parts.length - 1].charAt(0).toUpperCase() || 'S';
        }

        // Render Grid View
        function renderGridView() {
            const container = $('#gridViewContainer');
            container.empty();

            if (filteredContacts.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="support-empty-state">
                            <i class="fa fa-user-times"></i>
                            <h5 class="font-weight-bold text-dark">Không tìm thấy nhân viên / hotline nào</h5>
                            <p class="text-muted mb-0">Thử thay đổi bộ lọc phòng ban hoặc bấm Thêm Mới nhân viên</p>
                        </div>
                    </div>
                `);
                return;
            }

            filteredContacts.forEach(item => {
                const initial = getInitial(item.name);
                const avatarHtml = item.avatar 
                    ? `<img src="{{ asset('storage/clients/imgs/support') }}/${item.avatar}" class="staff-avatar mr-3" alt="${item.name}">`
                    : `<div class="staff-avatar-initial mr-3">${initial}</div>`;

                const popupPill = item.show_in_popup 
                    ? `<span class="toggle-pill active" onclick="toggleStatus(${item.id}, 'show_in_popup')" title="Nhấn để ẩn"><i class="fa fa-check-circle"></i> Hiện Popup</span>`
                    : `<span class="toggle-pill inactive" onclick="toggleStatus(${item.id}, 'show_in_popup')" title="Nhấn để hiện"><i class="fa fa-times-circle"></i> Ẩn Popup</span>`;

                const activePill = item.is_active 
                    ? `<span class="toggle-pill active" onclick="toggleStatus(${item.id}, 'is_active')" title="Nhấn để tạm tắt"><i class="fa fa-dot-circle-o"></i> Hoạt động</span>`
                    : `<span class="toggle-pill inactive" onclick="toggleStatus(${item.id}, 'is_active')" title="Nhấn để kích hoạt"><i class="fa fa-pause-circle"></i> Tạm dừng</span>`;

                const cardHtml = `
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="support-card" id="support-card-${item.id}">
                            <div class="support-card-header">
                                <div class="d-flex align-items-center">
                                    <span class="order-tag mr-2" title="Thứ tự hiển thị">#${item.sort_order || 0}</span>
                                    ${getDeptBadge(item.department_type)}
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 4px 9px;" onclick="editContact(${item.id})" title="Chỉnh sửa">
                                        <i class="fa fa-pencil"></i> Sửa
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 9px;" onclick="deleteContact(${item.id})" title="Xóa">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="support-card-body">
                                <div class="d-flex align-items-center">
                                    ${avatarHtml}
                                    <div class="overflow-hidden">
                                        <div class="staff-name text-truncate" title="${item.name}">${item.name}</div>
                                        <div class="staff-department text-truncate" title="${item.department}">${item.department}</div>
                                    </div>
                                </div>

                                <div class="staff-note">
                                    <i class="fa fa-quote-left text-muted mr-1" style="font-size: 11px;"></i>
                                    ${item.note || 'Tư vấn bán hàng & giải pháp thiết bị công nghiệp.'}
                                </div>

                                <div class="d-flex gap-2 mt-auto">
                                    <a href="tel:${item.phone}" class="btn-call-pill" title="Gọi điện cho ${item.name}">
                                        <i class="fa fa-phone"></i> ${item.phone}
                                    </a>
                                    <a href="https://zalo.me/${item.zalo_phone || item.phone}" target="_blank" class="btn-zalo-pill" title="Chat Zalo với ${item.name}">
                                        <i class="fa fa-commenting-o"></i> Zalo: ${item.zalo_phone || item.phone}
                                    </a>
                                </div>
                            </div>

                            <div class="support-card-footer">
                                <div>${popupPill}</div>
                                <div>${activePill}</div>
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
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa fa-user-times mb-2" style="font-size: 32px;"></i>
                            <div class="font-weight-bold">Không tìm thấy nhân viên / hotline nào</div>
                        </td>
                    </tr>
                `);
                return;
            }

            filteredContacts.forEach(item => {
                const initial = getInitial(item.name);
                const avatarSmall = item.avatar 
                    ? `<img src="{{ asset('storage/clients/imgs/support') }}/${item.avatar}" style="width: 32px; height: 32px; border-radius: 8px; object-fit: cover;" class="mr-2" alt="${item.name}">`
                    : `<div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #003b70; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; justify-content: center;" class="mr-2">${initial}</div>`;

                const popupPill = item.show_in_popup 
                    ? `<span class="toggle-pill active" onclick="toggleStatus(${item.id}, 'show_in_popup')"><i class="fa fa-check"></i> Hiện</span>`
                    : `<span class="toggle-pill inactive" onclick="toggleStatus(${item.id}, 'show_in_popup')"><i class="fa fa-times"></i> Ẩn</span>`;

                const activePill = item.is_active 
                    ? `<span class="toggle-pill active" onclick="toggleStatus(${item.id}, 'is_active')"><i class="fa fa-dot-circle-o"></i> Bật</span>`
                    : `<span class="toggle-pill inactive" onclick="toggleStatus(${item.id}, 'is_active')"><i class="fa fa-pause"></i> Tắt</span>`;

                const rowHtml = `
                    <tr>
                        <td class="text-center">
                            <span class="order-tag">#${item.sort_order || 0}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${avatarSmall}
                                <div>
                                    <div class="font-weight-bold text-dark">${item.name}</div>
                                    ${item.note ? `<small class="text-muted text-truncate d-inline-block" style="max-width: 200px;">${item.note}</small>` : ''}
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="tel:${item.phone}" class="text-primary font-weight-bold">
                                <i class="fa fa-phone mr-1"></i> ${item.phone}
                            </a>
                        </td>
                        <td>
                            <a href="https://zalo.me/${item.zalo_phone || item.phone}" target="_blank" class="text-success font-weight-bold">
                                <i class="fa fa-commenting-o mr-1"></i> ${item.zalo_phone || item.phone}
                            </a>
                        </td>
                        <td>
                            <span class="text-dark font-weight-500">${item.department}</span>
                        </td>
                        <td>
                            ${getDeptBadge(item.department_type)}
                        </td>
                        <td class="text-center">${popupPill}</td>
                        <td class="text-center">${activePill}</td>
                        <td class="text-right">
                            <button type="button" class="btn btn-sm btn-outline-primary mr-1" style="border-radius: 6px; padding: 4px 8px;" onclick="editContact(${item.id})" title="Chỉnh sửa">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 8px;" onclick="deleteContact(${item.id})" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // MODAL MANAGEMENT & SAVING
        // ═════════════════════════════════════════════════════════════════════
        function openModal() {
            $('#contactForm')[0].reset();
            $('#contactId').val('');
            $('#modalTitle').text('Thêm Nhân Viên Tư Vấn Mới');
            $('#contactDept').val('Phòng Kinh Doanh / Báo Giá');
            $('#contactDeptType').val('sale');
            $('#contactSortOrder').val('0');
            $('#contactShowPopup').val('1');
            $('#contactIsActive').val('1');
            $('#avatarPreviewWrap').hide();
            $('#contactModal').modal('show');
        }

        function onDeptTypeChange(val) {
            const deptInput = $('#contactDept');
            if (val === 'sale') {
                deptInput.val('Phòng Kinh Doanh / Báo Giá');
            } else if (val === 'technical') {
                deptInput.val('Tư Vấn Giải Pháp / Lập Trình');
            } else if (val === 'warranty') {
                deptInput.val('Dịch Vụ Kỹ Thuật / Bảo Hành');
            }
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#avatarPreview').attr('src', e.target.result);
                    $('#avatarPreviewWrap').css('display', 'flex');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function editContact(id) {
            $.ajax({
                url: `{{ url('admin/api/support-contacts') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data;
                    $('#contactId').val(data.id);
                    $('#contactName').val(data.name);
                    $('#contactPhone').val(data.phone);
                    $('#contactZalo').val(data.zalo_phone);
                    $('#contactDeptType').val(data.department_type);
                    $('#contactDept').val(data.department);
                    $('#contactNote').val(data.note);
                    $('#contactSortOrder').val(data.sort_order);
                    $('#contactShowPopup').val(data.show_in_popup ? '1' : '0');
                    $('#contactIsActive').val(data.is_active ? '1' : '0');

                    if (data.avatar) {
                        $('#avatarPreview').attr('src', `{{ asset('storage/clients/imgs/support') }}/${data.avatar}`);
                        $('#avatarPreviewWrap').css('display', 'flex');
                    } else {
                        $('#avatarPreviewWrap').hide();
                    }

                    $('#modalTitle').text('Chỉnh Sửa Thông Tin Nhân Viên');
                    $('#contactModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Lỗi khi lấy thông tin nhân viên:', xhr);
                    toastr.error('Không thể lấy thông tin nhân viên tư vấn!', 'Lỗi');
                }
            });
        }

        function saveContact() {
            const formData = new FormData($('#contactForm')[0]);
            $('#btnSaveContact').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Đang lưu...');

            $.ajax({
                url: "{{ route('admin.api.support_contacts.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                dataType: 'json',
                success: function(response) {
                    $('#btnSaveContact').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
                    $('#contactModal').modal('hide');
                    toastr.success(response.message || 'Lưu thông tin thành công!', 'Thông báo');
                    loadContacts();
                },
                error: function(xhr) {
                    $('#btnSaveContact').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng kiểm tra lại!', 'Lỗi');
                    }
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // TOGGLE STATUS ACTIONS
        // ═════════════════════════════════════════════════════════════════════
        function toggleStatus(id, field) {
            $.ajax({
                url: `{{ url('admin/api/support-contacts/toggle-status') }}/${id}`,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    field: field
                },
                headers: { 'X-CSRF-TOKEN': csrfToken },
                dataType: 'json',
                success: function(response) {
                    toastr.success(response.message || 'Cập nhật trạng thái thành công!', 'Thông báo');
                    loadContacts();
                },
                error: function() {
                    toastr.error('Lỗi cập nhật trạng thái!', 'Lỗi');
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // DELETE CONTACT
        // ═════════════════════════════════════════════════════════════════════
        function deleteContact(id) {
            Swal.fire({
                title: 'Xóa nhân viên / hotline này?',
                text: "Hành động này sẽ xóa vĩnh viễn đầu mối liên hệ khỏi hệ thống.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/api/support-contacts') }}/${id}`,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        dataType: 'json',
                        success: function(response) {
                            toastr.success(response.message || 'Đã xóa thành công!', 'Thông báo');
                            loadContacts();
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa nhân viên tư vấn!', 'Lỗi');
                        }
                    });
                }
            });
        }
    </script>
@endsection
