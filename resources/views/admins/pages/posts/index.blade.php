@extends('admins.layouts.master')
@section('title', 'Quản lý Bài viết')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .tox-tinymce {
            width: 100% !important;
        }
        /* Custom UI for Gallery Picker */
        .gallery-box {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 10px;
            min-height: 120px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .gallery-item {
            position: relative;
            width: 100px;
            height: 100px;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background: #f8f9fa;
            cursor: move;
        }
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .gallery-item .remove-btn {
            position: absolute;
            top: 4px;
            right: 4px;
            background: rgba(255, 0, 0, 0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            font-size: 12px;
            line-height: 22px;
            text-align: center;
            cursor: pointer;
            z-index: 10;
        }
        .gallery-add-btn {
            width: 100px;
            height: 100px;
            border: 1px dashed #aaa;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #aaa;
            font-size: 24px;
            background: #fafafa;
        }
        .gallery-add-btn:hover {
            background: #eee;
            color: #777;
        }
        /* Product Modal */
        #postModal .modal-dialog {
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
        #postsTable {
            border-collapse: separate;
            border-spacing: 0 12px;
            margin-top: -10px;
        }
        #postsTable thead th {
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
        #postsTable tbody tr {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
            border-radius: 10px;
            transition: all 0.25s ease;
        }
        #postsTable tbody tr:hover {
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.08);
            transform: translateY(-2px);
        }
        #postsTable tbody td {
            border-top: none;
            border-bottom: none;
            padding: 18px 20px;
            vertical-align: middle;
        }
        #postsTable tbody td:first-child {
            border-top-left-radius: 10px;
            border-bottom-left-radius: 10px;
        }
        #postsTable tbody td:last-child {
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
        .post-title-txt {
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
            margin-bottom: 3px;
        }
        .post-slug-txt {
            font-size: 12px;
            color: #64748b;
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
        .btn-restore {
            background: #ecfdf5;
            color: #10b981;
        }
        .btn-restore:hover {
            background: #10b981;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .btn-force-delete {
            background: #fff1f2;
            color: #e11d48;
        }
        .btn-force-delete:hover {
            background: #e11d48;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(225, 29, 72, 0.3);
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
                    <h4>Quản lý Bài viết</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Bài viết</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                        <div>
                            <h4 class="card-title mb-2">Danh sách Bài viết</h4>
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
                        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                            <button type="button" class="btn btn-success" id="btnBulkRestore" style="display: none;" onclick="restoreSelectedPosts()">
                                <i class="fa fa-undo mr-1"></i> Khôi phục (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-danger" id="btnBulkDelete" style="display: none;" onclick="deleteSelectedPosts()">
                                <i class="fa fa-trash mr-1"></i> Chuyển thùng rác (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="btnBulkForceDelete" style="display: none;" onclick="forceDeleteSelectedPosts()">
                                <i class="fa fa-times-circle mr-1"></i> Xóa vĩnh viễn (<span class="selectedCount">0</span>)
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="openExportModal()">
                                <i class="fa fa-download mr-1"></i> Xuất Excel/CSV
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="openImportModal()">
                                <i class="fa fa-upload mr-1"></i> Nhập Excel/CSV
                            </button>
                            <button type="button" class="btn btn-primary" onclick="openModal()">
                                <i class="fa fa-plus mr-1"></i> Thêm mới
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="postsTable" class="display" style="min-width: 845px">
                                <thead>
                                    <tr>
                                        <th style="width: 20px;"><input type="checkbox" id="checkAll" onclick="toggleCheckAll(this, event)"></th>
                                        <th>ID</th>
                                        <th>Ảnh</th>
                                        <th>Tiêu đề</th>
                                        <th>Danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
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

    <!-- Modal Create/Edit -->
    <div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form id="postForm" onsubmit="savePost(event)">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTitle">Thêm Bài viết Mới</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="post_id" name="id">
                        
                        <div class="row">
                            <!-- Left Column: Main info -->
                            <div class="col-md-8">
                                <h4 class="section-title">Thông tin chung</h4>
                                <div class="form-group">
                                    <label>Tiêu đề bài viết <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label>Slug (Tùy chọn)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Để trống tự tạo từ tiêu đề">
                                </div>
                                <div class="form-group">
                                    <label>Mô tả ngắn</label>
                                    <textarea class="form-control" id="summary" name="summary" rows="3"></textarea>
                                </div>
                                
                                <h4 class="section-title mt-4">Nội dung chi tiết <span class="text-danger">*</span></h4>
                                <div class="form-group">
                                    <textarea class="form-control tinymce" id="content" name="content"></textarea>
                                </div>

                                <h4 class="section-title mt-4">SEO Metadata</h4>
                                <div class="form-group">
                                    <label>Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title">
                                </div>
                                <div class="form-group">
                                    <label>Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3"></textarea>
                                </div>
                            </div>
                            
                            <!-- Right Column: Settings, Images -->
                            <div class="col-md-4">
                                <h4 class="section-title">Phân loại & Trạng thái</h4>
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="published">Đang xuất bản</option>
                                        <option value="draft">Bản nháp</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option data-placeholder="true" value="">Chọn danh mục</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <h4 class="section-title mt-4">Hình ảnh</h4>
                                <div class="form-group">
                                    <label>Ảnh đại diện (Thumbnail)</label>
                                    <input type="hidden" id="thumbnail_id" name="thumbnail_id">
                                    <div class="text-center" id="thumbnailPickerBox" onclick="openMediaPicker('thumbnail')" style="cursor: pointer; border: 2px dashed #ddd; padding: 20px; border-radius: 8px; background: #f9f9f9;">
                                        <i class="fa fa-image fa-3x text-muted mb-2"></i>
                                        <div class="mt-2 text-muted">Click để chọn ảnh đại diện</div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger light" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="btnSave">Lưu Bài viết</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Media Manager IFRAME Modal -->
    <div class="modal fade" id="mediaManagerModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Media Manager</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="mediaManagerIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Posts Modal (Client-side PapaParse & Batch Processing) -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" style="color: #0f172a;">
                        <i class="fa fa-file-excel-o text-success mr-2"></i>Nhập Bài viết từ file Excel / CSV
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
                                <option value="insert">2. Chỉ Thêm mới (Bỏ qua các bài viết đã tồn tại trùng Slug)</option>
                                <option value="update">3. Chỉ Cập nhật (Chỉ sửa bài viết đang có trong hệ thống)</option>
                            </select>
                            <small class="form-text text-muted mt-2">
                                • Cột <b>category</b> hỗ trợ định dạng phân cấp: <code>Tin tức > Tin công nghệ</code> (Hệ thống sẽ tự động tạo mới danh mục bài viết nếu chưa tồn tại).<br>
                                • Cột <b>image</b> nhập tên file ảnh trong kho Media (VD: <code>tin-tuc-1.jpg</code>).
                            </small>
                        </div>
                        <div class="form-group mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="font-weight-bold text-dark mb-0">Chọn file dữ liệu (.csv)</label>
                                <button type="button" class="btn btn-sm btn-link text-primary font-weight-bold p-0" onclick="downloadSamplePostCsv()">
                                    <i class="fa fa-download"></i> Tải file mẫu chuẩn UTF-8
                                </button>
                            </div>
                            <input type="file" id="importCsvFile" accept=".csv, .txt" class="form-control-file p-2 border rounded bg-light">
                        </div>
                    </div>

                    {{-- Bước 2: Map Cột --}}
                    <div id="importStep2" style="display: none;">
                        <div class="alert alert-info py-2 px-3 mb-3" style="font-size: 13px;">
                            <i class="fa fa-info-circle mr-1"></i> Khớp các cột từ file Excel của bạn với cột bài viết tương ứng:
                        </div>
                        <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                            <table class="table table-bordered table-sm" id="mappingTable">
                                <thead>
                                    <tr class="bg-light">
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
                    <button type="button" class="btn btn-primary font-weight-bold" id="btnReadHeaders" onclick="readPostCsvHeaders()">
                        Tiếp tục (Đọc cột) <i class="fa fa-arrow-right ml-1"></i>
                    </button>
                    <button type="button" class="btn btn-success font-weight-bold" id="btnStartImport" onclick="startPostImport()" style="display: none;">
                        <i class="fa fa-check-circle mr-1"></i> Bắt đầu Nhập dữ liệu
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tùy Chọn Xuất Bài Viết (Chọn Cột & Chọn Danh Mục) -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" style="color: #0f172a;">
                        <i class="fa fa-download text-success mr-2"></i>Tùy chọn Xuất Bài viết Excel / CSV
                    </h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    {{-- 1. Chọn Danh mục xuất --}}
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">1. Chọn Danh mục cần xuất:</label>
                        <select id="exportCategoryId" class="form-control">
                            <option value="">-- Tất cả danh mục bài viết (Mặc định) --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Nếu không chọn, hệ thống sẽ xuất tất cả bài viết.</small>
                    </div>

                    {{-- 2. Chọn Cột xuất --}}
                    <div class="form-group mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold text-dark mb-0">2. Chọn các Cột cần xuất:</label>
                            <div>
                                <button type="button" class="btn btn-xs btn-link text-primary p-0 font-weight-bold mr-2" onclick="toggleAllExportColumns(true)">Chọn tất cả</button>
                                <span class="text-muted">|</span>
                                <button type="button" class="btn btn-xs btn-link text-danger p-0 font-weight-bold ml-2" onclick="toggleAllExportColumns(false)">Bỏ chọn tất cả</button>
                            </div>
                        </div>
                        <div class="p-3 border rounded bg-light" style="max-height: 250px; overflow-y: auto;">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_id" value="id" checked>
                                        <label class="custom-control-label font-weight-bold text-dark" for="exp_col_id">ID Bài viết (id)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_title" value="title" checked>
                                        <label class="custom-control-label font-weight-bold text-dark" for="exp_col_title">Tiêu đề (title)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_slug" value="slug" checked>
                                        <label class="custom-control-label" for="exp_col_slug">Slug (slug)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_category" value="category" checked>
                                        <label class="custom-control-label" for="exp_col_category">Danh mục (category)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_summary" value="summary" checked>
                                        <label class="custom-control-label" for="exp_col_summary">Tóm tắt (summary)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_content" value="content" checked>
                                        <label class="custom-control-label" for="exp_col_content">Nội dung chi tiết (content)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_status" value="status" checked>
                                        <label class="custom-control-label" for="exp_col_status">Trạng thái (status)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_meta_title" value="meta_title" checked>
                                        <label class="custom-control-label" for="exp_col_meta_title">Tiêu đề SEO (meta_title)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_meta_desc" value="meta_description" checked>
                                        <label class="custom-control-label" for="exp_col_meta_desc">Mô tả SEO (meta_description)</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input export-col-chk" id="exp_col_image" value="image" checked>
                                        <label class="custom-control-label" for="exp_col_image">Ảnh Thumbnail (image)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success font-weight-bold" onclick="submitExportPosts()">
                        <i class="fa fa-download mr-1"></i> Tải file Excel / CSV
                    </button>
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
    <!-- PapaParse for high-speed client-side CSV handling -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>

    <script>
        let table, currentPickerMode;
        let ssCategory;
        let selectedPosts = new Set();
        window.currentTrashFilter = 'all';

        // ─── Các cột hệ thống phục vụ Import / Export Bài viết ───────────────
        const POST_SYSTEM_COLUMNS = [
            { key: 'id', label: 'ID Bài viết (Dùng để cập nhật chính xác)', required: false },
            { key: 'title', label: 'Tiêu đề bài viết (Bắt buộc)', required: true },
            { key: 'slug', label: 'Slug (Đường dẫn tĩnh SEO)', required: false },
            { key: 'category', label: 'Tên danh mục (VD: Tin tức > Tin công nghệ)', required: false },
            { key: 'summary', label: 'Tóm tắt bài viết (Mô tả ngắn)', required: false },
            { key: 'content', label: 'Nội dung bài viết chi tiết (HTML)', required: false },
            { key: 'status', label: 'Trạng thái (published / draft)', required: false },
            { key: 'meta_title', label: 'Tiêu đề SEO (Meta Title)', required: false },
            { key: 'meta_description', label: 'Mô tả SEO (Meta Description)', required: false },
            { key: 'image', label: 'Ảnh Đại diện (Tên file, VD: tin-tuc-1.jpg)', required: false }
        ];

        let postCsvHeaders = [];

        $(document).ready(function() {
            // Initialize SlimSelect
            ssCategory = new SlimSelect({
                select: '#category_id',
                settings: { placeholderText: 'Chọn danh mục', allowDeselect: true }
            });
            
            initTinyMCE();
            
            if ($.fn && $.fn.dataTable) {
                $.fn.dataTable.ext.errMode = 'none';
            }

            table = $('#postsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.api.posts.list") }}',
                    data: function(d) {
                        d.trash_status = window.currentTrashFilter || 'all';
                    },
                    dataSrc: function(json) {
                        if (!json) return [];
                        if (json.counts) {
                            $('#countAll').text(json.counts.all || 0);
                            $('#countActive').text(json.counts.active || 0);
                            $('#countTrashed').text(json.counts.trashed || 0);
                        }
                        return Array.isArray(json.data) ? json.data : [];
                    },
                    error: function(xhr, error, thrown) {
                        console.warn('Posts DataTable AJAX error:', xhr.status, xhr.responseText);
                    }
                },
                columns: [
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data) {
                            let checked = selectedPosts.has(parseInt(data)) ? 'checked' : '';
                            return `<input type="checkbox" class="post-checkbox" value="${data}" ${checked} onchange="togglePostSelect(${data}, this)">`;
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
                        orderable: false,
                        render: function(data, type, row) {
                            if(data && data.length > 0) {
                                return `<div class="thumbnail-wrapper"><img src="${data[0].url}" class="thumbnail-img" alt="${row.title}"></div>`;
                            }
                            return `<div class="thumbnail-wrapper"><i class="fa fa-image no-image-icon"></i></div>`;
                        }
                    },
                    { 
                        data: 'title',
                        render: function(data, type, row) {
                            return `
                                <div class="post-title-txt">${data}</div>
                                <div class="post-slug-txt">Slug: ${row.slug || 'N/A'}</div>
                            `;
                        }
                    },
                    { 
                        data: 'category',
                        orderable: false,
                        render: function(data) {
                            return data ? `<span style="color:#475569; font-size:13px; font-weight:500;">${data.name}</span>` : '<span class="text-muted">-</span>';
                        }
                    },
                    { 
                        data: 'status',
                        render: function(data, type, row) {
                            if (row.is_trashed) {
                                return `<span class="badge badge-danger">Trong thùng rác</span>`;
                            }
                            let badgeClass = data === 'published' ? 'badge-soft-success' : 'badge-soft-danger';
                            let text = data === 'published' ? 'Đã xuất bản' : 'Bản nháp';
                            return `<span class="${badgeClass}">${text}</span>`;
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            if(!data) return '-';
                            let date = new Date(data);
                            return date.toLocaleDateString('vi-VN');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            if (row.is_trashed) {
                                return `
                                    <div class="d-flex">
                                        <button class="btn-action btn-restore" onclick="restorePost(${row.id})" title="Khôi phục"><i class="fa fa-undo"></i></button>
                                        <button class="btn-action btn-force-delete" onclick="forceDeletePost(${row.id})" title="Xóa vĩnh viễn"><i class="fa fa-times-circle"></i></button>
                                    </div>
                                `;
                            }
                            return `
                                <div class="d-flex">
                                    <button class="btn-action btn-edit" onclick="editPost(${row.id})" title="Chỉnh sửa"><i class="fa fa-pencil"></i></button>
                                    <button class="btn-action btn-delete" onclick="deletePost(${row.id})" title="Chuyển vào thùng rác"><i class="fa fa-trash"></i></button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.21/i18n/Vietnamese.json"
                }
            });

            table.on('draw', function() {
                updateCheckAllState();
            });
            
            $('#postModal').on('hidden.bs.modal', function () {
                $('#postForm')[0].reset();
                $('#post_id').val('');
                tinymce.get('content').setContent('');
                
                // Clear media
                $('#thumbnail_id').val('');
                $('#thumbnailPickerBox').html(`<i class="fa fa-image fa-3x text-muted mb-2"></i><div class="mt-2 text-muted">Click để chọn ảnh đại diện</div>`);
                $('#thumbnailPickerBox').css('padding', '20px');
            });

            // Fix scrolling issue when closing nested modal
            $('#mediaManagerModal').on('hidden.bs.modal', function () {
                if ($('#postModal').is(':visible')) {
                    $('body').addClass('modal-open');
                }
            });
        });

        // ================= MEDIA PICKER SYSTEM =================
        
        // Listener for messages from Media Manager iframe
        window.addEventListener("message", function(event) {
            // Very important: Verify origin in production!
            if (event.data && event.data.type === 'media_selected') {
                const selectedItems = event.data.items; // Array of media objects
                if (selectedItems.length > 0) {
                    handleMediaSelection(selectedItems);
                }
                $('#mediaManagerModal').modal('hide');
            }
        }, false);

        function openMediaPicker(mode) {
            currentPickerMode = mode;
            let multiple = (mode === 'gallery') ? 1 : 0;
            const url = `{{ route('admin.media.index') }}?picker=1&multiple=${multiple}&folder=clients/imgs/posts`;
            $('#mediaManagerIframe').attr('src', url);
            
            // Show modal above others
            $('#mediaManagerModal').css('z-index', 100000).modal('show');
        }

        function handleMediaSelection(items) {
            if (currentPickerMode === 'thumbnail') {
                let item = items[0];
                $('#thumbnail_id').val(item.id);
                $('#thumbnailPickerBox').html(`<img src="${item.url}" style="max-width: 100%; max-height: 150px; border-radius: 6px;">`);
                $('#thumbnailPickerBox').css('padding', '5px');
            } else if (currentPickerMode === 'tinymce') {
                let item = items[0];
                if(window.tinymceCallback) {
                    window.tinymceCallback(item.url, { alt: item.title || item.original_name });
                    window.tinymceCallback = null;
                }
            } else if (currentPickerMode === 'tinymce_custom') {
                let item = items[0];
                if(window.activeTinyMCE) {
                    window.activeTinyMCE.insertContent(`<img src="${item.url}" alt="${item.title || item.original_name}">`);
                    window.activeTinyMCE = null;
                }
            }
        }

        // ================= TINYMCE =================
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
                        const url = `{{ route('admin.media.index') }}?picker=1&multiple=0&folder=clients/imgs/posts`;
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
                            const url = `{{ route('admin.media.index') }}?picker=1&multiple=0&folder=clients/imgs/posts`;
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
            $('#postForm')[0].reset();
            $('#post_id').val('');
            $('#thumbnail_id').val('');
            $('#thumbnailPickerBox').html(`<i class="fa fa-image fa-3x text-muted mb-2"></i><div class="mt-2 text-muted">Click để chọn ảnh đại diện</div>`).css('padding', '20px');
            
            ssCategory.setSelected('');
            $('#modalTitle').text('Thêm Bài viết Mới');
            $('#postModal').modal('show');
        }

        function savePost(e) {
            e.preventDefault();
            
            // Sync tinymce content to textarea before sending
            tinymce.triggerSave();
            
            let id = $('#post_id').val();
            let url = id ? `{{ url('admin/api/posts') }}/${id}` : '{{ route("admin.api.posts.store") }}';
            let method = id ? 'PUT' : 'POST';
            
            let formData = new FormData($('#postForm')[0]);
            
            // For PUT method in Laravel with FormData, we need to append _method=PUT and use POST
            if(id) {
                formData.append('_method', 'PUT');
                method = 'POST';
            }

            // Show loading
            $('#btnSave').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Đang lưu...');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#postModal').modal('hide');
                        table.ajax.reload(null, false); // Reload table without resetting pagination
                    } else {
                        toastr.error(response.message || 'Có lỗi xảy ra');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Lỗi hệ thống');
                    }
                },
                complete: function() {
                    $('#btnSave').prop('disabled', false).html('Lưu Bài viết');
                }
            });
        }

        function editPost(id) {
            $.get(`{{ url('admin/api/posts') }}/${id}`, function(response) {
                if (response.success) {
                    let p = response.data;
                    $('#post_id').val(p.id);
                    $('#title').val(p.title);
                    $('#slug').val(p.slug);
                    $('#summary').val(p.summary);
                    $('#meta_title').val(p.meta_title);
                    $('#meta_description').val(p.meta_description);
                    $('#status').val(p.status);
                    
                    if(p.category_id) {
                        ssCategory.setSelected(p.category_id.toString());
                    } else {
                        ssCategory.setSelected('');
                    }
                    
                    tinymce.get('content').setContent(p.content || '');
                    
                    // Set thumbnail
                    if(p.thumbnail_id && p.thumbnail_url) {
                        $('#thumbnail_id').val(p.thumbnail_id);
                        $('#thumbnailPickerBox').html(`<img src="${p.thumbnail_url}" style="max-width: 100%; max-height: 150px; border-radius: 6px;">`).css('padding', '5px');
                    } else {
                        $('#thumbnail_id').val('');
                        $('#thumbnailPickerBox').html(`<i class="fa fa-image fa-3x text-muted mb-2"></i><div class="mt-2 text-muted">Click để chọn ảnh đại diện</div>`).css('padding', '20px');
                    }

                    $('#modalTitle').text('Chỉnh sửa Bài viết');
                    $('#postModal').modal('show');
                }
            });
        }

        function setTrashFilter(status, btn) {
            window.currentTrashFilter = status;
            $('#filterStatusGroup button').removeClass('active');
            $(btn).addClass('active');
            selectedPosts.clear();
            $('#checkAll, .post-checkbox').prop('checked', false);
            updateCheckAllState();
            table.ajax.reload();
        }

        function toggleCheckAll(el, event) {
            event.stopPropagation();
            let isChecked = $(el).is(':checked');
            $('.post-checkbox').each(function() {
                let id = parseInt($(this).val());
                if (isChecked) {
                    selectedPosts.add(id);
                    $(this).prop('checked', true);
                } else {
                    selectedPosts.delete(id);
                    $(this).prop('checked', false);
                }
            });
            updateCheckAllState();
        }

        function togglePostSelect(id, el) {
            let numId = parseInt(id);
            if ($(el).is(':checked')) {
                selectedPosts.add(numId);
            } else {
                selectedPosts.delete(numId);
            }
            updateCheckAllState();
        }

        function updateCheckAllState() {
            let count = selectedPosts.size;
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
            } else {
                $('#btnBulkRestore').hide();
                $('#btnBulkDelete').hide();
                $('#btnBulkForceDelete').hide();
            }

            let totalOnPage = $('.post-checkbox').length;
            let checkedOnPage = $('.post-checkbox:checked').length;
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
                    text: `Đã ${actionName} thành công ${processedCount}/${total} bài viết!`,
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

        function deletePost(id) {
            Swal.fire({
                title: 'Chuyển vào thùng rác?',
                text: "Bài viết này sẽ bị xóa mềm và chuyển vào thùng rác.",
                type: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang chuyển vào thùng rác...', 'Đang xử lý dữ liệu bài viết...');
                    $.ajax({
                        url: `{{ url('admin/api/posts') }}/${id}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
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
                                selectedPosts.delete(parseInt(id));
                                updateCheckAllState();
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Lỗi!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Lỗi!', 'Lỗi khi xóa bài viết: ' + (xhr.responseJSON?.message || xhr.statusText), 'error');
                        }
                    });
                }
            });
        }

        function restorePost(id) {
            Swal.fire({
                title: 'Khôi phục bài viết?',
                text: "Bài viết sẽ được đưa trở lại danh sách hoạt động.",
                type: 'question',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Khôi phục ngay',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang khôi phục...', 'Đang đưa bài viết trở lại hoạt động...');
                    $.ajax({
                        url: `{{ url('admin/api/posts') }}/${id}/restore`,
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
                                selectedPosts.delete(parseInt(id));
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

        function forceDeletePost(id) {
            Swal.fire({
                title: 'XÓA VĨNH VIỄN?',
                text: "Hành động này KHÔNG THỂ khôi phục! Toàn bộ dữ liệu bài viết và ảnh liên quan sẽ bị xóa hoàn toàn.",
                type: 'error',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa vĩnh viễn',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    showProcessingModal('Đang xóa vĩnh viễn...', 'Đang xóa toàn bộ dữ liệu bài viết khỏi hệ thống...');
                    $.ajax({
                        url: `{{ url('admin/api/posts') }}/${id}/force-delete`,
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
                                selectedPosts.delete(parseInt(id));
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

        function deleteSelectedPosts() {
            let ids = Array.from(selectedPosts);
            if (ids.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một bài viết để chuyển vào thùng rác!');
                return;
            }

            Swal.fire({
                title: `Chuyển ${ids.length} bài viết vào thùng rác?`,
                text: "Các bài viết được chọn sẽ bị xóa mềm và đưa vào thùng rác.",
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
                        url: `{{ route('admin.api.posts.bulk.destroy') }}`,
                        title: `Đang chuyển ${ids.length} bài viết vào thùng rác...`,
                        actionName: 'chuyển thùng rác',
                        barClass: 'bg-warning',
                        onComplete: function() {
                            selectedPosts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        function restoreSelectedPosts() {
            let ids = Array.from(selectedPosts);
            if (ids.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một bài viết để khôi phục!');
                return;
            }

            Swal.fire({
                title: `Khôi phục ${ids.length} bài viết?`,
                text: "Các bài viết đã chọn sẽ được đưa trở lại danh sách hoạt động.",
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
                        url: `{{ route('admin.api.posts.bulk.restore') }}`,
                        title: `Đang khôi phục ${ids.length} bài viết...`,
                        actionName: 'khôi phục',
                        barClass: 'bg-success',
                        onComplete: function() {
                            selectedPosts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        function forceDeleteSelectedPosts() {
            let ids = Array.from(selectedPosts);
            if (ids.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một bài viết để xóa vĩnh viễn!');
                return;
            }

            Swal.fire({
                title: `XÓA VĨNH VIỄN ${ids.length} BÀI VIẾT?`,
                text: "Hành động này KHÔNG THỂ khôi phục! Tất cả dữ liệu bài viết và ảnh liên quan sẽ bị xóa hoàn toàn.",
                type: 'error',
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa vĩnh viễn tất cả',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    executeBatchActionWithProgress({
                        ids: ids,
                        url: `{{ route('admin.api.posts.bulk.force.destroy') }}`,
                        title: `Đang xóa vĩnh viễn ${ids.length} bài viết...`,
                        actionName: 'xóa vĩnh viễn',
                        barClass: 'bg-danger',
                        onComplete: function() {
                            selectedPosts.clear();
                            updateCheckAllState();
                            table.ajax.reload(null, false);
                        }
                    });
                }
            });
        }

        // ================= XUẤT POSTS (CHỌN CỘT & CHỌN DANH MỤC) =================
        function openExportModal() {
            $('#exportCategoryId').val('');
            toggleAllExportColumns(true);
            $('#exportModal').modal('show');
        }

        function toggleAllExportColumns(selectAll) {
            $('.export-col-chk').prop('checked', selectAll);
        }

        function submitExportPosts() {
            const categoryId = $('#exportCategoryId').val();
            const selectedCols = [];
            
            $('.export-col-chk:checked').each(function() {
                selectedCols.push($(this).val());
            });

            if (selectedCols.length === 0) {
                toastr.warning('Vui lòng chọn ít nhất một cột để xuất!');
                return;
            }

            let url = '{{ route("admin.api.posts.export") }}?';
            const params = [];

            if (categoryId) {
                params.push('category_id=' + encodeURIComponent(categoryId));
            }
            if (selectedCols.length > 0) {
                params.push('columns=' + encodeURIComponent(selectedCols.join(',')));
            }

            url += params.join('&');
            window.location.href = url;
            $('#exportModal').modal('hide');
            toastr.success('Đang khởi tạo file xuất...');
        }

        // ================= IMPORT / EXPORT EXCEL & CSV CHO POSTS =================
        function exportPostsCsv() {
            window.location.href = '{{ route("admin.api.posts.export") }}';
        }

        function downloadSamplePostCsv() {
            const sampleData = [
                ['id', 'title', 'slug', 'category', 'summary', 'content', 'status', 'meta_title', 'meta_description', 'image'],
                ['', 'Hướng dẫn chọn cảm biến quang Omron chính hãng', 'huong-dan-chon-cam-bien-quang-omron-chinh-hang', 'Tin tức > Tin công nghệ', 'Tổng hợp kinh nghiệm và tiêu chí lựa chọn cảm biến quang Omron phù hợp với nhà máy tự động hóa.', '<p>Nội dung chi tiết bài viết hướng dẫn chọn cảm biến quang...</p>', 'published', 'Hướng dẫn chọn cảm biến quang Omron | MISUTECH', 'Kinh nghiệm chọn cảm biến quang Omron chính hãng.', 'sensor-thumb.jpg'],
                ['', 'Tìm hiểu nguyên lý hoạt động của biến tần công nghiệp', 'tim-hieu-nguyen-ly-hoat-dong-cua-bien-tan-cong-nghiep', 'Kiến thức kỹ thuật', 'Biến tần là thiết bị quan trọng trong việc điều khiển tốc độ động cơ và tiết kiệm năng lượng.', '<p>Chi tiết về cấu tạo và nguyên lý hoạt động của biến tần...</p>', 'published', 'Nguyên lý hoạt động biến tần | MISUTECH', 'Tìm hiểu chi tiết về biến tần công nghiệp.', 'inverter-thumb.jpg']
            ];

            let csvContent = '\uFEFF'; // BOM UTF-8
            sampleData.forEach(row => {
                let escapedRow = row.map(val => `"${val.replace(/"/g, '""')}"`);
                csvContent += escapedRow.join(',') + '\r\n';
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', 'mau_nhap_bai_viet.csv');
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

        function readPostCsvHeaders() {
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
                        postCsvHeaders = results.meta.fields;
                        renderPostMappingTable();
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

        function renderPostMappingTable() {
            const tbody = $('#mappingTable tbody');
            tbody.empty();
            
            POST_SYSTEM_COLUMNS.forEach(col => {
                let options = `<option value="">-- Bỏ qua không nhập --</option>`;
                postCsvHeaders.forEach(header => {
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

        function startPostImport() {
            const fileInput = document.getElementById('importCsvFile');
            if (!fileInput.files.length) {
                toastr.error('Vui lòng chọn file');
                return;
            }
            const file = fileInput.files[0];
            const importMode = $('#importMode').val();
            
            // Thu thập mapping
            let mapping = {};
            let hasTitle = false;
            
            $('.mapping-select').each(function() {
                let sysKey = $(this).data('sys-key');
                let csvCol = $(this).val();
                if (csvCol) {
                    mapping[sysKey] = csvCol;
                    if (sysKey === 'title') hasTitle = true;
                }
            });
            
            if (!hasTitle) {
                toastr.error('Bạn bắt buộc phải ánh xạ cột "Tiêu đề bài viết"');
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
                        if (newRow.title && newRow.title !== '') {
                            mappedData.push(newRow);
                        }
                    });

                    const totalMappedRows = mappedData.length;
                    if (totalMappedRows === 0) {
                        toastr.error('Không tìm thấy dòng dữ liệu nào có Tiêu đề hợp lệ');
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
                            toastr.success(`Nhập bài viết thành công! Thêm mới: ${totalInserted}, Cập nhật: ${totalUpdated}, Bỏ qua: ${totalSkipped}`);
                            resetImportUI();
                            table.ajax.reload(null, false);
                            setTimeout(() => {
                                $('#importModal').modal('hide');
                            }, 1200);
                            return;
                        }

                        $.ajax({
                            url: '{{ route("admin.api.posts.import.batch") }}',
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
    </script>
@endsection
