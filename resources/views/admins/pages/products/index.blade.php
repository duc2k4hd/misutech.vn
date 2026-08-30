@extends('admins.layouts.master')

@section('title', 'Quản lý Sản phẩm')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .tox-tinymce {
            width: 100% !important;
        }
        .thumbnail-img {
            max-width: 50px;
            max-height: 50px;
            object-fit: contain;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .media-picker-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #fcfcfc;
            min-height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .media-picker-box:hover {
            border-color: #593bdb;
            background: #f4f2ff;
        }
        .media-preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
            cursor: grab;
            background: #fff;
        }
        .media-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .media-preview-item .remove-media {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 0, 0, 0.8);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            cursor: pointer;
            display: none;
        }
        .media-preview-item:hover .remove-media {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #productModal .modal-dialog {
            max-width: 90%;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #454545;
        }
        /* Media Manager Modal IFRAME */
        #mediaManagerModal .modal-dialog {
            max-width: 95%;
            height: 95%;
            margin: 20px auto;
        }
        #mediaManagerModal .modal-content {
            height: 100%;
        }

        /* --- PREMIUM TABLE STYLING --- */
        #productsTable {
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: -10px;
        }
        #productsTable thead th {
            border: none;
            text-transform: uppercase;
            font-size: 12px;
            font-weight: 600;
            color: #8898aa;
            letter-spacing: 0.5px;
            padding: 15px 20px;
            background: transparent;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        #productsTable tbody tr {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            border-radius: 10px;
            transition: all 0.25s ease;
        }
        #productsTable tbody tr:hover {
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.08);
            transform: translateY(-2px);
        }
        #productsTable tbody td {
            border-top: none;
            border-bottom: none;
            padding: 18px 20px;
            vertical-align: middle;
        }
        #productsTable tbody td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        #productsTable tbody td:last-child {
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }
        
        /* Modern Badges */
        .badge-soft-success {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }
        .badge-soft-danger {
            background-color: #fee2e2;
            color: #991b1b;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
        }

        /* Typography */
        .product-name-txt {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .product-sku-txt {
            font-size: 12px;
            color: #64748b;
        }
        .price-txt {
            font-weight: 700;
            color: #3b82f6;
            font-size: 14px;
        }

        /* Thumbnail Image */
        .thumbnail-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 10px;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .thumbnail-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            max-width: unset;
            max-height: unset;
            border-radius: 0;
            background: transparent;
        }
        .no-image-icon {
            color: #cbd5e1;
            font-size: 20px;
        }
        
        /* Action Buttons */
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.2s;
            margin-right: 8px;
            cursor: pointer;
        }
        .btn-edit {
            background: #eff6ff;
            color: #3b82f6;
        }
        .btn-edit:hover {
            background: #3b82f6;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
        }
        .btn-delete {
            background: #fef2f2;
            color: #ef4444;
        }
        .btn-delete:hover {
            background: #ef4444;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }
        
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #593bdb !important;
            color: #fff !important;
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(89, 59, 219, 0.3);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Quản lý Sản phẩm</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Sản phẩm</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-2">Danh sách Sản phẩm</h4>
                            <div class="btn-group btn-group-sm" role="group" id="filterStatusGroup">
                                <button type="button" class="btn btn-outline-primary active" onclick="setTrashFilter('all', this)">
                                    Tất cả (<span id="countAll">0</span>)
                                </button>
                                <button type="button" class="btn btn-outline-success" onclick="setTrashFilter('active', this)">
                                    Đang hoạt động (<span id="countActive">0</span>)
                                </button>
                                <button type="button" class="btn btn-outline-danger" onclick="setTrashFilter('trashed', this)">
                                    <i class="fa fa-trash"></i> Thùng rác (<span id="countTrashed">0</span>)
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-2 mt-md-0 flex-wrap">
                            <button type="button" class="btn btn-success mr-2 mb-1" id="btnBulkRestore" style="display: none;" onclick="restoreSelectedProducts()">
                                <i class="fa fa-undo"></i> Khôi phục (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-danger mr-2 mb-1" id="btnBulkDelete" style="display: none;" onclick="deleteSelectedProducts()">
                                <i class="fa fa-trash"></i> Chuyển thùng rác (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-outline-danger mr-2 mb-1" id="btnBulkForceDelete" style="display: none;" onclick="forceDeleteSelectedProducts()">
                                <i class="fa fa-times-circle"></i> Xóa vĩnh viễn (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-outline-success mr-2 mb-1" onclick="openExportModal()">
                                <i class="fa fa-download"></i> Xuất CSV
                            </button>
                            <button type="button" class="btn btn-outline-info mr-2 mb-1" onclick="openImportModal()">
                                <i class="fa fa-upload"></i> Nhập CSV
                            </button>
                            <button type="button" class="btn btn-primary mb-1" onclick="openModal()">
                                <i class="fa fa-plus"></i> Thêm mới
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Smart Selection Banner -->
                        <div id="selectAllBanner" class="alert alert-info py-2 px-3 mb-3 d-none justify-content-between align-items-center" style="border-radius: 8px; font-size: 13px; background: #e0f2fe; border-color: #bae6fd; color: #0369a1;">
                            <span>
                                <i class="fa fa-check-circle mr-1"></i> Đã chọn <strong id="selectedPageCount">0</strong> sản phẩm trên trang này.
                                <a href="javascript:void(0)" class="text-primary font-weight-bold ml-2" style="text-decoration: underline;" onclick="selectAllAcrossSystem()" id="btnSelectAllSystem">
                                    👉 Chọn tất cả <span id="totalSystemCount">57</span> sản phẩm trong hệ thống
                                </a>
                            </span>
                            <a href="javascript:void(0)" class="text-danger ml-3 font-weight-bold" onclick="clearAllSelections()" title="Bỏ chọn tất cả">Bỏ chọn</a>
                        </div>

                        <div class="table-responsive">
                            <table id="productsTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th style="width: 20px;"><input type="checkbox" id="checkAll" onclick="toggleCheckAll(this, event)"></th>
                                        <th>ID</th>
                                        <th>Ảnh</th>
                                        <th>Tên sản phẩm</th>
                                        <th>SKU</th>
                                        <th>Giá</th>
                                        <th>Danh mục</th>
                                        <th>Dòng SP (Series)</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm/Sửa Product -->
    <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Sản phẩm mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="productForm">
                    <div class="modal-body" style="background-color: #f4f6f9;">
                        <input type="hidden" id="product_id" name="id">
                        
                        <div class="row">
                            <!-- Cột trái: Thông tin chính -->
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="section-title">Thông tin cơ bản</h4>
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label>Tên sản phẩm <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name" name="name" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Đường dẫn (Slug)</label>
                                                <input type="text" class="form-control" id="slug" name="slug">
                                                <small class="text-muted">Để trống để tự tạo từ tên</small>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>SKU</label>
                                                <input type="text" class="form-control" id="sku" name="sku">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h4 class="section-title">Giá sản phẩm</h4>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Giá bán thường (đ) <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="price" name="price" required min="0" step="0.01">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Giá khuyến mãi (đ)</label>
                                                <input type="number" class="form-control" id="sale_price" name="sale_price" min="0" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h4 class="section-title">Nội dung</h4>
                                        <div class="form-group">
                                            <label>Mô tả ngắn</label>
                                            <textarea class="form-control tinymce" id="short_description" name="short_description"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Nội dung chi tiết</label>
                                            <textarea class="form-control tinymce" id="content" name="content"></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h4 class="section-title">SEO</h4>
                                        <div class="form-group">
                                            <label>Meta Title</label>
                                            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255">
                                        </div>
                                        <div class="form-group">
                                            <label>Meta Description</label>
                                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cột phải: Media & Phân loại -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h4 class="section-title">Trạng thái & Phân loại</h4>
                                        <div class="form-group">
                                            <label>Trạng thái</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Đang bán (Active)</option>
                                                <option value="draft">Bản nháp (Draft)</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Danh mục <span class="text-danger">*</span></label>
                                            <select class="form-control" id="category_id" name="category_id" required>
                                                <option data-placeholder="true" value="">Chọn danh mục</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Thương hiệu</label>
                                            <select class="form-control" id="brand_id" name="brand_id">
                                                <option data-placeholder="true" value="">Chọn thương hiệu</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Dòng sản phẩm (Series)</label>
                                            <select class="form-control" id="series_id" name="series_id">
                                                <option data-placeholder="true" value="">-- Không thuộc Series nào --</option>
                                                @foreach($series as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-body">
                                        <h4 class="section-title">Media (Ảnh sản phẩm)</h4>
                                        
                                        <label>Ảnh đại diện (Thumbnail)</label>
                                        <div class="media-picker-box mb-4" id="thumbnailPickerBox" onclick="openMediaPicker('thumbnail')">
                                            <i class="fa fa-image fa-3x text-muted mb-2"></i>
                                            <span>Click để chọn ảnh đại diện</span>
                                        </div>
                                        <input type="hidden" name="thumbnail_id" id="thumbnail_id">
                                        
                                        
                                        <label>Thư viện ảnh (Gallery)</label>
                                        <div id="galleryContainer" class="d-flex flex-wrap mb-2">
                                            <!-- Gallery items will be appended here -->
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-block" onclick="openMediaPicker('gallery', true)">
                                            <i class="fa fa-images"></i> Thêm ảnh thư viện
                                        </button>
                                        <input type="hidden" name="gallery_ids" id="gallery_ids">
                                        
                                        <hr>
                                        <label>Tài liệu (Catalog/Brochure)</label>
                                        <div id="catalogContainer" class="d-flex flex-wrap mb-2">
                                            <!-- Catalog items will be appended here -->
                                        </div>
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-block" onclick="openMediaPicker('catalog', true)">
                                            <i class="fa fa-file-pdf"></i> Thêm tài liệu Catalog
                                        </button>
                                        <input type="hidden" name="catalog_ids" id="catalog_ids">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Lưu Sản Phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Nhập Dữ liệu Siêu tốc</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Upload -->
                    <div id="importStep1">
                        <p class="text-muted">Chọn file CSV. Hệ thống hỗ trợ cập nhật một phần (chỉ cần ánh xạ cột cần sửa).</p>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">Chế độ Nhập (Import Mode)</label>
                            <select id="importMode" class="form-control">
                                <option value="upsert">Cập nhật & Thêm mới (Smart Upsert) - Mặc định</option>
                                <option value="insert_ignore">Chỉ Thêm mới (Bỏ qua SKU đã tồn tại)</option>
                            </select>
                            <small class="form-text text-muted">
                                - <b>Cập nhật & Thêm mới:</b> Sản phẩm đã có sẽ được cập nhật phần bị thay đổi. Sản phẩm mới sẽ được tạo.<br>
                                - <b>Chỉ Thêm mới:</b> Dùng khi bạn chỉ muốn import sản phẩm mới cứng. Trùng SKU sẽ bị bỏ qua nhanh chóng.
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">File dữ liệu (.csv)</label>
                            <input type="file" id="importCsvFile" accept=".csv" class="form-control-file">
                        </div>
                        <button type="button" class="btn btn-primary" id="btnReadHeaders" onclick="readCsvHeaders()">Tiếp tục (Đọc cột)</button>
                    </div>

                    <!-- Step 2: Mapping -->
                    <div id="importStep2" style="display:none;">
                        <h6 class="mb-3">Ghép Cột (Column Mapping)</h6>
                        <p class="text-info small">Chọn [Bỏ qua] nếu bạn không muốn cập nhật cột đó.</p>
                        <table class="table table-bordered table-sm" id="mappingTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Trường Hệ thống <span class="text-danger">*</span></th>
                                    <th>Cột trong File CSV</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Generated by JS -->
                            </tbody>
                        </table>
                        
                        <div id="importProgressWrapper" style="display:none;" class="mt-4">
                            <h6 class="mt-3 mb-1" id="importProgressText">Đang xử lý: 0 / 0</h6>
                            <div class="progress">
                                <div id="importProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <ul class="mt-2 list-unstyled" style="font-size: 13px; color: #666;">
                                <li>- Tạo mới: <span id="statInserted" class="font-weight-bold text-primary">0</span></li>
                                <li>- Cập nhật: <span id="statUpdated" class="font-weight-bold text-success">0</span></li>
                                <li>- Bỏ qua (Không đổi): <span id="statSkipped" class="font-weight-bold text-secondary">0</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btnCancelImport">Đóng</button>
                    <button type="button" class="btn btn-info" id="btnStartImport" onclick="startImport()" style="display:none;">Bắt đầu Nhập</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal chọn Media (Iframe) -->
    <div class="modal fade" id="mediaManagerModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn Media</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="mediaManagerIframe" src="" style="width: 100%; height: 100%; min-height: 500px; border: none;"></iframe>
                </div>
                <div class="modal-footer p-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export CSV -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tuỳ chọn Xuất CSV</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="exportForm">
                        <div class="form-group">
                            <label>Phương thức xuất</label>
                            <select class="form-control" id="export_type" name="export_type" onchange="toggleExportCategory()">
                                <option value="all">Xuất tất cả sản phẩm</option>
                                <option value="selected">Xuất các sản phẩm đã chọn (Tickbox)</option>
                                <option value="category">Xuất theo Danh mục</option>
                            </select>
                        </div>
                        <div class="form-group" id="export_category_group" style="display: none;">
                            <label>Chọn Danh mục</label>
                            <select class="form-control" id="export_category_id" name="export_category_id">
                                @foreach($categories as $c)
                                    <option value="{{ (is_array($c) ? $c['id'] : $c->id) }}">{{ (is_array($c) ? $c['name'] : $c->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-info">
                            Hệ thống sẽ tải xuống một file CSV chứa dữ liệu theo tuỳ chọn của bạn.
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="button" class="btn btn-success" onclick="executeExport()">Xuất File</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable -->
    <script src="{{ asset('admins/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <!-- Sortable for drag drop gallery -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <!-- PapaParse -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

    <script>
        let table, currentPickerMode;
        let galleryItems = [];
        let catalogItems = [];
        let ssCategory, ssBrand, ssSeries;
        let selectedProducts = new Set();

        $(document).ready(function() {
            // Initialize SlimSelect
            ssCategory = new SlimSelect({
                select: '#category_id',
                settings: { placeholderText: 'Chọn danh mục' }
            });
            ssBrand = new SlimSelect({
                select: '#brand_id',
                events: {
                    addable: function (value) {
                        return {
                            text: value,
                            value: value
                        };
                    }
                },
                settings: { placeholderText: 'Chọn hoặc gõ thương hiệu mới...', allowDeselect: true }
            });
            ssSeries = new SlimSelect({
                select: '#series_id',
                events: {
                    addable: function (value) {
                        return {
                            text: value,
                            value: value
                        };
                    }
                },
                settings: { placeholderText: 'Chọn hoặc gõ dòng sản phẩm (Series) mới...', allowDeselect: true }
            });
            
            initTinyMCE();
            
            let currentTrashFilter = 'all';
            window.currentTrashFilter = currentTrashFilter;

            table = $('#productsTable').DataTable({
                processing: true,
                serverSide: true, 
                lengthMenu: [[10, 50, 200, 500, 2000, 5000], [10, 50, 200, 500, 2000, 5000]],
                ajax: {
                    url: '{{ route("admin.api.products.list") }}',
                    data: function(d) {
                        d.trash_status = window.currentTrashFilter;
                    },
                    dataSrc: function(json) {
                        if (json.counts) {
                            $('#countAll').text(json.counts.all || 0);
                            $('#countActive').text(json.counts.active || 0);
                            $('#countTrashed').text(json.counts.trashed || 0);
                        }
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            let checked = selectedProducts.has(parseInt(data)) ? 'checked' : '';
                            return `<input type="checkbox" class="product-checkbox" value="${data}" ${checked} onclick="toggleProductCheck(this, ${data}, event)">`;
                        }
                    },
                    { 
                        data: 'id',
                        render: function(data) {
                            return `<span style="color:#94a3b8; font-weight:600; font-size:13px">#${data}</span>`;
                        }
                    },

                    { 
                        data: 'thumbnail_media',
                        render: function(data, type, row) {
                            if(data && data.length > 0) {
                                return `<div class="thumbnail-wrapper"><img src="${data[0].url}" class="thumbnail-img" alt="${row.name}"></div>`;
                            }
                            return `<div class="thumbnail-wrapper"><i class="fa fa-image no-image-icon"></i></div>`;
                        }
                    },
                    { 
                        data: 'name',
                        render: function(data, type, row) {
                            return `
                                <div class="product-name-txt">${data}</div>
                                <div class="product-sku-txt">Mã: ${row.sku || 'N/A'}</div>
                            `;
                        }
                    },
                    { 
                        data: 'sku', 
                        visible: false 
                    },
                    { 
                        data: 'price',
                        render: function(data) {
                            let priceFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data);
                            return `<span class="price-txt">${priceFormatted}</span>`;
                        }
                    },
                    { 
                        data: 'category',
                        render: function(data) {
                            return data ? `<span style="color:#475569; font-size:13px; font-weight:500;">${data.name}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    { 
                        data: 'series',
                        render: function(data) {
                            return data ? `<span class="badge badge-light" style="font-size:12px; font-weight:600; color:#593bdb; background:#f0effb;">${data.name}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data, type, row) {
                            if (row.is_trashed) {
                                return `<span class="badge-soft-danger"><i class="fa fa-trash mr-1"></i>Đã xóa mềm</span>`;
                            }
                            let badgeClass = data === 'active' ? 'badge-soft-success' : 'badge-soft-danger';
                            let text = data === 'active' ? 'Đang bán' : 'Bản nháp';
                            return `<span class="${badgeClass}">${text}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            if (row.is_trashed) {
                                return `
                                    <div class="d-flex">
                                        <button class="btn-action" style="background:#dcfce7; color:#166534;" onclick="restoreProduct(${row.id})" title="Khôi phục sản phẩm"><i class="fa fa-undo"></i></button>
                                        <button class="btn-action btn-delete" onclick="forceDeleteProduct(${row.id})" title="Xóa vĩnh viễn"><i class="fa fa-times-circle"></i></button>
                                    </div>
                                `;
                            }
                            return `
                                <div class="d-flex">
                                    <button class="btn-action btn-edit" onclick="editProduct(${row.id})" title="Chỉnh sửa"><i class="fa fa-pencil"></i></button>
                                    <button class="btn-action btn-delete" onclick="deleteProduct(${row.id})" title="Chuyển vào thùng rác"><i class="fa fa-trash"></i></button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    search: "Tìm kiếm:",
                    lengthMenu: "Hiển thị _MENU_ bản ghi",
                    info: "Hiển thị _START_ đến _END_ trong _TOTAL_ sản phẩm",
                    infoEmpty: "Không có dữ liệu",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Sau",
                        previous: "Trước"
                    }
                }
            });

            // Checkbox Events
            window.toggleCheckAll = function(el, e) {
                if (e) e.stopPropagation();
                let isChecked = $(el).is(':checked');
                $('.product-checkbox').prop('checked', isChecked);
                $('.product-checkbox').each(function() {
                    let val = parseInt($(this).val());
                    if (isChecked && !isNaN(val)) {
                        selectedProducts.add(val);
                    } else if (!isNaN(val)) {
                        selectedProducts.delete(val);
                    }
                });
                updateCheckAllState();
            };

            window.toggleProductCheck = function(el, id, e) {
                if (e) e.stopPropagation();
                let isChecked = $(el).is(':checked');
                let val = parseInt(id);
                if (isChecked && !isNaN(val)) {
                    selectedProducts.add(val);
                } else if (!isNaN(val)) {
                    selectedProducts.delete(val);
                }
                updateCheckAllState();
            };

            table.on('draw', function() {
                $('.product-checkbox').each(function() {
                    let val = parseInt($(this).val());
                    if (selectedProducts.has(val)) {
                        $(this).prop('checked', true);
                    }
                });
                updateCheckAllState();
            });
            
            $('#productModal').on('hidden.bs.modal', function () {
                $('#productForm')[0].reset();
                $('#product_id').val('');
                tinymce.get('short_description').setContent('');
                tinymce.get('content').setContent('');
                
                // Clear media
                $('#thumbnail_id').val('');
                $('#thumbnailPickerBox').html(`<i class="fa fa-image fa-3x text-muted mb-2"></i><span>Click để chọn ảnh đại diện</span>`);
                $('#thumbnailPickerBox').css('padding', '20px');
                
                galleryItems = [];
                catalogItems = [];
                renderGallery();
                renderCatalog();
            });

            // Fix scrolling issue when closing nested modal
            $('#mediaManagerModal').on('hidden.bs.modal', function () {
                if ($('#productModal').is(':visible')) {
                    $('body').addClass('modal-open');
                }
            });

            // Initialize Sortable for gallery
            new Sortable(document.getElementById('galleryContainer'), {
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function (evt) {
                    // Update galleryItems array order based on DOM
                    const newItems = [];
                    $('#galleryContainer .media-preview-item').each(function() {
                        const id = $(this).data('id');
                        const url = $(this).find('img').attr('src');
                        newItems.push({ id: parseInt(id), url: url });
                    });
                    galleryItems = newItems;
                    updateGalleryInput();
                }
            });
        });

        // ================= MEDIA PICKER LOGIC =================

        // Listen for messages from Media Manager iframe
        window.addEventListener('message', function(event) {
            // Validate origin if needed, e.g., if (event.origin !== "http://localhost:8000") return;
            if (event.data && event.data.type === 'media_selected') {
                const selectedMedia = event.data.data; // Array of selected files
                if (selectedMedia.length > 0) {
                    if (currentPickerMode === 'thumbnail') {
                        setThumbnail(selectedMedia[0]);
                    } else if (currentPickerMode === 'gallery') {
                        addToGallery(selectedMedia);
                    } else if (currentPickerMode === 'tinymce') {
                        window.tinymceCallback(selectedMedia[0].url, { alt: selectedMedia[0].name || '' });
                        window.tinymceCallback = null;
                    } else if (currentPickerMode === 'tinymce_custom') {
                        const f = selectedMedia[0];
                        const title = f.name ? f.name.replace(/\.[^/.]+$/, "") : 'No Title';
                        const html = `<figure class="misutech_product_figure" title="${title}">
    <img class="misutech_product_img" src="${f.url}" alt="${title}">
    <figcaption>${title}</figcaption>
</figure>`;
                        window.activeTinyMCE.insertContent(html);
                        window.activeTinyMCE = null;
                    }
                }
                $('#mediaManagerModal').modal('hide');
            }
        });

        function openMediaPicker(mode, multiple = false) {
            currentPickerMode = mode;
            let folder = 'clients/imgs/products';
            if (mode === 'catalog') {
                folder = 'clients/imgs/catalogs';
            }
            
            // Add ?picker=1&multiple=true to the media index route
            const url = `{{ route('admin.media.index') }}?picker=1&multiple=${multiple ? '1' : '0'}&folder=${folder}`;
            $('#mediaManagerIframe').attr('src', url);
            $('#mediaManagerModal').modal('show');
        }

        function setThumbnail(media) {
            $('#thumbnail_id').val(media.id);
            $('#thumbnailPickerBox').html(`<img src="${media.url}" style="max-width: 100%; max-height: 150px; object-fit: contain; border-radius: 4px;">`);
            $('#thumbnailPickerBox').css('padding', '5px');
        }

        function addToGallery(mediaList) {
            if (currentPickerMode === 'gallery') {
                mediaList.forEach(media => {
                    if (!galleryItems.some(item => item.id === media.id)) {
                        galleryItems.push({ id: media.id, url: media.url });
                    }
                });
                renderGallery();
            } else if (currentPickerMode === 'catalog') {
                mediaList.forEach(media => {
                    if (!catalogItems.some(item => item.id === media.id)) {
                        catalogItems.push({ id: media.id, url: media.url, filename: media.filename || 'Tài liệu ' + media.id });
                    }
                });
                renderCatalog();
            }
        }

        function renderGallery() {
            const container = $('#galleryContainer');
            container.empty();
            galleryItems.forEach(item => {
                container.append(`
                    <div class="media-preview-item" data-id="${item.id}">
                        <img src="${item.url}" alt="Gallery Image">
                        <button type="button" class="remove-media" onclick="removeGalleryItem(${item.id})">×</button>
                    </div>
                `);
            });
            updateGalleryInput();
        }

        function removeGalleryItem(id) {
            galleryItems = galleryItems.filter(item => item.id !== id);
            renderGallery();
        }

        function updateGalleryInput() {
            const ids = galleryItems.map(item => item.id);
            $('#gallery_ids').empty();
            let inputsHtml = '';
            ids.forEach(id => {
                inputsHtml += `<input type="hidden" name="gallery_ids[]" value="${id}">`;
            });
            $('#gallery_ids').html(inputsHtml);
        }

        function renderCatalog() {
            const container = $('#catalogContainer');
            container.empty();
            catalogItems.forEach((item, index) => {
                let html = `
                    <div class="gallery-item-wrapper m-1" style="position:relative; width: 100%; border: 1px solid #ddd; border-radius: 4px; padding: 5px; background: #f9f9f9; display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <i class="fa fa-file text-secondary mr-2"></i>
                            <a href="${item.url}" target="_blank" class="text-dark" style="font-size:12px;">${item.filename}</a>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="removeCatalogItem(${index})"><i class="fa fa-times"></i></button>
                    </div>
                `;
                container.append(html);
            });
            
            // Sortable catalog
            if(catalogItems.length > 1) {
                new Sortable(document.getElementById('catalogContainer'), {
                    animation: 150,
                    onEnd: function (evt) {
                        const itemEl = catalogItems.splice(evt.oldIndex, 1)[0];
                        catalogItems.splice(evt.newIndex, 0, itemEl);
                        updateCatalogInput();
                    }
                });
            }
            updateCatalogInput();
        }

        function removeCatalogItem(index) {
            catalogItems.splice(index, 1);
            renderCatalog();
            updateCatalogInput();
        }

        function updateCatalogInput() {
            const ids = catalogItems.map(item => item.id);
            $('#catalog_ids').empty();
            let inputsHtml = '';
            ids.forEach(id => {
                inputsHtml += `<input type="hidden" name="catalog_ids[]" value="${id}">`;
            });
            $('#catalog_ids').html(inputsHtml);
        }

        // ================= FORM & TINYMCE =================

        function initTinyMCE() {
            tinymce.init({
                selector: '.tinymce',
                min_height: 700,
                height: 700,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | customMedia image link code fullscreen',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                file_picker_types: 'image',
                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype === 'image') {
                        currentPickerMode = 'tinymce';
                        window.tinymceCallback = callback;
                        const url = `{{ route('admin.media.index') }}?picker=1&multiple=0&folder=clients/imgs/products`;
                        $('#mediaManagerIframe').attr('src', url);
                        
                        $('#mediaManagerModal').css('z-index', 100000).modal('show');
                    }
                },
                setup: function (editor) {
                    editor.ui.registry.addButton('customMedia', {
                        icon: 'gallery',
                        tooltip: 'Chèn Ảnh (Media Manager)',
                        onAction: function (_) {
                            currentPickerMode = 'tinymce_custom';
                            window.activeTinyMCE = editor;
                            const url = `{{ route('admin.media.index') }}?picker=1&multiple=0&folder=clients/imgs/products`;
                            $('#mediaManagerIframe').attr('src', url);
                            $('#mediaManagerModal').css('z-index', 100000).modal('show');
                        }
                    });

                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        }

        function openModal() {
            $('#productForm')[0].reset();
            $('#product_id').val('');
            $('#thumbnail_id').val('');
            $('#gallery_ids').empty();
            $('#thumbnailPickerBox').html(`<i class="fa fa-image fa-3x text-muted mb-2"></i><span>Click để chọn ảnh đại diện</span>`).css('padding', '20px');
            galleryItems = [];
            catalogItems = [];
            renderGallery();
            renderCatalog();
            
            tinymce.get('short_description').setContent('');
            tinymce.get('content').setContent('');
            
            ssCategory.setSelected('');
            ssBrand.setSelected('');
            ssSeries.setSelected('');

            $('#modalTitle').text('Thêm Sản phẩm mới');
            $('#productModal').modal('show');
        }

        function editProduct(id) {
            $.get(`{{ url('admin/api/products') }}/${id}`, function(response) {
                if(response.success) {
                    $('#modalTitle').text('Chỉnh sửa Sản phẩm');
                    const p = response.data;
                    
                    $('#product_id').val(p.id);
                    $('#name').val(p.name);
                    $('#slug').val(p.slug);
                    $('#sku').val(p.sku);
                    $('#price').val(p.price ? Math.round(p.price) : '');
                    $('#sale_price').val(p.sale_price ? Math.round(p.sale_price) : '');
                    
                    if (p.category_id) ssCategory.setSelected(p.category_id.toString());
                    else ssCategory.setSelected('');
                    
                    if (p.brand_id) ssBrand.setSelected(p.brand_id.toString());
                    else ssBrand.setSelected('');
                    
                    if (p.series_id) ssSeries.setSelected(p.series_id.toString());
                    else ssSeries.setSelected('');
                    
                    $('#status').val(p.status);
                    $('#meta_title').val(p.meta_title);
                    $('#meta_description').val(p.meta_description);
                    
                    if (p.short_description) tinymce.get('short_description').setContent(p.short_description);
                    if (p.content) tinymce.get('content').setContent(p.content);
                    
                    // Set Media
                    if (p.thumbnail_id && p.thumbnail_url) {
                        setThumbnail({ id: p.thumbnail_id, url: p.thumbnail_url });
                    }
                    
                    if (p.gallery && p.gallery.length > 0) {
                        galleryItems = p.gallery;
                        renderGallery();
                    }
                    
                    if (p.catalog && p.catalog.length > 0) {
                        catalogItems = p.catalog;
                        renderCatalog();
                    }
                    
                    $('#productModal').modal('show');
                } else {
                    toastr.error(response.message);
                }
            });
        }

        $('#productForm').on('submit', function(e) {
            e.preventDefault();
            
            tinymce.triggerSave();
            let formData = new FormData(this);
            let id = $('#product_id').val();
            let isUpdate = id !== '';
            
            let url = isUpdate ? `{{ url('admin/api/products') }}/${id}` : '{{ route("admin.api.products.store") }}';
            
            if (isUpdate) {
                formData.append('_method', 'PUT');
            }
            
            $('#btnSave').prop('disabled', true).text('Đang xử lý...');

            $.ajax({
                url: url,
                type: 'POST', // always POST for FormData, but _method overrides to PUT
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#productModal').modal('hide');
                        toastr.success(response.message);
                        table.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    if(xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for(let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                },
                complete: function() {
                    $('#btnSave').prop('disabled', false).text('Lưu Sản Phẩm');
                }
            });
        });

        let isSelectAllSystem = false;

        function selectAllAcrossSystem() {
            isSelectAllSystem = true;
            $('#btnSelectAllSystem').text('Đang tải danh sách...');
            
            $.get('{{ route("admin.api.products.list") }}', { length: -1, trash_status: window.currentTrashFilter }, function(res) {
                if (res.data) {
                    res.data.forEach(p => selectedProducts.add(parseInt(p.id)));
                    $('.product-checkbox').prop('checked', true);
                    updateCheckAllState();
                    toastr.success(`Đã chọn toàn bộ ${selectedProducts.size} sản phẩm trong hệ thống!`);
                }
            });
        }

        function clearAllSelections() {
            isSelectAllSystem = false;
            selectedProducts.clear();
            $('#checkAll, .product-checkbox').prop('checked', false);
            updateCheckAllState();
        }

        function setTrashFilter(status, btn) {
            window.currentTrashFilter = status;
            $('#filterStatusGroup button').removeClass('active');
            $(btn).addClass('active');
            clearAllSelections();
            table.ajax.reload();
        }

        function updateCheckAllState() {
            let count = selectedProducts.size;
            $('.selectedCount').text(count);

            if (count > 0) {
                if (window.currentTrashFilter === 'trashed') {
                    $('#btnBulkRestore').show();
                    $('#btnBulkForceDelete').show();
                    $('#btnBulkDelete').hide();
                } else if (window.currentTrashFilter === 'active') {
                    $('#btnBulkRestore').hide();
                    $('#btnBulkForceDelete').hide();
                    $('#btnBulkDelete').show();
                } else {
                    $('#btnBulkRestore').show();
                    $('#btnBulkDelete').show();
                    $('#btnBulkForceDelete').show();
                }

                let totalOnPage = $('.product-checkbox').length;
                let checkedOnPage = $('.product-checkbox:checked').length;
                
                let totalInSystem = parseInt($('#countAll').text()) || 0;
                if (window.currentTrashFilter === 'active') totalInSystem = parseInt($('#countActive').text()) || 0;
                if (window.currentTrashFilter === 'trashed') totalInSystem = parseInt($('#countTrashed').text()) || 0;

                if (checkedOnPage === totalOnPage && totalOnPage < totalInSystem && !isSelectAllSystem && checkedOnPage > 0) {
                    $('#selectAllBanner').removeClass('d-none').addClass('d-flex');
                    $('#selectedPageCount').text(checkedOnPage);
                    $('#totalSystemCount').text(totalInSystem);
                } else {
                    $('#selectAllBanner').addClass('d-none').removeClass('d-flex');
                }
            } else {
                $('#btnBulkRestore').hide();
                $('#btnBulkDelete').hide();
                $('#btnBulkForceDelete').hide();
                $('#selectAllBanner').addClass('d-none').removeClass('d-flex');
            }

            let totalOnPage = $('.product-checkbox').length;
            let checkedOnPage = $('.product-checkbox:checked').length;
            $('#checkAll').prop('checked', totalOnPage > 0 && totalOnPage === checkedOnPage);
        }

        /**
         * Thực thi tác vụ hàng loạt theo từng batch kèm thanh tiến trình Progress Bar (Tagbar) thời gian thực.
         */
        async function executeBatchActionWithProgress({ ids, url, title, actionName, barClass = 'bg-danger', onComplete }) {
            if (!ids || ids.length === 0) return;

            const total = ids.length;
            const batchSize = 100; // 100 mục mỗi đợt, siêu nhanh
            const chunks = [];
            for (let i = 0; i < total; i += batchSize) {
                chunks.push(ids.slice(i, i + batchSize));
            }

            // Hiển thị SweetAlert2 chứa thanh Progress Bar (Tagbar)
            Swal.fire({
                title: title || 'Đang xử lý dữ liệu...',
                html: `
                    <div class="my-3">
                        <div class="progress" style="height: 24px; border-radius: 12px; background-color: #f1f5f9; padding: 2px; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                            <div id="batchProgressBar" class="progress-bar progress-bar-striped progress-bar-animated ${barClass}" 
                                 role="progressbar" style="width: 0%; border-radius: 10px; font-weight: 700; font-size: 12px; transition: width 0.3s ease;">
                                0%
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 13.5px; color: #475569;">
                            <span id="batchProgressStatus"><i class="fa fa-spinner fa-spin mr-1"></i> Đang chuẩn bị xử lý...</span>
                            <strong id="batchProgressCount" style="font-size: 14px; color: #1e293b;">0 / ${total}</strong>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            });

            let processedCount = 0;
            let failedBatches = 0;

            for (let i = 0; i < chunks.length; i++) {
                const chunk = chunks[i];
                try {
                    const res = await $.ajax({
                        url: url,
                        type: 'POST',
                        data: JSON.stringify({ ids: chunk }),
                        contentType: 'application/json',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });

                    if (res.success) {
                        // Ưu tiên dùng count từ server, fallback về chunk.length
                        processedCount += (res.count != null ? res.count : chunk.length);
                    } else {
                        failedBatches++;
                        console.warn(`Batch ${i+1} failed:`, res.message);
                    }
                } catch (err) {
                    console.error(`Batch ${i+1} error:`, err.status, err.responseText);
                    failedBatches++;
                }

                const percent = Math.min(100, Math.round((processedCount / total) * 100));
                $('#batchProgressBar').css('width', `${percent}%`).text(`${percent}%`);
                $('#batchProgressCount').text(`${processedCount} / ${total}`);
                $('#batchProgressStatus').html(`<i class="fa fa-spinner fa-spin mr-1"></i> Đang xử lý đợt ${i + 1}/${chunks.length}...`);
            }

            // Hoàn tất tiến trình
            $('#batchProgressBar').css('width', '100%').text('100%');
            $('#batchProgressCount').text(`${total} / ${total}`);
            $('#batchProgressStatus').html('<i class="fa fa-check-circle text-success mr-1"></i> Đã xử lý xong toàn bộ!');

            setTimeout(() => {
                Swal.fire({
                    title: 'Hoàn tất!',
                    text: `Đã ${actionName} thành công ${processedCount}/${total} sản phẩm!`,
                    icon: 'success',
                    type: 'success',
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#3085d6',
                    timer: 2500,
                    timerProgressBar: true
                });

                if (typeof onComplete === 'function') {
                    onComplete();
                }
            }, 300);
        }

        function showProcessingModal(title, text) {
            Swal.fire({
                title: title || 'Đang xử lý dữ liệu...',
                html: `
                    <div class="d-flex flex-column align-items-center justify-content-center my-3">
                        <div class="spinner-border text-primary mb-3" style="width: 3.5rem; height: 3.5rem;" role="status">
                            <span class="sr-only">Đang xử lý...</span>
                        </div>
                        <div style="font-size: 14.5px; color: #475569; line-height: 1.6;">${text || 'Hệ thống đang thực hiện thao tác. Vui lòng không đóng hoặc tải lại trang...'}</div>
                    </div>
                `,
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false
            });
        }

        function deleteProduct(id) {
            Swal.fire({
                title: 'Chuyển vào thùng rác?',
                text: "Sản phẩm này sẽ bị xóa mềm và chuyển vào thùng rác.",
                type: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang chuyển vào thùng rác...', 'Đang xử lý dữ liệu sản phẩm...');
                    $.ajax({
                        url: `{{ url('admin/api/products') }}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: response.message,
                                    icon: 'success',
                                    type: 'success',
                                    confirmButtonText: 'Đóng',
                                    confirmButtonColor: '#3085d6',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                selectedProducts.delete(parseInt(id));
                                updateCheckAllState();
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Lỗi!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', 'Lỗi khi xóa: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                        }
                    });
                }
            });
        }

        function restoreProduct(id) {
            Swal.fire({
                title: 'Khôi phục sản phẩm?',
                text: "Sản phẩm sẽ được đưa trở lại danh sách hoạt động.",
                type: 'question',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Khôi phục ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang khôi phục...', 'Đang đưa sản phẩm trở lại hoạt động...');
                    $.ajax({
                        url: `{{ url('admin/api/products') }}/${id}/restore`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Thành công!',
                                    text: response.message,
                                    icon: 'success',
                                    type: 'success',
                                    confirmButtonText: 'Đóng',
                                    confirmButtonColor: '#3085d6',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                selectedProducts.delete(parseInt(id));
                                updateCheckAllState();
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Lỗi!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', 'Lỗi khi khôi phục: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                        }
                    });
                }
            });
        }

        function forceDeleteProduct(id) {
            Swal.fire({
                title: 'XÓA VĨNH VIỄN?',
                text: "Hành động này KHÔNG THỂ khôi phục lại! Dữ liệu sản phẩm và ảnh liên quan sẽ bị xóa hoàn toàn.",
                type: 'error',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa vĩnh viễn',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang xóa vĩnh viễn...', 'Đang xóa toàn bộ dữ liệu sản phẩm khỏi hệ thống...');
                    $.ajax({
                        url: `{{ url('admin/api/products') }}/${id}/force-delete`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: 'Đã xóa vĩnh viễn!',
                                    text: response.message,
                                    icon: 'success',
                                    type: 'success',
                                    confirmButtonText: 'Đóng',
                                    confirmButtonColor: '#3085d6',
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                selectedProducts.delete(parseInt(id));
                                updateCheckAllState();
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Lỗi!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', 'Lỗi khi xóa vĩnh viễn: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                        }
                    });
                }
            });
        }

        function deleteSelectedProducts() {
            let ids = Array.from(selectedProducts);
            if (ids.length === 0) return;

            Swal.fire({
                title: `Chuyển ${ids.length} sản phẩm vào thùng rác?`,
                text: "Các sản phẩm đã chọn sẽ bị xóa mềm.",
                type: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    executeBatchActionWithProgress({
                        ids: ids,
                        url: `{{ route('admin.api.products.bulk.destroy') }}`,
                        title: `Đang chuyển ${ids.length} sản phẩm vào thùng rác...`,
                        actionName: 'chuyển thùng rác',
                        barClass: 'bg-warning',
                        onComplete: function() {
                            selectedProducts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        function restoreSelectedProducts() {
            let ids = Array.from(selectedProducts);
            if (ids.length === 0) return;

            Swal.fire({
                title: `Khôi phục ${ids.length} sản phẩm?`,
                text: "Các sản phẩm đã chọn sẽ được đưa trở lại danh sách hoạt động.",
                type: 'question',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Khôi phục tất cả',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    executeBatchActionWithProgress({
                        ids: ids,
                        url: `{{ route('admin.api.products.bulk.restore') }}`,
                        title: `Đang khôi phục ${ids.length} sản phẩm...`,
                        actionName: 'khôi phục',
                        barClass: 'bg-success',
                        onComplete: function() {
                            selectedProducts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        function forceDeleteSelectedProducts() {
            let ids = Array.from(selectedProducts);
            if (ids.length === 0) return;

            Swal.fire({
                title: `XÓA VĨNH VIỄN ${ids.length} sản phẩm?`,
                text: "Hành động này KHÔNG THỂ HOÀN TÁC! Dữ liệu sản phẩm và quan hệ ảnh liên quan sẽ bị xóa hoàn toàn.",
                type: 'error',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa vĩnh viễn',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    executeBatchActionWithProgress({
                        ids: ids,
                        url: `{{ route('admin.api.products.bulk.force.destroy') }}`,
                        title: `Đang xóa vĩnh viễn ${ids.length} sản phẩm...`,
                        actionName: 'xóa vĩnh viễn',
                        barClass: 'bg-danger',
                        onComplete: function() {
                            selectedProducts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        // ================= IMPORT/EXPORT SYSTEM =================
        const SYSTEM_COLUMNS = [
            { key: 'sku', label: 'Mã SKU (Bắt buộc)', required: true },
            { key: 'name', label: 'Tên sản phẩm', required: false },
            { key: 'slug', label: 'Slug (Đường dẫn tĩnh)', required: false },
            { key: 'price', label: 'Giá bán thường (đ)', required: false },
            { key: 'sale_price', label: 'Giá khuyến mãi (đ)', required: false },
            { key: 'short_description', label: 'Mô tả ngắn', required: false },
            { key: 'content', label: 'Nội dung chi tiết (HTML)', required: false },
            { key: 'category', label: 'Tên Danh mục (VD: Biến tần, Cảm biến quang...)', required: false },
            { key: 'brand', label: 'Tên Thương hiệu (VD: Omron, Autonics...)', required: false },
            { key: 'series', label: 'Tên Dòng sản phẩm / Series (VD: Omron E3Z-D...)', required: false },
            { key: 'status', label: 'Trạng thái (active / draft)', required: false },
            { key: 'published_at', label: 'Ngày xuất bản (VD: 2026-08-29 12:00:00)', required: false },
            { key: 'meta_title', label: 'Tiêu đề SEO (Meta Title)', required: false },
            { key: 'meta_description', label: 'Mô tả SEO (Meta Description)', required: false },
            { key: 'image', label: 'Ảnh Đại diện Thumbnail (Tên file, VD: sensor-1.jpg)', required: false },
            { key: 'gallery', label: 'Ảnh Thư viện Gallery (Tên file, cách nhau bằng dấu phẩy)', required: false },
            { key: 'catalog', label: 'Tài liệu Catalog/PDF (Tên file, cách nhau bằng dấu phẩy)', required: false }
        ];

        let csvHeaders = [];

        function exportCsv() {
            window.location.href = '{{ route("admin.api.products.export") }}';
        }

        function openImportModal() {
            $('#importCsvFile').val('');
            $('#importStep1').show();
            $('#importStep2').hide();
            $('#importProgressWrapper').hide();
            $('#btnReadHeaders').prop('disabled', false).text('Tiếp tục (Đọc cột)');
            $('#btnStartImport').hide().prop('disabled', false).text('Bắt đầu Nhập');
            $('#btnCancelImport').prop('disabled', false);
            $('#statInserted').text('0');
            $('#statUpdated').text('0');
            $('#statSkipped').text('0');
            $('#importModal').modal('show');
        }

        function readCsvHeaders() {
            const fileInput = document.getElementById('importCsvFile');
            if (!fileInput.files.length) {
                toastr.error('Vui lòng chọn file CSV');
                return;
            }
            
            const file = fileInput.files[0];
            $('#btnReadHeaders').prop('disabled', true).text('Đang đọc...');

            // Just read the first row for headers
            Papa.parse(file, {
                header: true,
                preview: 1, // Read only first line of data
                skipEmptyLines: true,
                complete: function(results) {
                    if(results.meta && results.meta.fields) {
                        csvHeaders = results.meta.fields;
                        renderMappingTable();
                        $('#importStep1').hide();
                        $('#importStep2').show();
                        $('#btnStartImport').show();
                    } else {
                        toastr.error('Không tìm thấy cột (header) trong file CSV');
                        $('#btnReadHeaders').prop('disabled', false).text('Tiếp tục (Đọc cột)');
                    }
                },
                error: function(err) {
                    toastr.error('Lỗi đọc file');
                    $('#btnReadHeaders').prop('disabled', false).text('Tiếp tục (Đọc cột)');
                }
            });
        }

        function renderMappingTable() {
            const tbody = $('#mappingTable tbody');
            tbody.empty();
            
            SYSTEM_COLUMNS.forEach(col => {
                let options = `<option value="">-- Bỏ qua --</option>`;
                csvHeaders.forEach(header => {
                    // Auto-select if exact match
                    let selected = (header === col.key) ? 'selected' : '';
                    options += `<option value="${header}" ${selected}>${header}</option>`;
                });
                
                let requiredMark = col.required ? ' <span class="text-danger">*</span>' : '';
                
                tbody.append(`
                    <tr>
                        <td style="vertical-align: middle;">${col.label}${requiredMark}</td>
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
            const file = fileInput.files[0];
            const importMode = $('#importMode').val();
            
            // Collect mapping
            let mapping = {};
            let hasSku = false;
            
            $('.mapping-select').each(function() {
                let sysKey = $(this).data('sys-key');
                let csvCol = $(this).val();
                if (csvCol) {
                    mapping[sysKey] = csvCol;
                    if (sysKey === 'sku') hasSku = true;
                }
            });
            
            if (!hasSku) {
                toastr.error('Bạn bắt buộc phải ánh xạ cột SKU');
                return;
            }
            
            // UI setup
            $('#btnStartImport').prop('disabled', true).text('Đang xử lý...');
            $('#btnCancelImport').prop('disabled', true);
            $('#importProgressWrapper').show();
            $('#importProgressBar').css('width', '0%');
            $('#importProgressText').text('Đang đọc toàn bộ file CSV...');

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
                        toastr.error('File CSV trống');
                        resetImportUI();
                        return;
                    }
                    
                    // Transform data using mapping
                    const mappedData = [];
                    originalData.forEach(row => {
                        let newRow = {};
                        for (let sysKey in mapping) {
                            let csvCol = mapping[sysKey];
                            newRow[sysKey] = row[csvCol];
                        }
                        // Only add if SKU exists
                        if (newRow.sku) {
                            mappedData.push(newRow);
                        }
                    });

                    const totalMappedRows = mappedData.length;

                    const batchSize = 1000;
                    const batches = [];
                    for (let i = 0; i < totalMappedRows; i += batchSize) {
                        batches.push(mappedData.slice(i, i + batchSize));
                    }

                    let currentBatch = 0;
                    
                    $('#importProgressText').text(`Đang xử lý: 0 / ${totalMappedRows}`);

                    function processNextBatch() {
                        if (currentBatch >= batches.length) {
                            // Done
                            $('#btnStartImport').hide().prop('disabled', true);
                            $('#btnCancelImport').prop('disabled', false).text('Đóng');
                            $('#importProgressBar').removeClass('progress-bar-animated').addClass('bg-success').css('width', '100%');
                            $('#importProgressText').html(`<span class="text-success font-weight-bold"><i class="fa fa-check-circle mr-1"></i> Đã hoàn thành nhập toàn bộ ${totalMappedRows} sản phẩm!</span>`);

                            table.ajax.reload(null, false);

                            Swal.fire({
                                title: 'Nhập dữ liệu thành công!',
                                html: `
                                    <div class="text-left mt-2 p-3" style="background:#f0fdf4; border-radius:8px; border:1px solid #bbf7d0; font-size:14px;">
                                        <p class="mb-1 text-success font-weight-bold">✔ Tổng số sản phẩm đã xử lý: <strong>${totalMappedRows}</strong></p>
                                        <p class="mb-1 text-primary"><i class="fa fa-plus-circle mr-1"></i> Tạo mới: <strong>${totalInserted}</strong> sản phẩm</p>
                                        <p class="mb-1 text-info"><i class="fa fa-pencil mr-1"></i> Cập nhật: <strong>${totalUpdated}</strong> sản phẩm</p>
                                        <p class="mb-0 text-muted"><i class="fa fa-minus-circle mr-1"></i> Bỏ qua: <strong>${totalSkipped}</strong> sản phẩm</p>
                                    </div>
                                `,
                                type: 'success',
                                icon: 'success',
                                confirmButtonColor: '#28a745',
                                confirmButtonText: 'Xem danh sách sản phẩm'
                            }).then(() => {
                                $('#importModal').modal('hide');
                            });
                            return;
                        }

                        $.ajax({
                            url: '{{ route("admin.api.products.import.batch") }}',
                            type: 'POST',
                            data: JSON.stringify({ 
                                rows: batches[currentBatch],
                                mode: importMode 
                            }),
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                                let percent = (processed / totalMappedRows) * 100;
                                
                                $('#importProgressBar').css('width', percent + '%');
                                $('#importProgressText').text(`Đang xử lý: ${processed} / ${totalMappedRows}`);
                                
                                setTimeout(processNextBatch, 50);
                            }
                        });
                    }

                    // Start recursive batching
                    processNextBatch();
                },
                error: function(err) {
                    toastr.error('Lỗi khi đọc file CSV');
                    resetImportUI();
                }
            });
        }
        
        function resetImportUI() {
            $('#btnStartImport').prop('disabled', false).text('Bắt đầu Nhập');
            $('#btnCancelImport').prop('disabled', false);
        }

        // Export Logic
        function openExportModal() {
            if (selectedProducts.size > 0) {
                $('#export_type').val('selected');
            } else {
                $('#export_type').val('all');
            }
            $('#exportModal').modal('show');
            toggleExportCategory();
        }

        function toggleExportCategory() {
            let type = $('#export_type').val();
            if (type === 'category') {
                $('#export_category_group').show();
            } else {
                $('#export_category_group').hide();
            }
        }

        function executeExport() {
            let type = $('#export_type').val();
            let categoryId = $('#export_category_id').val();
            let ids = Array.from(selectedProducts).join(',');

            if (type === 'selected' && ids.length === 0) {
                toastr.warning('Bạn chưa chọn sản phẩm nào để xuất!');
                return;
            }

            let url = '{{ route("admin.api.products.export") }}?type=' + type;
            if (type === 'selected') url += '&ids=' + ids;
            if (type === 'category') url += '&category_id=' + categoryId;

            // Trigger download
            window.location.href = url;
            $('#exportModal').modal('hide');
        }
    </script>
@endsection
