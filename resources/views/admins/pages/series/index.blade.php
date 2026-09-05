@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           SERIES MANAGEMENT - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .series-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .series-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .series-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .series-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .series-stat-box .stat-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }
        .series-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .series-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Filter Tabs */
        .series-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .series-tab-btn {
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
        .series-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .series-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .series-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .series-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Series Cards (Grid View) */
        .series-card {
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
        .series-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        .series-card-header {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .series-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .series-card-footer {
            padding: 12px 18px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .series-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .series-slug {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 10px;
            max-width: 100%;
        }

        /* Badges */
        .badge-brand-pill {
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-cat-pill {
            background: #f8fafc;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            max-width: 100%;
        }
        .badge-status-pill {
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-status-active {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .badge-status-draft {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }

        /* Metric pill for models */
        .metric-models-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
        }

        /* ID Tag */
        .id-tag {
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
        .series-empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            grid-column: 1 / -1;
        }
        .series-empty-state i {
            font-size: 44px;
            color: #94a3b8;
            margin-bottom: 14px;
            display: inline-block;
        }

        /* Table view styling */
        .series-table {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .series-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        .series-table td {
            vertical-align: middle;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .series-table tr:hover td {
            background: #f8fafc;
        }

        /* Modern Pagination */
        .modern-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            padding: 16px 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
        }
        .page-btn {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
            padding: 6px 13px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }
        .page-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .page-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
        }
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark mb-1">Quản Lý Dòng Sản Phẩm (Series)</h4>
                <span class="text-muted" style="font-size: 13.5px;">Nhóm các model sản phẩm độc lập có cùng dòng/series lại với nhau (Biến tần, PLC, HMI, Cảm biến...)</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dòng sản phẩm</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Metric Cards (Spacious, Clean, Minimalist) -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="series-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Dòng Series</span>
                    <div class="stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                        <i class="fa fa-tags"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ dòng sản phẩm</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="series-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đang Hoạt Động</span>
                    <div class="stat-icon-wrap" style="background: #ecfdf5; color: #059669;">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                </div>
                <div class="stat-value text-success" id="statActive">0</div>
                <p class="stat-desc">Đang hiển thị trên website</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="series-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đã Gán Model SP</span>
                    <div class="stat-icon-wrap" style="background: #eff6ff; color: #0284c7;">
                        <i class="fa fa-cubes"></i>
                    </div>
                </div>
                <div class="stat-value text-primary" id="statWithProducts">0</div>
                <p class="stat-desc">Series đã có mã sản phẩm</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="series-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Hãng Sản Xuất Phủ Sóng</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-building-o"></i>
                    </div>
                </div>
                <div class="stat-value text-dark" id="statTotalBrands">0</div>
                <p class="stat-desc">Thương hiệu có dòng series</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="series-status-tabs">
        <button type="button" class="series-tab-btn active" data-tab="all" onclick="switchFilterTab('all')">
            <span>Tất cả Series</span>
            <span class="tab-count" id="tabCountAll">0</span>
        </button>
        <button type="button" class="series-tab-btn" data-tab="active" onclick="switchFilterTab('active')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #059669;"></span>
            <span>Đang hoạt động</span>
            <span class="tab-count" id="tabCountActive">0</span>
        </button>
        <button type="button" class="series-tab-btn" data-tab="has_products" onclick="switchFilterTab('has_products')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #0284c7;"></span>
            <span>Đã gán Model</span>
            <span class="tab-count" id="tabCountWithProducts">0</span>
        </button>
        <button type="button" class="series-tab-btn" data-tab="no_products" onclick="switchFilterTab('no_products')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #d97706;"></span>
            <span>Chưa gán Model</span>
            <span class="tab-count" id="tabCountNoProducts">0</span>
        </button>
        <button type="button" class="series-tab-btn" data-tab="draft" onclick="switchFilterTab('draft')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #64748b;"></span>
            <span>Tạm ẩn / Bản nháp</span>
            <span class="tab-count" id="tabCountDraft">0</span>
        </button>
    </div>

    <!-- Toolbar: Search, Filters, Sorting, View Toggle & Add Button -->
    <div class="card mb-4" style="border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0" style="border-color: #cbd5e1; border-radius: 8px 0 0 8px;">
                                <i class="fa fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" id="searchInput" class="form-control border-left-0" 
                               placeholder="Tìm theo tên series, slug..." 
                               style="border-color: #cbd5e1; border-radius: 0 8px 8px 0; font-size: 13px;"
                               oninput="handleSearch(this.value)">
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 mb-2 mb-lg-0">
                    <select id="brandFilterSelect" class="form-control form-control-sm" style="border-radius: 8px; border-color: #cbd5e1;" onchange="handleBrandChange(this.value)">
                        <option value="all">-- Tất cả hãng --</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3 col-md-3 mb-2 mb-lg-0">
                    <select id="categoryFilterSelect" class="form-control form-control-sm" style="border-radius: 8px; border-color: #cbd5e1;" onchange="handleCategoryChange(this.value)">
                        <option value="all">-- Tất cả danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-4 col-md-12 d-flex justify-content-lg-end align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center mr-2">
                        <select id="sortSelect" class="form-control form-control-sm" style="width: 140px; border-radius: 8px; border-color: #cbd5e1;" onchange="handleSortChange(this.value)">
                            <option value="latest">Mới tạo trước</option>
                            <option value="sort_order">Thứ tự ưu tiên</option>
                            <option value="name_asc">Tên (A-Z)</option>
                            <option value="products_desc">Nhiều model nhất</option>
                            <option value="oldest">Cũ nhất trước</option>
                        </select>
                    </div>

                    <!-- View Switcher -->
                    <div class="btn-group mr-2" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewGrid" onclick="switchView('grid')" title="Giao diện dạng Thẻ (Grid View)">
                            <i class="fa fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewTable" onclick="switchView('table')" title="Giao diện dạng Bảng (Table View)">
                            <i class="fa fa-table"></i>
                        </button>
                    </div>

                    <!-- Refresh Button -->
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" style="border-radius: 8px; padding: 6px 12px;" onclick="loadSeries()" title="Làm mới">
                        <i class="fa fa-refresh" id="refreshIcon"></i>
                    </button>

                    <!-- Add Series Button -->
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px; padding: 6px 14px; white-space: nowrap;" onclick="openModal()">
                        <i class="fa fa-plus mr-1"></i> + Thêm Series
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
        <div class="series-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th>Tên Dòng Series</th>
                        <th>Đường Dẫn (Slug)</th>
                        <th>Thương Hiệu</th>
                        <th>Danh Mục</th>
                        <th class="text-center" style="width: 120px;">Số Model</th>
                        <th class="text-center" style="width: 80px;">Thứ Tự</th>
                        <th class="text-center" style="width: 120px;">Trạng Thái</th>
                        <th class="text-right" style="width: 130px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Rows via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination Bar -->
    <div id="paginationContainer" class="modern-pagination" style="display: none;">
        <div class="text-muted" style="font-size: 13px;" id="paginationInfo">
            Hiển thị 0 đến 0 trong 0 dòng sản phẩm
        </div>
        <div class="d-flex align-items-center gap-2">
            <select id="perPageSelect" class="form-control form-control-sm mr-2" style="width: 100px; border-radius: 8px;" onchange="handlePerPageChange(this.value)">
                <option value="12">12 / trang</option>
                <option value="24" selected>24 / trang</option>
                <option value="48">48 / trang</option>
                <option value="100">100 / trang</option>
            </select>
            <div id="paginationButtons" class="d-flex gap-1">
                <!-- Pagination Buttons via JS -->
            </div>
        </div>
    </div>

    <!-- Modal Thêm / Chỉnh Sửa Dòng Sản Phẩm -->
    <div class="modal fade" id="seriesModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <div class="modal-header py-3 px-4" style="background: #003b70; color: #ffffff;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa fa-tags"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Thêm Dòng Sản Phẩm Mới</h5>
                            <span class="text-white-50" style="font-size: 12px;">Nhóm các model thiết bị có cùng dòng/series</span>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form id="seriesForm">
                    @csrf
                    <input type="hidden" id="seriesId" name="id">
                    
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Tên Dòng Series <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="seriesName" name="name" 
                                       placeholder="Ví dụ: ABB MS116 Series, Omron E3Z Series..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;" required>
                            </div>
                            <div class="col-md-5 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Đường dẫn tĩnh (Slug) <span class="text-muted font-weight-normal">(Tự tạo theo tên)</span>
                                </label>
                                <input type="text" class="form-control" id="seriesSlug" name="slug" 
                                       placeholder="Ví dụ: abb-ms116-series..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Thương Hiệu / Hãng sản xuất
                                </label>
                                <select class="form-control" id="seriesBrandId" name="brand_id" style="border-radius: 8px; border-color: #cbd5e1;">
                                    <option value="">-- Chọn thương hiệu --</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Danh Mục Sản Phẩm
                                </label>
                                <select class="form-control" id="seriesCategoryId" name="category_id" style="border-radius: 8px; border-color: #cbd5e1;">
                                    <option value="">-- Chọn danh mục --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Thứ tự ưu tiên (Nhỏ đứng trước)</label>
                                <input type="number" class="form-control" id="seriesSortOrder" name="sort_order" 
                                       value="0" style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Trạng thái hiển thị</label>
                                <select class="form-control" id="seriesStatus" name="status" style="border-radius: 8px; border-color: #cbd5e1; font-weight: 600;">
                                    <option value="active" selected>● Đang hoạt động (Hiển thị)</option>
                                    <option value="draft">○ Tạm ẩn / Bản nháp</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Mô tả tóm tắt dòng sản phẩm</label>
                            <textarea class="form-control" id="seriesDescription" name="description" rows="3" 
                                      placeholder="Mô tả đặc điểm nổi bật, dải công suất, tính năng chính của series..." 
                                      style="border-radius: 8px; border-color: #cbd5e1;"></textarea>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Nội dung chi tiết / Thông số chung</label>
                            <textarea class="form-control" id="seriesContent" name="content" rows="4" 
                                      placeholder="Thông số kỹ thuật chung, sơ đồ chân, bảng mã model..." 
                                      style="border-radius: 8px; border-color: #cbd5e1;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Tiêu đề SEO (Meta Title)</label>
                                <input type="text" class="form-control" id="metaTitle" name="meta_title" 
                                       placeholder="Tiêu đề hiển thị Google..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Mô tả SEO (Meta Description)</label>
                                <input type="text" class="form-control" id="metaDescription" name="meta_description" 
                                       placeholder="Mô tả ngắn gọn cho công cụ tìm kiếm..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-3 px-4 bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold" id="btnSaveSeries">
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
        let currentTab = 'all';
        let currentKeyword = '';
        let currentBrandId = 'all';
        let currentCategoryId = 'all';
        let currentSort = 'latest';
        let currentPage = 1;
        let currentPerPage = 24;
        let currentView = 'grid'; // 'grid' | 'table'
        let searchTimeout = null;

        const csrfToken = '{{ csrf_token() }}';

        // ═════════════════════════════════════════════════════════════════════
        // INITIALIZATION
        // ═════════════════════════════════════════════════════════════════════
        $(document).ready(function() {
            // Auto generate slug from name
            $('#seriesName').on('input', function() {
                if (!$('#seriesId').val()) {
                    let title = $(this).val();
                    let slug = title.toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[đĐ]/g, 'd')
                        .replace(/([^0-9a-z-\s])/g, '')
                        .replace(/(\s+)/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    $('#seriesSlug').val(slug);
                }
            });

            loadSeries();

            $('#seriesForm').on('submit', function(e) {
                e.preventDefault();
                saveSeries();
            });
        });

        // ═════════════════════════════════════════════════════════════════════
        // LOAD DATA VIA AJAX (POST / GET compatible)
        // ═════════════════════════════════════════════════════════════════════
        function loadSeries() {
            const icon = $('#refreshIcon');
            icon.addClass('fa-spin');

            let filterType = 'all';
            let status = 'all';

            if (currentTab === 'active') {
                status = 'active';
            } else if (currentTab === 'draft') {
                status = 'draft';
            } else if (currentTab === 'has_products') {
                filterType = 'has_products';
            } else if (currentTab === 'no_products') {
                filterType = 'no_products';
            }

            $.ajax({
                url: '{{ route("admin.api.series.list") }}',
                type: 'POST',
                data: {
                    keyword: currentKeyword,
                    brand_id: currentBrandId,
                    category_id: currentCategoryId,
                    status: status,
                    filter_type: filterType,
                    sort: currentSort,
                    page: currentPage,
                    per_page: currentPerPage,
                    _token: csrfToken
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    const data = res.data || [];
                    const stats = res.stats || {};
                    const pagination = res.pagination || {};

                    updateStats(stats);
                    renderView(data);
                    renderPagination(pagination);
                },
                error: function(xhr) {
                    console.error('Lỗi tải danh sách dòng sản phẩm:', xhr);
                    toastr.error('Không thể tải danh sách dòng sản phẩm!', 'Lỗi');
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
            const total = stats.total || 0;
            const active = stats.active || 0;
            const withProducts = stats.with_products || 0;
            const totalBrands = stats.total_brands || 0;

            $('#statTotal').text(total.toLocaleString('vi-VN'));
            $('#statActive').text(active.toLocaleString('vi-VN'));
            $('#statWithProducts').text(withProducts.toLocaleString('vi-VN'));
            $('#statTotalBrands').text(totalBrands.toLocaleString('vi-VN'));

            $('#tabCountAll').text(total.toLocaleString('vi-VN'));
            $('#tabCountActive').text(active.toLocaleString('vi-VN'));
            $('#tabCountWithProducts').text(withProducts.toLocaleString('vi-VN'));
            $('#tabCountNoProducts').text((total - withProducts).toLocaleString('vi-VN'));
            $('#tabCountDraft').text((total - active).toLocaleString('vi-VN'));
        }

        // ═════════════════════════════════════════════════════════════════════
        // FILTERING, SEARCHING & SORTING
        // ═════════════════════════════════════════════════════════════════════
        function switchFilterTab(tab) {
            currentTab = tab;
            currentPage = 1;
            $('.series-tab-btn').removeClass('active');
            $(`.series-tab-btn[data-tab="${tab}"]`).addClass('active');
            loadSeries();
        }

        function handleSearch(val) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentKeyword = val.trim();
                currentPage = 1;
                loadSeries();
            }, 300);
        }

        function handleBrandChange(val) {
            currentBrandId = val;
            currentPage = 1;
            loadSeries();
        }

        function handleCategoryChange(val) {
            currentCategoryId = val;
            currentPage = 1;
            loadSeries();
        }

        function handleSortChange(val) {
            currentSort = val;
            currentPage = 1;
            loadSeries();
        }

        function handlePerPageChange(val) {
            currentPerPage = parseInt(val) || 24;
            currentPage = 1;
            loadSeries();
        }

        function goToPage(page) {
            currentPage = page;
            loadSeries();
            $('html, body').animate({ scrollTop: $('#gridViewContainer').offset().top - 120 }, 200);
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
        }

        // ═════════════════════════════════════════════════════════════════════
        // RENDER VIEWS
        // ═════════════════════════════════════════════════════════════════════
        function renderView(items) {
            if (currentView === 'grid') {
                renderGridView(items);
            }
            renderTableView(items);
        }

        // Render Grid View
        function renderGridView(items) {
            const container = $('#gridViewContainer');
            container.empty();

            if (items.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="series-empty-state">
                            <i class="fa fa-tags"></i>
                            <h5 class="font-weight-bold text-dark">Không tìm thấy dòng sản phẩm nào</h5>
                            <p class="text-muted mb-0">Thử thay đổi bộ lọc thương hiệu, danh mục hoặc từ khóa tìm kiếm</p>
                        </div>
                    </div>
                `);
                return;
            }

            items.forEach(item => {
                const brandName = item.brand ? item.brand.name : 'Khác';
                const catName = item.category ? item.category.name : 'Chưa phân mục';
                const isActive = item.status === 'active';
                const statusBadge = isActive 
                    ? `<span class="badge-status-pill badge-status-active"><i class="fa fa-check-circle"></i> Hoạt động</span>`
                    : `<span class="badge-status-pill badge-status-draft"><i class="fa fa-eye-slash"></i> Tạm ẩn</span>`;

                const cardHtml = `
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="series-card" id="series-card-${item.id}">
                            <div class="series-card-header">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="id-tag">#${item.id}</span>
                                    <span class="badge-brand-pill ml-1"><i class="fa fa-building-o"></i> ${brandName}</span>
                                </div>
                                <div>
                                    ${statusBadge}
                                </div>
                            </div>

                            <div class="series-card-body">
                                <h6 class="series-name" title="${item.name}">${item.name}</h6>
                                <span class="series-slug text-truncate" title="${item.slug}">${item.slug}</span>

                                <div class="mb-3 w-100">
                                    <span class="badge-cat-pill text-truncate" title="${catName}">
                                        <i class="fa fa-folder-o"></i> ${catName}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-auto w-100 pt-2">
                                    <span class="metric-models-pill" title="Số model thuộc series">
                                        <i class="fa fa-cube"></i> ${item.products_count || 0} Model
                                    </span>
                                    <small class="text-muted font-weight-bold" title="Thứ tự hiển thị">Thứ tự: ${item.sort_order || 0}</small>
                                </div>
                            </div>

                            <div class="series-card-footer">
                                <a href="/series/${encodeURIComponent(item.slug)}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; padding: 4px 10px;" title="Xem series ngoài web">
                                    <i class="fa fa-external-link mr-1"></i> Xem web
                                </a>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 4px 9px;" onclick="editSeries(${item.id})" title="Chỉnh sửa">
                                        <i class="fa fa-pencil"></i> Sửa
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 9px;" onclick="deleteSeries(${item.id}, '${escapeHtml(item.name)}', ${item.products_count || 0})" title="Xóa">
                                        <i class="fa fa-trash"></i>
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
        function renderTableView(items) {
            const tbody = $('#tableBody');
            tbody.empty();

            if (items.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa fa-tags mb-2" style="font-size: 32px;"></i>
                            <div class="font-weight-bold">Không tìm thấy dòng sản phẩm nào</div>
                        </td>
                    </tr>
                `);
                return;
            }

            items.forEach(item => {
                const brandName = item.brand ? item.brand.name : '---';
                const catName = item.category ? item.category.name : '---';
                const isActive = item.status === 'active';
                const statusBadge = isActive 
                    ? `<span class="badge-status-pill badge-status-active"><i class="fa fa-check-circle"></i> Hiển thị</span>`
                    : `<span class="badge-status-pill badge-status-draft"><i class="fa fa-eye-slash"></i> Tạm ẩn</span>`;

                const rowHtml = `
                    <tr>
                        <td class="text-center">
                            <span class="id-tag">#${item.id}</span>
                        </td>
                        <td>
                            <strong class="text-dark font-weight-bold" style="font-size: 14px;">${item.name}</strong>
                        </td>
                        <td>
                            <span class="series-slug mb-0">${item.slug}</span>
                        </td>
                        <td>
                            <span class="badge-brand-pill">${brandName}</span>
                        </td>
                        <td style="max-width: 220px;">
                            <span class="badge-cat-pill text-truncate" title="${catName}">${catName}</span>
                        </td>
                        <td class="text-center">
                            <span class="metric-models-pill">${item.products_count || 0} Model</span>
                        </td>
                        <td class="text-center">
                            <span class="text-muted font-weight-bold">${item.sort_order || 0}</span>
                        </td>
                        <td class="text-center">${statusBadge}</td>
                        <td class="text-right">
                            <a href="/series/${encodeURIComponent(item.slug)}" target="_blank" class="btn btn-sm btn-outline-secondary mr-1" style="border-radius: 6px; padding: 4px 8px;" title="Xem ngoài web">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary mr-1" style="border-radius: 6px; padding: 4px 8px;" onclick="editSeries(${item.id})" title="Chỉnh sửa">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 8px;" onclick="deleteSeries(${item.id}, '${escapeHtml(item.name)}', ${item.products_count || 0})" title="Xóa">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(rowHtml);
            });
        }

        // Render Pagination Buttons
        function renderPagination(p) {
            const container = $('#paginationContainer');
            if (!p.total || p.total === 0) {
                container.hide();
                return;
            }
            container.show();

            const from = p.from || 1;
            const to = p.to || p.total;
            $('#paginationInfo').text(`Hiển thị ${from.toLocaleString('vi-VN')} đến ${to.toLocaleString('vi-VN')} trong tổng số ${p.total.toLocaleString('vi-VN')} dòng sản phẩm`);

            const btnWrap = $('#paginationButtons');
            btnWrap.empty();

            const cur = p.current_page || 1;
            const last = p.last_page || 1;

            // Prev Button
            btnWrap.append(`<button type="button" class="page-btn" ${cur <= 1 ? 'disabled' : ''} onclick="goToPage(${cur - 1})"><i class="fa fa-angle-left"></i> Trước</button>`);

            // Page numbers
            let startPage = Math.max(1, cur - 2);
            let endPage = Math.min(last, cur + 2);

            if (startPage > 1) {
                btnWrap.append(`<button type="button" class="page-btn" onclick="goToPage(1)">1</button>`);
                if (startPage > 2) btnWrap.append(`<span class="px-2 text-muted">...</span>`);
            }

            for (let i = startPage; i <= endPage; i++) {
                btnWrap.append(`<button type="button" class="page-btn ${i === cur ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`);
            }

            if (endPage < last) {
                if (endPage < last - 1) btnWrap.append(`<span class="px-2 text-muted">...</span>`);
                btnWrap.append(`<button type="button" class="page-btn" onclick="goToPage(${last})">${last}</button>`);
            }

            // Next Button
            btnWrap.append(`<button type="button" class="page-btn" ${cur >= last ? 'disabled' : ''} onclick="goToPage(${cur + 1})">Sau <i class="fa fa-angle-right"></i></button>`);
        }

        // ═════════════════════════════════════════════════════════════════════
        // MODAL MANAGEMENT & SAVING
        // ═════════════════════════════════════════════════════════════════════
        function openModal() {
            $('#seriesForm')[0].reset();
            $('#seriesId').val('');
            $('#modalTitle').text('Thêm Dòng Sản Phẩm Mới');
            $('#seriesSortOrder').val('0');
            $('#seriesStatus').val('active');
            $('#seriesBrandId').val('');
            $('#seriesCategoryId').val('');
            $('#seriesModal').modal('show');
        }

        function editSeries(id) {
            $.ajax({
                url: `{{ url('admin/api/series') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data;
                    $('#seriesId').val(data.id);
                    $('#seriesName').val(data.name);
                    $('#seriesSlug').val(data.slug);
                    $('#seriesBrandId').val(data.brand_id || '');
                    $('#seriesCategoryId').val(data.category_id || '');
                    $('#seriesSortOrder').val(data.sort_order || 0);
                    $('#seriesStatus').val(data.status || 'active');
                    $('#seriesDescription').val(data.description || '');
                    $('#seriesContent').val(data.content || '');
                    $('#metaTitle').val(data.meta_title || '');
                    $('#metaDescription').val(data.meta_description || '');

                    $('#modalTitle').text('Chỉnh Sửa Dòng Series: ' + data.name);
                    $('#seriesModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Lỗi khi lấy thông tin series:', xhr);
                    toastr.error('Không thể lấy thông tin dòng sản phẩm!', 'Lỗi');
                }
            });
        }

        function saveSeries() {
            const id = $('#seriesId').val();
            const formData = {
                name: $('#seriesName').val(),
                slug: $('#seriesSlug').val(),
                brand_id: $('#seriesBrandId').val() || null,
                category_id: $('#seriesCategoryId').val() || null,
                sort_order: $('#seriesSortOrder').val() || 0,
                status: $('#seriesStatus').val() || 'active',
                description: $('#seriesDescription').val(),
                content: $('#seriesContent').val(),
                meta_title: $('#metaTitle').val(),
                meta_description: $('#metaDescription').val(),
                _token: csrfToken
            };

            const url = id ? `{{ url('admin/api/series') }}/${id}` : "{{ route('admin.api.series.store') }}";
            const method = id ? 'PUT' : 'POST';

            $('#btnSaveSeries').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Đang lưu...');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                dataType: 'json',
                success: function(response) {
                    $('#btnSaveSeries').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
                    $('#seriesModal').modal('hide');
                    toastr.success(response.message || 'Lưu thông tin thành công!', 'Thông báo');
                    loadSeries();
                },
                error: function(xhr) {
                    $('#btnSaveSeries').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        toastr.error(xhr.responseJSON.message);
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng kiểm tra lại!', 'Lỗi');
                    }
                }
            });
        }

        // ═════════════════════════════════════════════════════════════════════
        // DELETE SERIES
        // ═════════════════════════════════════════════════════════════════════
        function deleteSeries(id, name, productsCount) {
            let warnText = `Hành động này sẽ xóa vĩnh viễn dòng series "${name}".`;
            if (productsCount > 0) {
                warnText += ` ${productsCount} sản phẩm thuộc series này sẽ được tách ra độc lập (không bị xóa).`;
            }

            Swal.fire({
                title: 'Xóa dòng sản phẩm này?',
                text: warnText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/api/series') }}/${id}`,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        dataType: 'json',
                        success: function(response) {
                            toastr.success(response.message || 'Đã xóa dòng sản phẩm thành công!', 'Thông báo');
                            loadSeries();
                        },
                        error: function(xhr) {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi khi xóa series!';
                            toastr.error(msg, 'Lỗi');
                        }
                    });
                }
            });
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }
    </script>
@endsection
