@extends('admins.layouts.master')

@section('styles')
    <style>
        .tox-promotion { display: none !important; }
        .ss-main { border-radius: 0.25rem; min-height: 40px; }

        /* ─── Tree Container & Toolbar ────────────────────────────────────────── */
        .cat-toolbar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        .cat-toolbar-left {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 300px;
        }

        .cat-search-box {
            position: relative;
            flex: 1;
            max-width: 340px;
        }

        .cat-search-box input {
            width: 100%;
            height: 40px;
            padding: 8px 14px 8px 36px;
            font-size: 14px;
            color: #0f172a;
            font-weight: 500;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            transition: all 0.2s;
        }

        .cat-search-box input:focus {
            border-color: #593bdb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(89, 59, 219, 0.12);
        }

        .cat-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 14px;
            pointer-events: none;
        }

        .cat-type-filter select {
            height: 40px;
            padding: 6px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #f8fafc;
            color: #0f172a;
            font-size: 13.5px;
            font-weight: 600;
            outline: none;
            cursor: pointer;
        }

        .cat-toolbar-right {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .btn-tree-tool {
            height: 40px;
            padding: 0 13px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.15s;
        }

        /* ─── Tree View Main Canvas ───────────────────────────────────────────── */
        .cat-tree-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            min-height: 400px;
        }

        .cat-tree-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .cat-tree-node {
            margin-bottom: 6px;
            position: relative;
        }

        .cat-tree-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 14px;
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: 6px;
            transition: all 0.15s ease;
            gap: 12px;
        }

        .cat-tree-item:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }

        /* Highlight level */
        .cat-level-0 > .cat-tree-item {
            background: #fafbfc;
            border-left: 4px solid #593bdb;
        }
        .cat-level-1 > .cat-tree-item {
            border-left: 4px solid #0284c7;
        }
        .cat-level-2 > .cat-tree-item {
            border-left: 4px solid #0d9488;
        }
        .cat-level-3 > .cat-tree-item {
            border-left: 4px solid #eab308;
        }

        .cat-item-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
            min-width: 0;
        }

        .cat-toggle-btn {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 4px;
            color: #475569;
            font-size: 11px;
            cursor: pointer;
            transition: all 0.15s ease;
            flex-shrink: 0;
            padding: 0;
        }

        .cat-toggle-btn:hover {
            background: #593bdb;
            color: #ffffff;
            border-color: #593bdb;
        }

        .cat-toggle-btn.is-leaf {
            visibility: hidden;
            pointer-events: none;
        }

        .cat-folder-icon {
            font-size: 16px;
            flex-shrink: 0;
        }

        .cat-name-box {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            min-width: 0;
        }

        .cat-name {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a !important; /* Chữ màu đen đậm sắc nét */
            letter-spacing: -0.1px;
            cursor: pointer;
        }

        .cat-level-0 .cat-name {
            font-size: 15.5px;
        }

        .cat-slug-badge {
            font-size: 11.5px;
            font-family: monospace;
            color: #475569;
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 3px;
            border: 1px solid #e2e8f0;
        }

        .cat-level-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .badge-lvl-0 { background: #ede9fe; color: #5b21b6; }
        .badge-lvl-1 { background: #e0f2fe; color: #0369a1; }
        .badge-lvl-2 { background: #ccfbf1; color: #0f766e; }
        .badge-lvl-3 { background: #fef3c7; color: #b45309; }

        .cat-item-center {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .cat-stat-badge {
            font-size: 12px;
            color: #334155;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
        }

        .cat-type-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .type-product { background: #dcfce7; color: #15803d; }
        .type-post { background: #e0f2fe; color: #0369a1; }

        .cat-status-badge {
            font-size: 11.5px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 4px;
        }
        .status-active { background: #22c55e; color: #ffffff; }
        .status-draft { background: #ef4444; color: #ffffff; }

        .cat-item-right {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .cat-btn-action {
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }

        .btn-add-sub {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }
        .btn-add-sub:hover {
            background: #16a34a;
            color: #ffffff;
            border-color: #16a34a;
        }

        .btn-edit-cat {
            background: #f8fafc;
            color: #0284c7;
            border-color: #cbd5e1;
        }
        .btn-edit-cat:hover {
            background: #0284c7;
            color: #ffffff;
            border-color: #0284c7;
        }

        .btn-delete-cat {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .btn-delete-cat:hover {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        /* ─── Nested Children Container ───────────────────────────────────────── */
        .cat-children {
            list-style: none;
            margin: 6px 0 0 24px;
            padding-left: 12px;
            border-left: 2px dashed #cbd5e1;
            transition: all 0.2s ease;
        }

        .cat-children.is-collapsed {
            display: none;
        }

        .cat-empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #64748b;
            font-size: 15px;
        }

        /* Highlight matching search */
        .cat-tree-node.is-hidden {
            display: none !important;
        }

        .highlight-text {
            background: #fef08a;
            color: #854d0e;
            padding: 1px 4px;
            border-radius: 2px;
        }

        /* ─── Modal Import Styling ────────────────────────────────────────────── */
        .mapping-table th { background: #f8fafc; font-size: 13px; font-weight: 700; color: #0f172a; }
        .mapping-table td { font-size: 13px; vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản lý Cây Phân cấp Danh mục</h4>
                <span class="ml-1" style="color: #334155; font-weight: 500;">Xem và quản lý trực quan cấu trúc danh mục Cha ➔ Con ➔ Cháu ➔ Chắt</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Cây danh mục</a></li>
            </ol>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="cat-toolbar">
        <div class="cat-toolbar-left">
            <div class="cat-search-box">
                <i class="fa fa-search cat-search-icon"></i>
                <input type="text" id="catTreeSearch" placeholder="Tìm kiếm nhanh danh mục (tên, slug)..." autocomplete="off">
            </div>
            <div class="cat-type-filter">
                <select id="catTypeFilter" onchange="filterCategoryTree()">
                    <option value="">-- Tất cả loại --</option>
                    <option value="product">Sản phẩm (Product)</option>
                    <option value="post">Bài viết (Post)</option>
                </select>
            </div>
        </div>
        <div class="cat-toolbar-right">
            <button type="button" class="btn btn-outline-secondary btn-tree-tool" onclick="expandAllNodes()">
                <i class="fa fa-expand"></i> Mở rộng tất cả
            </button>
            <button type="button" class="btn btn-outline-secondary btn-tree-tool" onclick="collapseAllNodes()">
                <i class="fa fa-compress"></i> Thu gọn tất cả
            </button>
            <button type="button" class="btn btn-outline-success btn-tree-tool" onclick="exportCategoriesCsv()">
                <i class="fa fa-download"></i> Xuất Excel/CSV
            </button>
            <button type="button" class="btn btn-outline-info btn-tree-tool" onclick="openImportModal()">
                <i class="fa fa-upload"></i> Nhập Excel/CSV
            </button>
            <button type="button" class="btn btn-primary btn-tree-tool" onclick="openModal()">
                <i class="fa fa-plus-circle"></i> + Thêm danh mục gốc
            </button>
        </div>
    </div>

    {{-- Tree Canvas --}}
    <div class="cat-tree-container">
        <div id="catTreeLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Đang tải cây danh mục...</span>
            </div>
            <p class="mt-2 text-muted font-weight-bold">Đang tải cấu trúc cây danh mục...</p>
        </div>

        <ul class="cat-tree-list" id="catTreeRoot" style="display: none;">
            {{-- Rendered via Javascript Tree Builder --}}
        </ul>

        <div id="catTreeEmpty" class="cat-empty-state" style="display: none;">
            <i class="fa fa-folder-open-o fa-3x mb-3 text-muted"></i>
            <p>Không tìm thấy danh mục nào phù hợp.</p>
        </div>
    </div>

    {{-- Modal Thêm / Sửa Danh mục --}}
    <div class="modal fade" id="categoryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTitle" style="color: #0f172a;">Thêm Danh mục mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <input type="hidden" id="category_id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control font-weight-bold text-dark" id="name" name="name" placeholder="Ví dụ: Cảm biến quang" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Slug (Đường dẫn SEO)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Để trống để tự động tạo từ tên">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Loại danh mục <span class="text-danger">*</span></label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="product">Sản phẩm (Product)</option>
                                        <option value="post">Bài viết (Post)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Thuộc Danh mục cha (Cấp trên)</label>
                                    <select id="parent_id" name="parent_id">
                                        <option value="">-- Là Danh mục Gốc (Cấp 1) --</option>
                                        @foreach($parentCategories as $pCat)
                                            <option value="{{ $pCat->id }}">{{ $pCat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark d-flex justify-content-between align-items-center">
                                        <span>Ảnh Banner Danh mục (Lưu tại storage/clients/imgs/banners/{slug}.ext)</span>
                                    </label>
                                    <input type="hidden" id="remove_banner" name="remove_banner" value="0">
                                    <input type="file" id="banner_file" name="banner_file" accept="image/png, image/jpeg, image/webp, image/svg+xml" class="d-none" onchange="previewBannerFile(this)">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary font-weight-bold mr-2" onclick="$('#banner_file').click()">
                                            <i class="fa fa-upload mr-1"></i> Chọn / Tải ảnh Banner
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger font-weight-bold" id="btnRemoveBanner" onclick="removeBannerImage()" style="display: none;">
                                            <i class="fa fa-trash mr-1"></i> Xóa Banner
                                        </button>
                                    </div>
                                    <div id="bannerPreviewBox" class="border rounded p-2 bg-light text-center" style="display: none; position: relative;">
                                        <img id="bannerPreviewImg" src="" alt="Banner Preview" style="max-width: 100%; max-height: 120px; object-fit: cover; border-radius: 4px;">
                                        <small class="d-block text-muted mt-1" id="bannerFileNameText"></small>
                                    </div>
                                    <small class="text-muted d-block mt-1">Hệ thống sẽ tự động đặt tên file ảnh đúng theo <b>Slug</b> của danh mục (Ví dụ: <code>cam-bien.png</code>).</small>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Icon / Ảnh tròn Danh mục (storage/clients/imgs/categories/{slug}.ext)</label>
                                    <input type="file" id="icon_file" name="icon_file" accept="image/*" class="d-none" onchange="previewIconFile(this)">
                                    <div class="d-flex align-items-center mb-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary font-weight-bold mr-2" onclick="$('#icon_file').click()">
                                            <i class="fa fa-image mr-1"></i> Chọn Icon
                                        </button>
                                        <div id="iconPreviewBox" style="display: none;" class="align-items-center">
                                            <img id="iconPreviewImg" src="" alt="Icon" style="width: 36px; height: 36px; object-fit: contain; border-radius: 50%; border: 1px solid #cbd5e1; background: #fff; padding: 2px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Vị trí thứ tự</label>
                                            <input type="number" class="form-control" id="position" name="position" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="font-weight-bold text-dark">Trạng thái</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Hiển thị (Active)</option>
                                                <option value="draft">Ẩn (Draft)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold text-dark">Nội dung giới thiệu chi tiết danh mục</label>
                                    <textarea id="content" name="content" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary font-weight-bold" onclick="saveCategory()">
                        <i class="fa fa-save mr-1"></i> Lưu thay đổi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Import Danh mục Excel / CSV (Client-Side Parsing, Map Cột & Batching) --}}
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" style="color: #0f172a;">
                        <i class="fa fa-file-excel-o text-success mr-2"></i>Nhập Danh mục từ file Excel / CSV
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" id="btnCloseImportModal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    {{-- Bước 1: Chọn file & Chế độ --}}
                    <div id="importStep1">
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Chế độ Nhập (Import Mode)</label>
                            <select id="importMode" class="form-control">
                                <option value="upsert">1. Thêm mới và Cập nhật (Khuyên dùng: Tự tạo mới nếu chưa có, cập nhật nếu đã có)</option>
                                <option value="insert">2. Chỉ Thêm mới (Bỏ qua các danh mục đã tồn tại)</option>
                                <option value="update">3. Chỉ Cập nhật (Chỉ sửa thông tin các danh mục đã có)</option>
                            </select>
                            <small class="form-text text-muted mt-2">
                                • Cột <b>parent</b> hỗ trợ định dạng phân cấp: <code>Cha > Con > Cháu</code> (Hệ thống sẽ tự động tạo đủ cây cha con nếu chưa tồn tại).
                            </small>
                        </div>
                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="font-weight-bold text-dark mb-0">Chọn file dữ liệu (.csv)</label>
                                <button type="button" class="btn btn-sm btn-link text-primary font-weight-bold p-0" onclick="downloadSampleCsv()">
                                    <i class="fa fa-download"></i> Tải file mẫu chuẩn UTF-8
                                </button>
                            </div>
                            <input type="file" id="importCsvFile" accept=".csv, .txt" class="form-control-file p-2 border rounded bg-light">
                        </div>
                    </div>

                    {{-- Bước 2: Map Cột --}}
                    <div id="importStep2" style="display: none;">
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 13px;">
                            <i class="fa fa-info-circle mr-1"></i> Kiểm tra và khớp các cột từ file Excel của bạn với cột hệ thống tương ứng:
                        </div>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-bordered table-sm mapping-table" id="mappingTable">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;">Cột Hệ Thống</th>
                                        <th style="width: 55%;">Cột trong File của bạn</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Render via Javascript --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Progress & Stats --}}
                        <div id="importProgressWrapper" style="display: none;" class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0 font-weight-bold text-primary" id="importProgressText">Đang xử lý: 0 / 0</h6>
                                <span class="badge badge-info" id="importPercentBadge">0%</span>
                            </div>
                            <div class="progress" style="height: 18px; border-radius: 4px;">
                                <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <div class="row text-center mt-3 pt-2 border-top">
                                <div class="col-4">
                                    <small class="text-muted d-block font-weight-bold">Thêm mới</small>
                                    <h5 class="text-success font-weight-bold mb-0" id="statInserted">0</h5>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block font-weight-bold">Cập nhật</small>
                                    <h5 class="text-info font-weight-bold mb-0" id="statUpdated">0</h5>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted d-block font-weight-bold">Bỏ qua</small>
                                    <h5 class="text-secondary font-weight-bold mb-0" id="statSkipped">0</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCancelImport">Đóng</button>
                    <button type="button" class="btn btn-outline-secondary" id="btnBackToStep1" onclick="backToStep1()" style="display: none;">
                        <i class="fa fa-arrow-left"></i> Chọn lại file
                    </button>
                    <button type="button" class="btn btn-primary font-weight-bold" id="btnReadHeaders" onclick="readCsvHeaders()">
                        Tiếp tục (Đọc cột) <i class="fa fa-arrow-right ml-1"></i>
                    </button>
                    <button type="button" class="btn btn-success font-weight-bold" id="btnStartImport" onclick="startImport()" style="display: none;">
                        <i class="fa fa-check-circle mr-1"></i> Bắt đầu Nhập dữ liệu
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- PapaParse for high-speed client-side CSV handling -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

    <script>
        let rawCategories = [];
        let ssParent, ssStatus, ssType;
        let collapsedNodes = new Set(); // Lưu ID các node đang bị thu gọn

        // ─── Các cột hệ thống phục vụ Import / Export ────────────────────────
        const SYSTEM_COLUMNS = [
            { key: 'id', label: 'ID Danh mục (Dùng để cập nhật chính xác)', required: false },
            { key: 'name', label: 'Tên danh mục (Bắt buộc)', required: true },
            { key: 'slug', label: 'Slug (Đường dẫn tĩnh SEO)', required: false },
            { key: 'parent', label: 'Danh mục cha (VD: Biến tần > Biến tần 1 pha)', required: false },
            { key: 'type', label: 'Loại danh mục (product / post)', required: false },
            { key: 'position', label: 'Vị trí thứ tự (Số nguyên)', required: false },
            { key: 'status', label: 'Trạng thái (active / draft)', required: false },
            { key: 'content', label: 'Nội dung giới thiệu chi tiết (HTML)', required: false }
        ];

        let csvHeaders = [];

        $(document).ready(function() {
            // 1. Tải dữ liệu cây danh mục ngay lập tức
            loadCategoryTree();

            // 2. Khởi tạo các thành phần giao diện an toàn
            try {
                initSlimSelects();
            } catch(e) {
                console.warn('SlimSelect init:', e);
            }

            try {
                initTinyMCE();
            } catch(e) {
                console.warn('TinyMCE init:', e);
            }

            // Lắng nghe tìm kiếm gõ phím tức thì
            $('#catTreeSearch').on('input', function() {
                filterCategoryTree();
            });

            // Reset modal khi đóng
            $('#categoryModal').on('hidden.bs.modal', function () {
                $('#categoryForm')[0].reset();
                $('#category_id').val('');
                if (ssParent) ssParent.setSelected('');
                if (ssStatus) ssStatus.setSelected('active');
                if (ssType) ssType.setSelected('product');
                if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                    tinymce.get('content').setContent('');
                }
            });
        });

        function initSlimSelects() {
            if (typeof SlimSelect === 'undefined') return;

            const pEl = document.querySelector('#parent_id');
            if (pEl) {
                ssParent = new SlimSelect({
                    select: '#parent_id',
                    settings: {
                        placeholderText: 'Chọn hoặc tìm danh mục cha...',
                        allowDeselect: true
                    }
                });
            }

            const sEl = document.querySelector('#status');
            if (sEl) {
                ssStatus = new SlimSelect({
                    select: '#status',
                    settings: { showSearch: false }
                });
            }

            const tEl = document.querySelector('#type');
            if (tEl) {
                ssType = new SlimSelect({
                    select: '#type',
                    settings: { showSearch: false }
                });
            }
        }

        function initTinyMCE() {
            if (typeof tinymce === 'undefined') return;

            tinymce.init({
                selector: '#content',
                height: 250,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });
        }

        // ─── Tải dữ liệu & Xây dựng Cây Phân Cấp ─────────────────────────────
        function loadCategoryTree(preserveCollapsed = true) {
            $('#catTreeLoading').show();
            $('#catTreeRoot').hide();
            $('#catTreeEmpty').hide();

            $.ajax({
                url: '{{ route("admin.categories.api.list") }}',
                type: 'GET',
                success: function(res) {
                    $('#catTreeLoading').hide();
                    rawCategories = res.data || [];
                    
                    if (rawCategories.length === 0) {
                        $('#catTreeEmpty').show();
                        return;
                    }

                    renderTree();
                    updateParentDropdownOptions();
                },
                error: function() {
                    $('#catTreeLoading').hide();
                    toastr.error('Lỗi khi tải danh sách danh mục!');
                }
            });
        }

        function buildCategoryTreeHierarchy(items) {
            let itemMap = {};
            let tree = [];

            items.forEach(item => {
                itemMap[item.id] = { ...item, childrenNodes: [] };
            });

            items.forEach(item => {
                if (item.parent_id && itemMap[item.parent_id]) {
                    itemMap[item.parent_id].childrenNodes.push(itemMap[item.id]);
                } else {
                    tree.push(itemMap[item.id]);
                }
            });

            return tree;
        }

        function getLevelBadge(level) {
            switch(level) {
                case 0: return '<span class="cat-level-badge badge-lvl-0">Gốc</span>';
                case 1: return '<span class="cat-level-badge badge-lvl-1">Cấp 2 (Con)</span>';
                case 2: return '<span class="cat-level-badge badge-lvl-2">Cấp 3 (Cháu)</span>';
                default: return `<span class="cat-level-badge badge-lvl-3">Cấp ${level + 1} (Chắt)</span>`;
            }
        }

        function createNodeElement(node, level, index = 0) {
            const li = document.createElement('li');
            li.className = `cat-tree-node cat-level-${level}`;
            li.dataset.id = node.id;
            li.dataset.name = (node.name || '').toLowerCase();
            li.dataset.slug = (node.slug || '').toLowerCase();
            li.dataset.rawName = node.name || '';
            li.dataset.type = node.type || 'product';
            li.dataset.originalIndex = index;

            const hasChildren = node.childrenNodes && node.childrenNodes.length > 0;
            const isCollapsed = collapsedNodes.has(node.id);

            const typeBadge = node.type === 'product'
                ? '<span class="cat-type-badge type-product">Sản phẩm</span>'
                : '<span class="cat-type-badge type-post">Bài viết</span>';

            const statusBadge = node.status === 'active'
                ? '<span class="cat-status-badge status-active">Hiển thị</span>'
                : '<span class="cat-status-badge status-draft">Ẩn</span>';

            const countInfo = node.type === 'product'
                ? `${node.products_count || 0} SP`
                : `${node.posts_count || 0} bài`;

            const childrenBadge = hasChildren
                ? `<span class="cat-stat-badge" title="Số danh mục con trực thuộc"><i class="fa fa-sitemap mr-1"></i>${node.childrenNodes.length} mục con</span>`
                : '';

            li.innerHTML = `
                <div class="cat-tree-item">
                    <div class="cat-item-left">
                        <button type="button" class="cat-toggle-btn ${!hasChildren ? 'is-leaf' : ''}" onclick="toggleNode(${node.id})">
                            <i class="fa ${isCollapsed ? 'fa-chevron-right' : 'fa-chevron-down'}"></i>
                        </button>
                        <span class="cat-folder-icon">${hasChildren ? (isCollapsed ? '📁' : '📂') : '📄'}</span>
                        <div class="cat-name-box">
                            <span class="cat-name" onclick="toggleNode(${node.id})">${escapeHtml(node.name)}</span>
                            ${getLevelBadge(level)}
                            <span class="cat-slug-badge" title="Đường dẫn tĩnh">/${escapeHtml(node.slug)}</span>
                        </div>
                    </div>

                    <div class="cat-item-center">
                        ${childrenBadge}
                        <span class="cat-stat-badge"><i class="fa fa-cubes mr-1"></i>${countInfo}</span>
                        ${typeBadge}
                        ${statusBadge}
                    </div>

                    <div class="cat-item-right">
                        <button type="button" class="cat-btn-action btn-add-sub" onclick="openAddSubModal(${node.id}, '${escapeJs(node.name)}')" title="Thêm danh mục con thuộc '${escapeHtml(node.name)}'">
                            <i class="fa fa-plus"></i> + Con
                        </button>
                        <button type="button" class="cat-btn-action btn-edit-cat" onclick="editCategory(${node.id})" title="Chỉnh sửa">
                            <i class="fa fa-pencil"></i> Sửa
                        </button>
                        <button type="button" class="cat-btn-action btn-delete-cat" onclick="deleteCategory(${node.id}, '${escapeJs(node.name)}', ${node.childrenNodes ? node.childrenNodes.length : 0})" title="Xóa">
                            <i class="fa fa-trash"></i> Xóa
                        </button>
                    </div>
                </div>
            `;

            if (hasChildren) {
                const ul = document.createElement('ul');
                ul.className = `cat-children ${isCollapsed ? 'is-collapsed' : ''}`;
                ul.id = `children-of-${node.id}`;

                node.childrenNodes.forEach((child, cIdx) => {
                    ul.appendChild(createNodeElement(child, level + 1, cIdx));
                });

                li.appendChild(ul);
            }

            return li;
        }

        function renderTree() {
            const rootEl = document.getElementById('catTreeRoot');
            rootEl.innerHTML = '';

            const treeData = buildCategoryTreeHierarchy(rawCategories);
            
            treeData.forEach((node, idx) => {
                rootEl.appendChild(createNodeElement(node, 0, idx));
            });

            $('#catTreeRoot').show();
            filterCategoryTree(); // Áp dụng filter hiện tại nếu có
        }

        // ─── Đóng / Mở Nhánh ────────────────────────────────────────────────
        function toggleNode(nodeId) {
            const nodeEl = document.querySelector(`.cat-tree-node[data-id="${nodeId}"]`);
            if (!nodeEl) return;

            const childrenUl = document.getElementById(`children-of-${nodeId}`);
            if (!childrenUl) return;

            const toggleBtn = nodeEl.querySelector('.cat-toggle-btn i');
            const folderIcon = nodeEl.querySelector('.cat-folder-icon');

            if (childrenUl.classList.contains('is-collapsed')) {
                childrenUl.classList.remove('is-collapsed');
                collapsedNodes.delete(nodeId);
                if (toggleBtn) toggleBtn.className = 'fa fa-chevron-down';
                if (folderIcon) folderIcon.textContent = '📂';
            } else {
                childrenUl.classList.add('is-collapsed');
                collapsedNodes.add(nodeId);
                if (toggleBtn) toggleBtn.className = 'fa fa-chevron-right';
                if (folderIcon) folderIcon.textContent = '📁';
            }
        }

        function expandAllNodes() {
            collapsedNodes.clear();
            document.querySelectorAll('.cat-children').forEach(ul => {
                ul.classList.remove('is-collapsed');
            });
            document.querySelectorAll('.cat-toggle-btn:not(.is-leaf) i').forEach(icon => {
                icon.className = 'fa fa-chevron-down';
            });
            document.querySelectorAll('.cat-folder-icon').forEach(f => {
                if (f.closest('.cat-tree-node').querySelector('.cat-children')) {
                    f.textContent = '📂';
                }
            });
        }

        function collapseAllNodes() {
            document.querySelectorAll('.cat-children').forEach(ul => {
                ul.classList.add('is-collapsed');
                const parentId = parseInt(ul.id.replace('children-of-', ''));
                if (parentId) collapsedNodes.add(parentId);
            });
            document.querySelectorAll('.cat-toggle-btn:not(.is-leaf) i').forEach(icon => {
                icon.className = 'fa fa-chevron-right';
            });
            document.querySelectorAll('.cat-folder-icon').forEach(f => {
                if (f.closest('.cat-tree-node').querySelector('.cat-children')) {
                    f.textContent = '📁';
                }
            });
        }

        // ─── Xử lý Tiếng Việt (NFC/NFD, Có dấu & Không dấu) ────────────────
        function removeVietnameseTones(str) {
            if (!str) return '';
            return str
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .replace(/Đ/g, 'D')
                .toLowerCase()
                .trim();
        }

        function normalizeSearchStr(str) {
            if (!str) return '';
            return str.normalize('NFC').toLowerCase().trim();
        }

        // ─── Tính điểm ưu tiên (Ưu tiên từ trái sang phải / Prefix Match) ───
        function calculateMatchScore(cat, queryNfc, queryNoTone) {
            if (!queryNfc) return 0;

            const nameNfc = normalizeSearchStr(cat.name);
            const nameNoTone = removeVietnameseTones(cat.name);
            const slug = (cat.slug || '').toLowerCase();

            // 1. Khớp chính xác 100%
            if (nameNfc === queryNfc || nameNoTone === queryNoTone) {
                return 10000;
            }

            // 2. Bắt đầu ngay từ ký tự đầu tiên bên trái (Prefix Match: index = 0)
            if (nameNfc.startsWith(queryNfc) || nameNoTone.startsWith(queryNoTone) || slug.startsWith(queryNoTone)) {
                return 8000 - Math.min(nameNfc.length, 300);
            }

            // 3. Khớp ở đầu một từ (Word Boundary)
            const idxNfc = nameNfc.indexOf(queryNfc);
            const idxNoTone = nameNoTone.indexOf(queryNoTone);
            const minIdx = Math.min(idxNfc >= 0 ? idxNfc : 999, idxNoTone >= 0 ? idxNoTone : 999);

            if (minIdx < 999) {
                return 5000 - (minIdx * 50) - Math.min(nameNfc.length, 100);
            }

            if (slug.includes(queryNoTone)) {
                return 2000;
            }

            return 0;
        }

        // ─── Sắp xếp lại danh sách các node theo điểm số ────────────────────
        function sortTreeNodes(parentUl, scoreMap, isSearching) {
            const listItems = Array.from(parentUl.children).filter(el => el.matches('.cat-tree-node'));
            if (listItems.length <= 1) return;

            listItems.sort((a, b) => {
                if (isSearching) {
                    const scoreA = scoreMap[parseInt(a.dataset.id)] || 0;
                    const scoreB = scoreMap[parseInt(b.dataset.id)] || 0;
                    if (scoreB !== scoreA) {
                        return scoreB - scoreA; // Điểm cao (khớp từ trái sang phải) lên trước
                    }
                }
                const origA = parseInt(a.dataset.originalIndex || 0);
                const origB = parseInt(b.dataset.originalIndex || 0);
                return origA - origB;
            });

            listItems.forEach(li => parentUl.appendChild(li));
        }

        function sortEntireTree(scoreMap, isSearching) {
            const rootUl = document.getElementById('catTreeRoot');
            if (rootUl) {
                sortTreeNodes(rootUl, scoreMap, isSearching);
            }
            document.querySelectorAll('.cat-children').forEach(ul => {
                sortTreeNodes(ul, scoreMap, isSearching);
            });
        }

        // ─── Highlight từ khóa ──────────────────────────────────────────────
        function highlightSearchText(element, rawText, query) {
            if (!query) {
                element.textContent = rawText;
                return;
            }
            const cleanQuery = query.trim().normalize('NFC');
            const rawNorm = rawText.normalize('NFC');

            try {
                const escaped = cleanQuery.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp(`(${escaped})`, 'gi');
                if (regex.test(rawNorm)) {
                    element.innerHTML = escapeHtml(rawNorm).replace(regex, '<mark class="highlight-text">$1</mark>');
                    return;
                }
            } catch(e) {}

            element.textContent = rawText;
        }

        // ─── Tìm kiếm & Lọc chính ───────────────────────────────────────────
        function filterCategoryTree() {
            const rawQuery = ($('#catTreeSearch').val() || '').trim();
            const queryNfc = normalizeSearchStr(rawQuery);
            const queryNoTone = removeVietnameseTones(rawQuery);
            const typeFilter = $('#catTypeFilter').val();

            const allNodes = document.querySelectorAll('.cat-tree-node');

            if (!rawQuery && !typeFilter) {
                sortEntireTree({}, false);

                allNodes.forEach(node => {
                    node.classList.remove('is-hidden');
                    const nameSpan = node.querySelector('.cat-name');
                    const rawName = node.dataset.rawName || (nameSpan ? nameSpan.textContent : '');
                    if (nameSpan && rawName) {
                        nameSpan.textContent = rawName;
                    }
                });
                $('#catTreeEmpty').hide();
                return;
            }

            // Xây dựng bản đồ quan hệ Cha - Con từ rawCategories
            const catMap = {};
            const childrenMap = {};
            rawCategories.forEach(cat => {
                catMap[cat.id] = cat;
                const pId = cat.parent_id || 0;
                if (!childrenMap[pId]) childrenMap[pId] = [];
                childrenMap[pId].push(cat.id);
            });

            const visibleIds = new Set();
            const directlyMatchedIds = new Set();
            const scoreMap = {};

            // 1. Quét tính điểm & lọc danh mục
            rawCategories.forEach(cat => {
                const catType = cat.type || 'product';

                let matchesType = true;
                if (typeFilter) {
                    matchesType = catType === typeFilter;
                }

                let score = 0;
                if (rawQuery) {
                    score = calculateMatchScore(cat, queryNfc, queryNoTone);
                } else if (matchesType) {
                    score = 100;
                }

                if (score > 0 && matchesType) {
                    directlyMatchedIds.add(cat.id);
                    visibleIds.add(cat.id);
                    scoreMap[cat.id] = Math.max(scoreMap[cat.id] || 0, score);

                    // A. Truy vết tất cả CHA / ÔNG / CỤ lên tận Gốc (Ancestors)
                    let pId = cat.parent_id;
                    let pScore = score - 1;
                    while (pId && catMap[pId]) {
                        visibleIds.add(pId);
                        scoreMap[pId] = Math.max(scoreMap[pId] || 0, pScore);
                        pId = catMap[pId].parent_id;
                        pScore = pScore - 1;
                    }

                    // B. Truy vết tất cả CON / CHÁU / CHẮT bên dưới (Descendants)
                    function collectDescendants(id, inheritScore) {
                        if (childrenMap[id]) {
                            childrenMap[id].forEach(childId => {
                                visibleIds.add(childId);
                                scoreMap[childId] = Math.max(scoreMap[childId] || 0, inheritScore);
                                collectDescendants(childId, inheritScore - 1);
                            });
                        }
                    }
                    collectDescendants(cat.id, score - 1);
                }
            });

            // 2. Sắp xếp lại DOM theo điểm ưu tiên từ trái sang phải
            sortEntireTree(scoreMap, true);

            // 3. Cập nhật trạng thái hiển thị / ẩn và mở rộng nhánh
            let matchCount = 0;

            allNodes.forEach(node => {
                const id = parseInt(node.dataset.id);
                const nameSpan = node.querySelector('.cat-name');
                const rawName = node.dataset.rawName || (nameSpan ? nameSpan.textContent : '');

                if (visibleIds.has(id)) {
                    node.classList.remove('is-hidden');
                    matchCount++;

                    // Highlight từ khóa nếu node này khớp trực tiếp
                    if (rawQuery && directlyMatchedIds.has(id) && nameSpan) {
                        highlightSearchText(nameSpan, rawName, rawQuery);
                    } else if (nameSpan) {
                        nameSpan.textContent = rawName;
                    }

                    // Tự động mở rộng nhánh con
                    const childrenUl = document.getElementById(`children-of-${id}`);
                    if (childrenUl) {
                        childrenUl.classList.remove('is-collapsed');
                        collapsedNodes.delete(id);
                        const icon = node.querySelector('.cat-toggle-btn i');
                        if (icon) icon.className = 'fa fa-chevron-down';
                        const fIcon = node.querySelector('.cat-folder-icon');
                        if (fIcon) fIcon.textContent = '📂';
                    }
                } else {
                    node.classList.add('is-hidden');
                    if (nameSpan) nameSpan.textContent = rawName;
                }
            });

            $('#catTreeEmpty').toggle(matchCount === 0);
        }

        function resetCategoryMediaPreviews() {
            $('#remove_banner').val('0');
            $('#banner_file').val('');
            $('#icon_file').val('');
            $('#bannerPreviewBox').hide();
            $('#bannerPreviewImg').attr('src', '');
            $('#btnRemoveBanner').hide();
            $('#iconPreviewBox').hide();
            $('#iconPreviewImg').attr('src', '');
        }

        function previewBannerFile(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#bannerPreviewImg').attr('src', e.target.result);
                    $('#bannerPreviewBox').show();
                    $('#btnRemoveBanner').show();
                    $('#bannerFileNameText').html(`Ảnh tải lên: <b>${file.name}</b> (Sẽ được lưu với tên theo slug danh mục)`);
                    $('#remove_banner').val('0');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeBannerImage() {
            $('#remove_banner').val('1');
            $('#banner_file').val('');
            $('#bannerPreviewBox').hide();
            $('#bannerPreviewImg').attr('src', '');
            $('#btnRemoveBanner').hide();
        }

        function previewIconFile(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#iconPreviewImg').attr('src', e.target.result);
                    $('#iconPreviewBox').css('display', 'inline-flex');
                };
                reader.readAsDataURL(file);
            }
        }

        // ─── Modal Actions (Thêm / Sửa / Xóa) ────────────────────────────────
        function openModal() {
            $('#modalTitle').text('Thêm Danh mục Gốc mới');
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            resetCategoryMediaPreviews();
            if (ssParent) ssParent.setSelected('');
            if (ssStatus) ssStatus.setSelected('active');
            if (ssType) ssType.setSelected('product');
            if (tinymce.get('content')) tinymce.get('content').setContent('');
            $('#categoryModal').modal('show');
        }

        function openAddSubModal(parentId, parentName) {
            $('#modalTitle').text(`Thêm Danh mục con thuộc: ${parentName}`);
            $('#categoryForm')[0].reset();
            $('#category_id').val('');
            resetCategoryMediaPreviews();
            if (ssParent) ssParent.setSelected(String(parentId));
            if (ssStatus) ssStatus.setSelected('active');
            if (ssType) ssType.setSelected('product');
            if (tinymce.get('content')) tinymce.get('content').setContent('');
            $('#categoryModal').modal('show');
        }

        function editCategory(id) {
            resetCategoryMediaPreviews();
            $.get(`{{ url('admin/api/categories') }}/${id}`, function(res) {
                if (res.success && res.data) {
                    const data = res.data;
                    $('#modalTitle').text(`Chỉnh sửa Danh mục: ${data.name}`);
                    $('#category_id').val(data.id);
                    $('#name').val(data.name);
                    $('#slug').val(data.slug);
                    if (ssType) ssType.setSelected(data.type || 'product');
                    if (ssParent) ssParent.setSelected(data.parent_id ? String(data.parent_id) : '');
                    $('#position').val(data.position || 0);
                    if (ssStatus) ssStatus.setSelected(data.status || 'active');

                    // Banner preview
                    if (data.banner) {
                        let bannerUrl = data.banner.startsWith('http') 
                            ? data.banner 
                            : `{{ asset('storage/clients/imgs/banners') }}/${data.banner}`;
                        $('#bannerPreviewImg').attr('src', bannerUrl);
                        $('#bannerFileNameText').html(`File banner hiện tại: <b>${data.banner}</b>`);
                        $('#bannerPreviewBox').show();
                        $('#btnRemoveBanner').show();
                    }

                    // Icon preview
                    if (data.icon) {
                        let iconUrl = data.icon.startsWith('http') 
                            ? data.icon 
                            : `{{ asset('storage/clients/imgs/categories') }}/${data.icon}`;
                        $('#iconPreviewImg').attr('src', iconUrl);
                        $('#iconPreviewBox').css('display', 'inline-flex');
                    }

                    if (tinymce.get('content')) {
                        tinymce.get('content').setContent(data.content || '');
                    }

                    $('#categoryModal').modal('show');
                } else {
                    toastr.error('Không tìm thấy thông tin danh mục!');
                }
            }).fail(function() {
                toastr.error('Lỗi khi tải thông tin danh mục');
            });
        }

        function saveCategory() {
            if (tinymce.get('content')) {
                tinymce.triggerSave();
            }

            const name = $('#name').val().trim();
            if (!name) {
                toastr.warning('Vui lòng nhập tên danh mục!');
                $('#name').focus();
                return;
            }

            const formData = new FormData($('#categoryForm')[0]);
            const id = $('#category_id').val();
            const parentId = $('#parent_id').val();

            if (id && parentId && parseInt(id) === parseInt(parentId)) {
                toastr.error('Danh mục cha không thể là chính nó!');
                return;
            }

            $.ajax({
                url: '{{ route("admin.categories.api.store") }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        $('#categoryModal').modal('hide');
                        loadCategoryTree();
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        let msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        toastr.error(msg);
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                }
            });
        }

        function deleteCategory(id, name, childrenCount) {
            let warnText = `Bạn có chắc muốn xóa danh mục "${name}"?`;
            if (childrenCount > 0) {
                warnText = `CẢNH BÁO: Danh mục "${name}" có ${childrenCount} danh mục con. Khi xóa, các danh mục con sẽ được chuyển lên cấp cha!`;
            }

            Swal.fire({
                title: 'Xác nhận xóa?',
                text: warnText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/api/categories') }}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                loadCategoryTree();
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function() {
                            toastr.error('Có lỗi xảy ra khi xóa danh mục!');
                        }
                    });
                }
            });
        }

        function updateParentDropdownOptions() {
            const map = {};
            rawCategories.forEach(c => map[c.id] = c);

            let options = [{ text: '-- Là Danh mục Gốc (Cấp 1) --', value: '' }];

            let formatted = rawCategories.map(cat => {
                let path = cat.name;
                let p = cat.parent_id ? map[cat.parent_id] : null;
                while (p) {
                    path = p.name + ' > ' + path;
                    p = p.parent_id ? map[p.parent_id] : null;
                }
                return { id: cat.id, path: path };
            });

            formatted.sort((a, b) => a.path.localeCompare(b.path));

            formatted.forEach(item => {
                options.push({ text: item.path, value: String(item.id) });
            });

            if (ssParent) {
                const currentVal = ssParent.getSelected();
                ssParent.setData(options);
                if (currentVal && currentVal.length > 0) {
                    ssParent.setSelected(currentVal);
                }
            }
        }

        // ================= IMPORT / EXPORT EXCEL & CSV SYSTEM =================
        function exportCategoriesCsv() {
            const typeFilter = $('#catTypeFilter').val();
            let url = '{{ route("admin.api.categories.export") }}';
            if (typeFilter) {
                url += '?type=' + encodeURIComponent(typeFilter);
            }
            window.location.href = url;
        }

        function downloadSampleCsv() {
            const sampleData = [
                ['name', 'slug', 'parent', 'type', 'position', 'status', 'content'],
                ['Biến tần', 'bien-tan', '', 'product', '1', 'active', 'Giới thiệu về dòng biến tần chính hãng'],
                ['Biến tần 1 pha', 'bien-tan-1-pha', 'Biến tần', 'product', '2', 'active', 'Biến tần 1 pha chất lượng cao'],
                ['Biến tần 1 pha 220V', 'bien-tan-1-pha-220v', 'Biến tần > Biến tần 1 pha', 'product', '3', 'active', 'Biến tần 1 pha 220V siêu bền'],
                ['Cảm biến', 'cam-bien', '', 'product', '4', 'active', 'Các dòng cảm biến công nghiệp'],
                ['Cảm biến quang', 'cam-bien-quang', 'Cảm biến', 'product', '5', 'active', 'Cảm biến quang điện thu phát'],
                ['Tin tức công nghệ', 'tin-tuc-cong-nghe', '', 'post', '6', 'active', 'Tin tức tự động hóa']
            ];

            let csvContent = '\uFEFF'; // BOM UTF-8
            sampleData.forEach(row => {
                let escapedRow = row.map(val => `"${val.replace(/"/g, '""')}"`);
                csvContent += escapedRow.join(',') + '\r\n';
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'mau_nhap_danh_muc.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function openImportModal() {
            $('#importCsvFile').val('');
            $('#importStep1').show();
            $('#importStep2').hide();
            $('#importProgressWrapper').hide();
            $('#btnBackToStep1').hide();
            $('#btnReadHeaders').show().prop('disabled', false).html('Tiếp tục (Đọc cột) <i class="fa fa-arrow-right ml-1"></i>');
            $('#btnStartImport').hide().prop('disabled', false).html('<i class="fa fa-check-circle mr-1"></i> Bắt đầu Nhập dữ liệu');
            $('#btnCancelImport').prop('disabled', false);
            $('#btnCloseImportModal').prop('disabled', false);
            $('#statInserted').text('0');
            $('#statUpdated').text('0');
            $('#statSkipped').text('0');
            $('#importModal').modal('show');
        }

        function backToStep1() {
            $('#importStep1').show();
            $('#importStep2').hide();
            $('#btnBackToStep1').hide();
            $('#btnReadHeaders').show();
            $('#btnStartImport').hide();
        }

        function readCsvHeaders() {
            const fileInput = document.getElementById('importCsvFile');
            if (!fileInput.files.length) {
                toastr.error('Vui lòng chọn file CSV');
                return;
            }
            
            const file = fileInput.files[0];
            $('#btnReadHeaders').prop('disabled', true).text('Đang đọc file...');

            Papa.parse(file, {
                header: true,
                preview: 1,
                skipEmptyLines: true,
                complete: function(results) {
                    if(results.meta && results.meta.fields && results.meta.fields.length > 0) {
                        csvHeaders = results.meta.fields;
                        renderMappingTable();
                        $('#importStep1').hide();
                        $('#importStep2').show();
                        $('#btnBackToStep1').show();
                        $('#btnReadHeaders').hide();
                        $('#btnStartImport').show();
                    } else {
                        toastr.error('Không tìm thấy danh sách cột (Header) trong file CSV');
                        $('#btnReadHeaders').prop('disabled', false).html('Tiếp tục (Đọc cột) <i class="fa fa-arrow-right ml-1"></i>');
                    }
                },
                error: function(err) {
                    toastr.error('Lỗi khi đọc file CSV: ' + err);
                    $('#btnReadHeaders').prop('disabled', false).html('Tiếp tục (Đọc cột) <i class="fa fa-arrow-right ml-1"></i>');
                }
            });
        }

        function renderMappingTable() {
            const tbody = $('#mappingTable tbody');
            tbody.empty();
            
            SYSTEM_COLUMNS.forEach(col => {
                let options = `<option value="">-- Bỏ qua không nhập --</option>`;
                csvHeaders.forEach(header => {
                    const cleanH = header.trim().toLowerCase();
                    const cleanKey = col.key.trim().toLowerCase();
                    let selected = (cleanH === cleanKey) ? 'selected' : '';
                    options += `<option value="${escapeHtml(header)}" ${selected}>${escapeHtml(header)}</option>`;
                });
                
                let requiredMark = col.required ? ' <span class="text-danger font-weight-bold">*</span>' : '';
                
                tbody.append(`
                    <tr>
                        <td class="font-weight-bold text-dark">${col.label}${requiredMark}</td>
                        <td>
                            <select class="form-control form-control-sm mapping-select" data-sys-key="${col.key}">
                                ${options}
                            </select>
                        </td>
                    </tr>
                `);
            });
        }

        function startImport() {
            const fileInput = document.getElementById('importCsvFile');
            if (!fileInput.files.length) {
                toastr.error('Vui lòng chọn file');
                return;
            }
            const file = fileInput.files[0];
            const importMode = $('#importMode').val();
            
            // Thu thập mapping
            let mapping = {};
            let hasName = false;
            
            $('.mapping-select').each(function() {
                let sysKey = $(this).data('sys-key');
                let csvCol = $(this).val();
                if (csvCol) {
                    mapping[sysKey] = csvCol;
                    if (sysKey === 'name') hasName = true;
                }
            });
            
            if (!hasName) {
                toastr.error('Bạn bắt buộc phải ánh xạ cột "Tên danh mục"');
                return;
            }
            
            // Setup UI tiến trình
            $('#btnStartImport').prop('disabled', true).text('Đang xử lý...');
            $('#btnCancelImport').prop('disabled', true);
            $('#btnCloseImportModal').prop('disabled', true);
            $('#btnBackToStep1').hide();
            $('#importProgressWrapper').show();
            $('#importProgressBar').css('width', '0%');
            $('#importProgressText').text('Đang đọc toàn bộ file...');
            $('#importPercentBadge').text('0%');

            let totalInserted = 0;
            let totalUpdated = 0;
            let totalSkipped = 0;

            Papa.parse(file, {
                header: true,
                skipEmptyLines: true,
                complete: function(results) {
                    const originalData = results.data;
                    const totalRows = originalData.length;
                    
                    if (totalRows === 0) {
                        toastr.error('File CSV không có dòng dữ liệu nào');
                        resetImportUI();
                        return;
                    }
                    
                    // Transform data using mapping
                    const mappedData = [];
                    originalData.forEach(row => {
                        let newRow = {};
                        for (let sysKey in mapping) {
                            let csvCol = mapping[sysKey];
                            newRow[sysKey] = row[csvCol] !== undefined ? String(row[csvCol]).trim() : '';
                        }
                        if (newRow.name && newRow.name !== '') {
                            mappedData.push(newRow);
                        }
                    });

                    const totalMappedRows = mappedData.length;
                    if (totalMappedRows === 0) {
                        toastr.error('Không tìm thấy dòng dữ liệu nào có Tên danh mục hợp lệ');
                        resetImportUI();
                        return;
                    }

                    // Chia lô 1000 dòng
                    const batchSize = 1000;
                    const batches = [];
                    for (let i = 0; i < totalMappedRows; i += batchSize) {
                        batches.push(mappedData.slice(i, i + batchSize));
                    }

                    let currentBatch = 0;
                    $('#importProgressText').text(`Đang xử lý: 0 / ${totalMappedRows}`);

                    function processNextBatch() {
                        if (currentBatch >= batches.length) {
                            // Hoàn tất
                            toastr.success(`Nhập thành công! Thêm mới: ${totalInserted}, Cập nhật: ${totalUpdated}, Bỏ qua: ${totalSkipped}`);
                            resetImportUI();
                            loadCategoryTree();
                            setTimeout(() => {
                                $('#importModal').modal('hide');
                            }, 1200);
                            return;
                        }

                        $.ajax({
                            url: '{{ route("admin.api.categories.import.batch") }}',
                            type: 'POST',
                            data: JSON.stringify({ 
                                rows: batches[currentBatch],
                                mode: importMode 
                            }),
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success && response.stats) {
                                    totalInserted += response.stats.inserted;
                                    totalUpdated += response.stats.updated;
                                    totalSkipped += response.stats.skipped;
                                    
                                    $('#statInserted').text(totalInserted);
                                    $('#statUpdated').text(totalUpdated);
                                    $('#statSkipped').text(totalSkipped);
                                }
                            },
                            error: function(xhr) {
                                console.error('Batch error', xhr);
                                toastr.error('Lỗi khi xử lý lô ' + (currentBatch + 1));
                            },
                            complete: function() {
                                currentBatch++;
                                let processed = Math.min(currentBatch * batchSize, totalMappedRows);
                                let percent = Math.round((processed / totalMappedRows) * 100);
                                
                                $('#importProgressBar').css('width', percent + '%');
                                $('#importPercentBadge').text(percent + '%');
                                $('#importProgressText').text(`Đang xử lý: ${processed} / ${totalMappedRows}`);
                                
                                setTimeout(processNextBatch, 50);
                            }
                        });
                    }

                    // Chạy lô đầu tiên
                    processNextBatch();
                },
                error: function(err) {
                    toastr.error('Lỗi khi đọc file CSV: ' + err);
                    resetImportUI();
                }
            });
        }

        function resetImportUI() {
            $('#btnStartImport').prop('disabled', false).html('<i class="fa fa-check-circle mr-1"></i> Bắt đầu Nhập dữ liệu');
            $('#btnCancelImport').prop('disabled', false);
            $('#btnCloseImportModal').prop('disabled', false);
            $('#btnBackToStep1').show();
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        function escapeJs(str) {
            if (!str) return '';
            return str.replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '\\"');
        }
    </script>
@endsection
