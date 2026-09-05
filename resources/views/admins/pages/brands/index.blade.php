@extends('admins.layouts.master')

@section('styles')
    <style>
        /* ═════════════════════════════════════════════════════════════════════
           BRANDS MANAGEMENT - MODERN MINIMALIST & SPACIOUS DESIGN
           ═════════════════════════════════════════════════════════════════════ */

        /* Top Metric Cards */
        .brand-stat-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 22px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }
        .brand-stat-box:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .brand-stat-box .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .brand-stat-box .stat-label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 0;
        }
        .brand-stat-box .stat-icon-wrap {
            width: 38px;
            height: 38px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }
        .brand-stat-box .stat-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .brand-stat-box .stat-desc {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 0;
        }

        /* Filter Tabs */
        .brand-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eef2f6;
        }
        .brand-tab-btn {
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
        .brand-tab-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        .brand-tab-btn.active {
            background: #003b70;
            color: #ffffff;
            border-color: #003b70;
            box-shadow: 0 2px 6px rgba(0, 59, 112, 0.25);
        }
        .brand-tab-btn .tab-count {
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
            transition: all 0.15s;
        }
        .brand-tab-btn.active .tab-count {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* Brand Cards (Grid View) */
        .brand-card {
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
        .brand-card:hover {
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }
        .brand-card-header {
            padding: 14px 18px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .brand-card-footer {
            padding: 12px 18px;
            background: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Brand Logo Container */
        .brand-logo-wrap {
            width: 100%;
            height: 80px;
            background: #f8fafc;
            border: 1px solid #eef2f6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            margin-bottom: 14px;
        }
        .brand-logo-img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .brand-logo-placeholder {
            font-size: 24px;
            font-weight: 800;
            color: #003b70;
            background: #eff6ff;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbeafe;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .brand-slug {
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, Courier, monospace;
            font-size: 12px;
            color: #64748b;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 12px;
        }
        .brand-desc-snippet {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 14px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
        }

        /* Metric badges in card */
        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 700;
        }
        .metric-pill-products {
            background: #eff6ff;
            color: #0284c7;
            border: 1px solid #bfdbfe;
        }
        .metric-pill-series {
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
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
        .brand-empty-state {
            padding: 60px 20px;
            text-align: center;
            background: #ffffff;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            color: #64748b;
            grid-column: 1 / -1;
        }
        .brand-empty-state i {
            font-size: 44px;
            color: #94a3b8;
            margin-bottom: 14px;
            display: inline-block;
        }

        /* Table view styling */
        .brand-table {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .brand-table th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }
        .brand-table td {
            vertical-align: middle;
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
        }
        .brand-table tr:hover td {
            background: #f8fafc;
        }
        .table-logo-img {
            width: 70px;
            height: 38px;
            object-fit: contain;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 2px 6px;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="font-weight-bold text-dark mb-1">Quản Lý Thương Hiệu (Brands)</h4>
                <span class="text-muted" style="font-size: 13.5px;">Quản lý danh sách các Hãng sản xuất thiết bị tự động hóa (Omron, Mitsubishi, Yaskawa, Fuji, Schneider...)</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb" style="background: transparent;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Thương hiệu</a></li>
            </ol>
        </div>
    </div>

    <!-- Top Metric Cards (Spacious, Clean, Minimalist) -->
    <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="brand-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Thương Hiệu</span>
                    <div class="stat-icon-wrap" style="background: #f1f5f9; color: #475569;">
                        <i class="fa fa-building-o"></i>
                    </div>
                </div>
                <div class="stat-value" id="statTotal">0</div>
                <p class="stat-desc">Toàn bộ hãng sản xuất đối tác</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="brand-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Đang Có Sản Phẩm</span>
                    <div class="stat-icon-wrap" style="background: #eff6ff; color: #0284c7;">
                        <i class="fa fa-cubes"></i>
                    </div>
                </div>
                <div class="stat-value text-primary" id="statHasProducts">0</div>
                <p class="stat-desc">Hãng có thiết bị trên hệ thống</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="brand-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Dòng Series Liên Kết</span>
                    <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;">
                        <i class="fa fa-tags"></i>
                    </div>
                </div>
                <div class="stat-value text-dark" id="statTotalSeries">0</div>
                <p class="stat-desc">Tổng số series theo các hãng</p>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="brand-stat-box">
                <div class="stat-header">
                    <span class="stat-label">Tổng Thiết Bị Theo Hãng</span>
                    <div class="stat-icon-wrap" style="background: #dcfce7; color: #16a34a;">
                        <i class="fa fa-check-circle-o"></i>
                    </div>
                </div>
                <div class="stat-value text-success" id="statTotalProducts">0</div>
                <p class="stat-desc">Sản phẩm, biến tần, PLC, sensor...</p>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="brand-status-tabs">
        <button type="button" class="brand-tab-btn active" data-filter="all" onclick="filterByType('all')">
            <span>Tất cả thương hiệu</span>
            <span class="tab-count" id="tabCountAll">0</span>
        </button>
        <button type="button" class="brand-tab-btn" data-filter="has_products" onclick="filterByType('has_products')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #0284c7;"></span>
            <span>Đang có sản phẩm</span>
            <span class="tab-count" id="tabCountHasProducts">0</span>
        </button>
        <button type="button" class="brand-tab-btn" data-filter="has_series" onclick="filterByType('has_series')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #d97706;"></span>
            <span>Đang có dòng Series</span>
            <span class="tab-count" id="tabCountHasSeries">0</span>
        </button>
        <button type="button" class="brand-tab-btn" data-filter="no_products" onclick="filterByType('no_products')">
            <span class="d-inline-block rounded-circle mr-1" style="width: 8px; height: 8px; background: #64748b;"></span>
            <span>Chưa có sản phẩm</span>
            <span class="tab-count" id="tabCountNoProducts">0</span>
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
                               placeholder="Tìm kiếm thương hiệu theo tên, slug, mô tả..." 
                               style="border-color: #cbd5e1; border-radius: 0 8px 8px 0; font-size: 13.5px;"
                               oninput="handleSearch(this.value)">
                    </div>
                </div>
                <div class="col-md-7 d-flex justify-content-md-end align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center mr-3">
                        <label class="mb-0 text-muted mr-2 font-weight-bold" style="font-size: 13px; white-space: nowrap;">Sắp xếp:</label>
                        <select id="sortSelect" class="form-control form-control-sm" style="width: 175px; border-radius: 8px; border-color: #cbd5e1;" onchange="handleSortChange(this.value)">
                            <option value="latest">Mới tạo trước</option>
                            <option value="oldest">Cũ nhất trước</option>
                            <option value="name_asc">Tên hãng (A-Z)</option>
                            <option value="name_desc">Tên hãng (Z-A)</option>
                            <option value="products_desc">Nhiều sản phẩm nhất</option>
                            <option value="series_desc">Nhiều Series nhất</option>
                        </select>
                    </div>

                    <!-- View Switcher -->
                    <div class="btn-group mr-3" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" id="btnViewGrid" onclick="switchView('grid')" title="Giao diện dạng Thẻ Logo (Grid View)">
                            <i class="fa fa-th-large"></i> Dạng Thẻ
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnViewTable" onclick="switchView('table')" title="Giao diện dạng Bảng (Table View)">
                            <i class="fa fa-table"></i> Dạng Bảng
                        </button>
                    </div>

                    <!-- Refresh Button -->
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2" style="border-radius: 8px; padding: 6px 14px;" onclick="loadBrands()" title="Làm mới">
                        <i class="fa fa-refresh mr-1" id="refreshIcon"></i> Làm mới
                    </button>

                    <!-- Add Brand Button -->
                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" style="border-radius: 8px; padding: 6px 16px;" onclick="openModal()">
                        <i class="fa fa-plus mr-1"></i> + Thêm Thương Hiệu Mới
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
        <div class="brand-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="text-center">ID</th>
                        <th style="width: 90px;" class="text-center">Logo</th>
                        <th>Tên Thương Hiệu</th>
                        <th>Đường Dẫn (Slug)</th>
                        <th class="text-center" style="width: 140px;">Số Sản Phẩm</th>
                        <th class="text-center" style="width: 140px;">Số Series</th>
                        <th>Ngày Tạo</th>
                        <th class="text-right" style="width: 140px;">Hành Động</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Rows via JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Thêm / Chỉnh Sửa Thương Hiệu -->
    <div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: 14px; border: none; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.15);">
                <div class="modal-header py-3 px-4" style="background: #003b70; color: #ffffff;">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="width: 38px; height: 38px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa fa-building-o"></i>
                        </div>
                        <div>
                            <h5 class="modal-title font-weight-bold text-white mb-0" id="modalTitle">Thêm Thương Hiệu Mới</h5>
                            <span class="text-white-50" style="font-size: 12px;">Hãng sản xuất thiết bị tự động hóa công nghiệp</span>
                        </div>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <form id="brandForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="brandId" name="id">
                    
                    <div class="modal-body p-4">
                        <div class="row">
                            <div class="col-md-7 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Tên Thương Hiệu (Hãng sản xuất) <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="brandName" name="name" 
                                       placeholder="Ví dụ: Mitsubishi Electric, Omron, Yaskawa, Schneider..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;" required>
                            </div>
                            <div class="col-md-5 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">
                                    Đường dẫn tĩnh (Slug) <span class="text-muted font-weight-normal">(Tự tạo theo tên)</span>
                                </label>
                                <input type="text" class="form-control" id="brandSlug" name="slug" 
                                       placeholder="Ví dụ: mitsubishi-electric, omron..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Logo Thương Hiệu</label>
                            <div class="custom-file mb-2">
                                <input type="file" class="custom-file-input" id="logoFile" name="logo" accept="image/*" onchange="previewLogo(this)">
                                <label class="custom-file-label" for="logoFile" style="border-radius: 8px; border-color: #cbd5e1;">Chọn file ảnh logo từ máy tính...</label>
                            </div>
                            <div id="logoPreviewWrap" class="mt-2 align-items-center" style="display: none;">
                                <img id="logoPreview" src="" style="max-height: 60px; max-width: 140px; object-fit: contain; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 4px;" alt="Logo Preview">
                                <small class="text-muted ml-2">Logo hiển thị</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;">Giới thiệu / Lịch sử Thương Hiệu</label>
                            <textarea class="form-control" id="brandContent" name="content" rows="4" 
                                      placeholder="Mô tả về lịch sử hình thành, thế mạnh thiết bị, xuất xứ hãng..." 
                                      style="border-radius: 8px; border-color: #cbd5e1;"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Tiêu đề SEO (Meta Title)</label>
                                <input type="text" class="form-control" id="metaTitle" name="meta_title" 
                                       placeholder="Tiêu đề hiển thị Google SEO..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold text-dark" style="font-size: 13px;">Mô tả SEO (Meta Description)</label>
                                <input type="text" class="form-control" id="metaDescription" name="meta_description" 
                                       placeholder="Mô tả ngắn gọn hiển thị kết quả tìm kiếm..." 
                                       style="border-radius: 8px; border-color: #cbd5e1;">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer py-3 px-4 bg-light d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4 font-weight-bold" id="btnSaveBrand">
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
        let brandsData = [];
        let filteredBrands = [];
        let currentFilterType = 'all';
        let currentKeyword = '';
        let currentSort = 'latest';
        let currentView = 'grid'; // 'grid' | 'table'
        let searchTimeout = null;

        const csrfToken = '{{ csrf_token() }}';

        // ═════════════════════════════════════════════════════════════════════
        // INITIALIZATION
        // ═════════════════════════════════════════════════════════════════════
        $(document).ready(function() {
            // Auto generate slug from name
            $('#brandName').on('input', function() {
                if (!$('#brandId').val()) {
                    let title = $(this).val();
                    let slug = title.toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[đĐ]/g, 'd')
                        .replace(/([^0-9a-z-\s])/g, '')
                        .replace(/(\s+)/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    $('#brandSlug').val(slug);
                }
            });

            loadBrands();

            $('#brandForm').on('submit', function(e) {
                e.preventDefault();
                saveBrand();
            });
        });

        // ═════════════════════════════════════════════════════════════════════
        // LOAD DATA VIA AJAX (POST / GET compatible)
        // ═════════════════════════════════════════════════════════════════════
        function loadBrands() {
            const icon = $('#refreshIcon');
            icon.addClass('fa-spin');

            $.ajax({
                url: '{{ route("admin.api.brands.list") }}',
                type: 'POST',
                data: {
                    keyword: currentKeyword,
                    filter_type: currentFilterType,
                    sort: currentSort,
                    _token: csrfToken
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                success: function(res) {
                    brandsData = res.data || [];
                    updateStats(res.stats || {});
                    applyClientFilterAndRender();
                },
                error: function(xhr) {
                    console.error('Lỗi tải danh sách thương hiệu:', xhr);
                    toastr.error('Không thể tải danh sách thương hiệu!', 'Lỗi');
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
            $('#statHasProducts').text(stats.has_products || 0);
            $('#statTotalSeries').text(stats.total_series || 0);
            $('#statTotalProducts').text(stats.total_products || 0);

            $('#tabCountAll').text(stats.total || 0);
            $('#tabCountHasProducts').text(stats.has_products || 0);

            let hasSeriesCount = 0;
            let noProductsCount = 0;
            brandsData.forEach(item => {
                if ((item.series_count || 0) > 0) hasSeriesCount++;
                if ((item.products_count || 0) === 0) noProductsCount++;
            });

            $('#tabCountHasSeries').text(hasSeriesCount);
            $('#tabCountNoProducts').text(noProductsCount);
        }

        // ═════════════════════════════════════════════════════════════════════
        // FILTERING, SEARCHING & SORTING
        // ═════════════════════════════════════════════════════════════════════
        function filterByType(type) {
            currentFilterType = type;
            $('.brand-tab-btn').removeClass('active');
            $(`.brand-tab-btn[data-filter="${type}"]`).addClass('active');
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
            filteredBrands = brandsData.filter(item => {
                // Filter type
                if (currentFilterType === 'has_products' && (item.products_count || 0) === 0) {
                    return false;
                }
                if (currentFilterType === 'no_products' && (item.products_count || 0) > 0) {
                    return false;
                }
                if (currentFilterType === 'has_series' && (item.series_count || 0) === 0) {
                    return false;
                }

                // Keyword check
                if (currentKeyword) {
                    const name = (item.name || '').toLowerCase();
                    const slug = (item.slug || '').toLowerCase();
                    const content = (item.content || '').toLowerCase();
                    if (!name.includes(currentKeyword) && 
                        !slug.includes(currentKeyword) && 
                        !content.includes(currentKeyword)) {
                        return false;
                    }
                }
                return true;
            });

            // Sorting
            filteredBrands.sort((a, b) => {
                if (currentSort === 'name_asc') {
                    return (a.name || '').localeCompare(b.name || '');
                } else if (currentSort === 'name_desc') {
                    return (b.name || '').localeCompare(a.name || '');
                } else if (currentSort === 'products_desc') {
                    return (b.products_count || 0) - (a.products_count || 0);
                } else if (currentSort === 'series_desc') {
                    return (b.series_count || 0) - (a.series_count || 0);
                } else if (currentSort === 'oldest') {
                    return a.id - b.id;
                } else {
                    return b.id - a.id;
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

        function getInitial(name) {
            if (!name) return 'B';
            return name.trim().charAt(0).toUpperCase() || 'B';
        }

        function getLogoSrc(logo) {
            if (!logo) return null;
            return logo.startsWith('http') ? logo : `{{ asset('storage/clients/imgs/brands') }}/${logo}`;
        }

        // Render Grid View
        function renderGridView() {
            const container = $('#gridViewContainer');
            container.empty();

            if (filteredBrands.length === 0) {
                container.html(`
                    <div class="col-12">
                        <div class="brand-empty-state">
                            <i class="fa fa-building-o"></i>
                            <h5 class="font-weight-bold text-dark">Không tìm thấy thương hiệu nào</h5>
                            <p class="text-muted mb-0">Thử thay đổi bộ lọc hoặc thêm mới thương hiệu đối tác</p>
                        </div>
                    </div>
                `);
                return;
            }

            filteredBrands.forEach(item => {
                const initial = getInitial(item.name);
                const logoSrc = getLogoSrc(item.logo);
                const logoHtml = logoSrc 
                    ? `<img src="${logoSrc}" class="brand-logo-img" alt="${item.name}">`
                    : `<div class="brand-logo-placeholder">${initial}</div>`;

                const cardHtml = `
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="brand-card" id="brand-card-${item.id}">
                            <div class="brand-card-header">
                                <span class="id-tag">#${item.id}</span>
                                <div class="d-flex align-items-center gap-1">
                                    <a href="/cua-hang?brand=${encodeURIComponent(item.slug)}" target="_blank" class="btn btn-sm btn-outline-secondary" style="border-radius: 6px; padding: 3px 8px;" title="Xem sản phẩm ngoài web">
                                        <i class="fa fa-external-link"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; padding: 3px 8px;" onclick="editBrand(${item.id})" title="Chỉnh sửa">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 3px 8px;" onclick="deleteBrand(${item.id}, ${item.products_count || 0}, ${item.series_count || 0})" title="Xóa">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="brand-card-body">
                                <div class="brand-logo-wrap">
                                    ${logoHtml}
                                </div>

                                <h6 class="brand-name text-truncate w-100" title="${item.name}">${item.name}</h6>
                                <span class="brand-slug text-truncate" style="max-width: 100%;">${item.slug}</span>

                                ${item.content ? `<div class="brand-desc-snippet" title="${item.content}">${item.content}</div>` : ''}

                                <div class="d-flex justify-content-center gap-2 mt-auto w-100">
                                    <span class="metric-pill metric-pill-products" title="Số sản phẩm thuộc hãng">
                                        <i class="fa fa-cube"></i> ${item.products_count || 0} SP
                                    </span>
                                    <span class="metric-pill metric-pill-series" title="Số dòng series">
                                        <i class="fa fa-tags"></i> ${item.series_count || 0} Series
                                    </span>
                                </div>
                            </div>

                            <div class="brand-card-footer">
                                <small class="text-muted"><i class="fa fa-calendar-o mr-1"></i> ${new Date(item.created_at || Date.now()).toLocaleDateString('vi-VN')}</small>
                                <button type="button" class="btn btn-xs btn-outline-primary font-weight-bold" style="border-radius: 6px; padding: 3px 10px;" onclick="editBrand(${item.id})">
                                    Chỉnh sửa
                                </button>
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

            if (filteredBrands.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa fa-building-o mb-2" style="font-size: 32px;"></i>
                            <div class="font-weight-bold">Không tìm thấy thương hiệu nào</div>
                        </td>
                    </tr>
                `);
                return;
            }

            filteredBrands.forEach(item => {
                const initial = getInitial(item.name);
                const logoSrc = getLogoSrc(item.logo);
                const logoHtml = logoSrc 
                    ? `<img src="${logoSrc}" class="table-logo-img" alt="${item.name}">`
                    : `<div style="width: 70px; height: 38px; border-radius: 6px; background: #eff6ff; color: #003b70; font-weight: 800; font-size: 15px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid #dbeafe;">${initial}</div>`;

                const rowHtml = `
                    <tr>
                        <td class="text-center">
                            <span class="id-tag">#${item.id}</span>
                        </td>
                        <td class="text-center">
                            ${logoHtml}
                        </td>
                        <td>
                            <strong class="text-dark font-weight-bold" style="font-size: 14.5px;">${item.name}</strong>
                        </td>
                        <td>
                            <span class="brand-slug mb-0">${item.slug}</span>
                        </td>
                        <td class="text-center">
                            <span class="metric-pill metric-pill-products">${item.products_count || 0} SP</span>
                        </td>
                        <td class="text-center">
                            <span class="metric-pill metric-pill-series">${item.series_count || 0} Series</span>
                        </td>
                        <td>
                            <small class="text-muted">${new Date(item.created_at || Date.now()).toLocaleDateString('vi-VN')}</small>
                        </td>
                        <td class="text-right">
                            <a href="/cua-hang?brand=${encodeURIComponent(item.slug)}" target="_blank" class="btn btn-sm btn-outline-secondary mr-1" style="border-radius: 6px; padding: 4px 8px;" title="Xem ngoài web">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-primary mr-1" style="border-radius: 6px; padding: 4px 8px;" onclick="editBrand(${item.id})" title="Chỉnh sửa">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 8px;" onclick="deleteBrand(${item.id}, ${item.products_count || 0}, ${item.series_count || 0})" title="Xóa">
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
            $('#brandForm')[0].reset();
            $('#brandId').val('');
            $('#modalTitle').text('Thêm Thương Hiệu Mới');
            $('#logoPreviewWrap').hide();
            $('.custom-file-label').text('Chọn file ảnh logo từ máy tính...');
            $('#brandModal').modal('show');
        }

        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                $(input).next('.custom-file-label').text(fileName);
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoPreview').attr('src', e.target.result);
                    $('#logoPreviewWrap').css('display', 'flex');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function editBrand(id) {
            $.ajax({
                url: `{{ url('admin/api/brands') }}/${id}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    const data = response.data;
                    $('#brandId').val(data.id);
                    $('#brandName').val(data.name);
                    $('#brandSlug').val(data.slug);
                    $('#brandContent').val(data.content || '');
                    $('#metaTitle').val(data.meta_title || '');
                    $('#metaDescription').val(data.meta_description || '');

                    const logoSrc = getLogoSrc(data.logo);
                    if (logoSrc) {
                        $('#logoPreview').attr('src', logoSrc);
                        $('#logoPreviewWrap').css('display', 'flex');
                    } else {
                        $('#logoPreviewWrap').hide();
                    }

                    $('.custom-file-label').text('Thay đổi ảnh logo (nếu muốn)...');
                    $('#modalTitle').text('Chỉnh Sửa Thương Hiệu: ' + data.name);
                    $('#brandModal').modal('show');
                },
                error: function(xhr) {
                    console.error('Lỗi khi lấy thông tin thương hiệu:', xhr);
                    toastr.error('Không thể lấy thông tin thương hiệu!', 'Lỗi');
                }
            });
        }

        function saveBrand() {
            const id = $('#brandId').val();
            const formData = new FormData($('#brandForm')[0]);
            const url = id ? `{{ url('admin/api/brands') }}/${id}` : "{{ route('admin.api.brands.store') }}";

            // Nếu update có file thì dùng POST kèm _method PUT
            if (id) {
                formData.append('_method', 'PUT');
            }

            $('#btnSaveBrand').prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Đang lưu...');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: { 'X-CSRF-TOKEN': csrfToken },
                dataType: 'json',
                success: function(response) {
                    $('#btnSaveBrand').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
                    $('#brandModal').modal('hide');
                    toastr.success(response.message || 'Lưu thông tin thành công!', 'Thông báo');
                    loadBrands();
                },
                error: function(xhr) {
                    $('#btnSaveBrand').prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Lưu thông tin');
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
        // DELETE BRAND
        // ═════════════════════════════════════════════════════════════════════
        function deleteBrand(id, productsCount, seriesCount) {
            if (productsCount > 0) {
                Swal.fire({
                    title: 'Không thể xóa!',
                    text: `Thương hiệu này đang có ${productsCount} sản phẩm liên kết. Vui lòng chuyển hoặc xóa các sản phẩm trước.`,
                    icon: 'warning',
                    confirmButtonColor: '#003b70',
                    confirmButtonText: 'Đã hiểu'
                });
                return;
            }

            if (seriesCount > 0) {
                Swal.fire({
                    title: 'Không thể xóa!',
                    text: `Thương hiệu này đang có ${seriesCount} dòng Series liên kết. Vui lòng chuyển hoặc xóa các Series trước.`,
                    icon: 'warning',
                    confirmButtonColor: '#003b70',
                    confirmButtonText: 'Đã hiểu'
                });
                return;
            }

            Swal.fire({
                title: 'Xóa thương hiệu này?',
                text: "Hành động này sẽ xóa vĩnh viễn hãng sản xuất khỏi hệ thống.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/api/brands') }}/${id}`,
                        type: 'DELETE',
                        data: { _token: csrfToken },
                        headers: { 'X-CSRF-TOKEN': csrfToken },
                        dataType: 'json',
                        success: function(response) {
                            toastr.success(response.message || 'Đã xóa thương hiệu thành công!', 'Thông báo');
                            loadBrands();
                        },
                        error: function(xhr) {
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi khi xóa thương hiệu!';
                            toastr.error(msg, 'Lỗi');
                        }
                    });
                }
            });
        }
    </script>
@endsection
