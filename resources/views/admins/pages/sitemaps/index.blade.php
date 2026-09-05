@extends('admins.layouts.master')

@section('styles')
    <style>
        .sitemap-stat-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .sitemap-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
        }
        .sitemap-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .sitemap-module-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
            transition: all 0.2s ease;
            cursor: pointer;
            user-select: none;
        }
        .sitemap-module-box:hover {
            border-color: #3b82f6;
            background: #f0f7ff;
        }
        .sitemap-module-box.active {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
        }
        .sitemap-module-box .custom-control-label {
            cursor: pointer;
            font-weight: 600;
            color: #1e293b;
            font-size: 14px;
        }
        .sitemap-module-box .module-desc {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
            margin-bottom: 0;
        }
        .badge-sitemap-index {
            background-color: #7c3aed;
            color: #ffffff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .badge-sitemap-sub {
            background-color: #0284c7;
            color: #ffffff;
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 4px;
        }
        .sitemap-code-block {
            background: #0f172a;
            color: #38bdf8;
            padding: 10px 14px;
            border-radius: 6px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-primary font-weight-bold"><i class="mdi mdi-sitemap mr-1"></i> Quản Lý & Cấu Hình Sitemap</h4>
                <span class="ml-1">Tự động tạo sitemap chuẩn Google SEO, hỗ trợ chia nhiều file & ảnh</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Sitemap</a></li>
            </ol>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-sm-6">
            <div class="card sitemap-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="sitemap-stat-icon bg-primary-light text-primary mr-3">
                            <i class="mdi mdi-xml"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Tổng số File</span>
                            <h3 class="font-weight-bold mb-0 text-dark" id="statTotalFiles">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-sm-6">
            <div class="card sitemap-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="sitemap-stat-icon bg-success-light text-success mr-3">
                            <i class="mdi mdi-link-variant"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Tổng Link Đã Lập Chỉ Mục</span>
                            <h3 class="font-weight-bold mb-0 text-success" id="statTotalLinks">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-sm-6">
            <div class="card sitemap-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="sitemap-stat-icon bg-warning-light text-warning mr-3">
                            <i class="mdi mdi-numeric"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Giới hạn Link / File</span>
                            <h3 class="font-weight-bold mb-0 text-warning" id="statMaxLinks">10,000</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3 col-sm-6">
            <div class="card sitemap-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="sitemap-stat-icon bg-info-light text-info mr-3">
                            <i class="mdi mdi-clock-check-outline"></i>
                        </div>
                        <div>
                            <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px;">Cập nhật lần cuối</span>
                            <h5 class="font-weight-bold mb-0 text-dark" id="statLastGenerated" style="font-size: 14px;">Chưa tạo</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khung Cấu hình & Tạo Sitemap -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h4 class="card-title text-dark font-weight-bold"><i class="mdi mdi-tune mr-1 text-primary"></i> Cài Đặt Nội Dung & Phân Đoạn Sitemap</h4>
                        <p class="text-muted mb-0 font-13">Chọn các phần dữ liệu bạn muốn Googlebot thu thập và thiết lập số lượng URL trên mỗi file sitemap</p>
                    </div>
                    <div class="mt-2 mt-sm-0">
                        <button type="button" class="btn btn-outline-secondary mr-2" onclick="saveSitemapSettings()">
                            <i class="mdi mdi-content-save mr-1"></i> Lưu Cấu Hình
                        </button>
                        <button type="button" class="btn btn-primary" id="btnGenerateSitemap" onclick="generateSitemap()">
                            <i class="mdi mdi-refresh mr-1"></i> Tạo Lại Sitemap Ngay
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="sitemapConfigForm" onsubmit="return false;">
                        <div class="row mb-4">
                            <!-- Giới hạn số link / file -->
                            <div class="col-md-6 col-lg-4 mb-3">
                                <label class="font-weight-bold text-dark">
                                    <i class="mdi mdi-counter mr-1 text-primary"></i> Số link tối đa trên 1 file sitemap <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control font-weight-bold text-primary" id="sitemap_max_links_per_file" name="sitemap_max_links_per_file" min="100" max="50000" step="500" value="10000" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-weight-bold">link / file</span>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    <i class="mdi mdi-information-outline"></i> Ví dụ: Có 20.000 sản phẩm với mức 10.000 link/file sẽ tự động tạo thành <code>products-sitemap.xml</code> và <code>products-1-sitemap.xml</code>.
                                </small>
                            </div>

                            <!-- Link Master Index File -->
                            <div class="col-md-6 col-lg-8 mb-3">
                                <label class="font-weight-bold text-dark">
                                    <i class="mdi mdi-google mr-1 text-success"></i> Đường dẫn Sitemap Index chính (Khai báo Google Search Console)
                                </label>
                                <div class="sitemap-code-block">
                                    <span id="masterSitemapUrl">{{ url('sitemap.xml') }}</span>
                                    <div>
                                        <a href="{{ url('sitemap.xml') }}" target="_blank" class="btn btn-xs btn-outline-info text-white mr-1" title="Mở trong tab mới">
                                            <i class="mdi mdi-open-in-new"></i> Xem
                                        </a>
                                        <button type="button" class="btn btn-xs btn-info" onclick="copyToClipboard('{{ url('sitemap.xml') }}')" title="Copy link">
                                            <i class="mdi mdi-content-copy"></i> Copy Link
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    File chỉ mục tổng được lưu tại <code>public/sitemap.xml</code> và tự động đồng bộ vào <code>public/robots.txt</code>.
                                </small>
                            </div>
                        </div>

                        <!-- Danh sách các Module cần đưa vào sitemap -->
                        <h5 class="font-weight-bold text-dark mb-3"><i class="mdi mdi-checkbox-multiple-marked mr-1 text-primary"></i> Các Thành Phần Đưa Vào Sitemap</h5>
                        <div class="row">
                            <!-- Products -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_products')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_products" name="sitemap_include_products" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_products">
                                            <i class="mdi mdi-store mr-1 text-primary"></i> Sản Phẩm (Products)
                                        </label>
                                    </div>
                                    <p class="module-desc">Toàn bộ sản phẩm đang hoạt động (/san-pham/{slug})</p>
                                </div>
                            </div>

                            <!-- Categories -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_categories')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_categories" name="sitemap_include_categories" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_categories">
                                            <i class="mdi mdi-format-list-bulleted mr-1 text-info"></i> Danh Mục (Categories)
                                        </label>
                                    </div>
                                    <p class="module-desc">Tất cả danh mục sản phẩm (/danh-muc/{slug})</p>
                                </div>
                            </div>

                            <!-- Series -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_series')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_series" name="sitemap_include_series" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_series">
                                            <i class="mdi mdi-layers mr-1 text-warning"></i> Dòng SP (Series)
                                        </label>
                                    </div>
                                    <p class="module-desc">Trang chi tiết các dòng series (/series/{slug})</p>
                                </div>
                            </div>

                            <!-- Blogs / Posts -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_blogs')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_blogs" name="sitemap_include_blogs" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_blogs">
                                            <i class="mdi mdi-newspaper mr-1 text-danger"></i> Bài Viết (Blogs/News)
                                        </label>
                                    </div>
                                    <p class="module-desc">Tin tức, bài viết công nghệ (/blog/{slug})</p>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_images')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_images" name="sitemap_include_images" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_images">
                                            <i class="mdi mdi-image-multiple mr-1 text-success"></i> Hình Ảnh (Images)
                                        </label>
                                    </div>
                                    <p class="module-desc">Google Image Sitemap (Ảnh SP, banner, thư viện)</p>
                                </div>
                            </div>

                            <!-- Documents -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_documents')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_documents" name="sitemap_include_documents" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_documents">
                                            <i class="mdi mdi-file-pdf-box mr-1 text-secondary"></i> Tài Liệu (Documents)
                                        </label>
                                    </div>
                                    <p class="module-desc">File catalog, datasheet kỹ thuật sản phẩm</p>
                                </div>
                            </div>

                            <!-- Static Pages -->
                            <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                                <div class="sitemap-module-box" onclick="toggleModuleBox('sitemap_include_pages')">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="sitemap_include_pages" name="sitemap_include_pages" value="1" checked>
                                        <label class="custom-control-label" for="sitemap_include_pages">
                                            <i class="mdi mdi-home-outline mr-1 text-dark"></i> Trang Tĩnh (Pages)
                                        </label>
                                    </div>
                                    <p class="module-desc">Trang chủ, Cửa hàng, Báo giá, Liên hệ,...</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bảng danh sách các file Sitemap XML đã tạo -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title text-dark font-weight-bold"><i class="mdi mdi-format-list-checks mr-1 text-success"></i> Danh Sách File Sitemap XML Đã Tạo</h4>
                        <p class="text-muted mb-0 font-13">Tất cả các file sitemap con được lưu tự động trong thư mục <code>public/sitemaps/</code></p>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadSitemapInfo()">
                        <i class="mdi mdi-reload mr-1"></i> Làm mới danh sách
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mb-0" id="sitemapFilesTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Tên File Sitemap</th>
                                    <th>Loại Dữ Liệu</th>
                                    <th class="text-center">Số Lượng URL</th>
                                    <th class="text-center">Dung Lượng</th>
                                    <th>Cập Nhật Lúc</th>
                                    <th class="text-center" style="width: 180px;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody id="sitemapFilesTableBody">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-loading mdi-spin mr-1"></i> Đang tải danh sách sitemap...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            loadSitemapInfo();

            // Cập nhật trạng thái active của box khi checkbox thay đổi
            $('#sitemapConfigForm input[type="checkbox"]').on('change', function() {
                updateModuleBoxState($(this));
            });
        });

        function toggleModuleBox(checkboxId) {
            const checkbox = $('#' + checkboxId);
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        }

        function updateModuleBoxState(checkbox) {
            const box = checkbox.closest('.sitemap-module-box');
            if (checkbox.is(':checked')) {
                box.addClass('active');
            } else {
                box.removeClass('active');
            }
        }

        function loadSitemapInfo() {
            $.ajax({
                url: "{{ route('admin.api.sitemaps.info') }}",
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        // 1. Cập nhật thống kê
                        $('#statTotalFiles').text(res.summary.total_files || 0);
                        $('#statTotalLinks').text((res.summary.total_links || 0).toLocaleString());
                        $('#statMaxLinks').text((res.settings.sitemap_max_links_per_file || 10000).toLocaleString());
                        $('#statLastGenerated').text(res.summary.last_generated || 'Chưa tạo');

                        // 2. Điền form cấu hình
                        $('#sitemap_max_links_per_file').val(res.settings.sitemap_max_links_per_file || 10000);
                        $('#sitemap_include_products').prop('checked', !!res.settings.sitemap_include_products);
                        $('#sitemap_include_categories').prop('checked', !!res.settings.sitemap_include_categories);
                        $('#sitemap_include_series').prop('checked', !!res.settings.sitemap_include_series);
                        $('#sitemap_include_blogs').prop('checked', !!res.settings.sitemap_include_blogs);
                        $('#sitemap_include_images').prop('checked', !!res.settings.sitemap_include_images);
                        $('#sitemap_include_documents').prop('checked', !!res.settings.sitemap_include_documents);
                        $('#sitemap_include_pages').prop('checked', !!res.settings.sitemap_include_pages);

                        $('#sitemapConfigForm input[type="checkbox"]').each(function() {
                            updateModuleBoxState($(this));
                        });

                        // 3. Render bảng files
                        renderFilesTable(res.files || []);
                    }
                },
                error: function(err) {
                    toastr.error('Không thể tải thông tin Sitemap: ' + (err.responseJSON?.message || 'Lỗi server'));
                }
            });
        }

        function renderFilesTable(files) {
            const tbody = $('#sitemapFilesTableBody');
            tbody.empty();

            if (!files || files.length === 0) {
                tbody.html(`
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="mdi mdi-alert-circle-outline font-24 d-block mb-2 text-warning"></i>
                            Chưa có file Sitemap nào được tạo. Hãy bấm nút <strong>"Tạo Lại Sitemap Ngay"</strong> ở trên để bắt đầu!
                        </td>
                    </tr>
                `);
                return;
            }

            files.forEach((file, index) => {
                const isIndex = file.is_index;
                const badgeClass = isIndex ? 'badge-sitemap-index' : 'badge-sitemap-sub';
                const countBadge = isIndex 
                    ? `<span class="badge badge-purple">${file.link_count} file sitemap con</span>` 
                    : `<span class="badge badge-success font-weight-bold">${file.link_count.toLocaleString()} URL</span>`;

                const tr = `
                    <tr class="${isIndex ? 'table-light font-weight-bold' : ''}">
                        <td>${index + 1}</td>
                        <td>
                            <a href="${file.url}" target="_blank" class="text-primary font-weight-bold">
                                <i class="mdi ${isIndex ? 'mdi-sitemap text-purple' : 'mdi-file-xml text-info'} mr-1"></i>
                                ${file.name}
                            </a>
                            <div class="text-muted font-11 font-weight-normal">${file.rel_path}</div>
                        </td>
                        <td>
                            <span class="${badgeClass}">${file.type_label}</span>
                        </td>
                        <td class="text-center">${countBadge}</td>
                        <td class="text-center">${file.size_formatted}</td>
                        <td>${file.updated_at}</td>
                        <td class="text-center">
                            <a href="${file.url}" target="_blank" class="btn btn-xs btn-outline-primary" title="Xem XML">
                                <i class="mdi mdi-eye"></i> Xem
                            </a>
                            <button type="button" class="btn btn-xs btn-outline-info" onclick="copyToClipboard('${file.url}')" title="Copy URL">
                                <i class="mdi mdi-content-copy"></i> Copy
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(tr);
            });
        }

        function saveSitemapSettings() {
            const formData = {
                _token: "{{ csrf_token() }}",
                sitemap_max_links_per_file: $('#sitemap_max_links_per_file').val(),
                sitemap_include_products: $('#sitemap_include_products').is(':checked') ? 1 : 0,
                sitemap_include_categories: $('#sitemap_include_categories').is(':checked') ? 1 : 0,
                sitemap_include_series: $('#sitemap_include_series').is(':checked') ? 1 : 0,
                sitemap_include_blogs: $('#sitemap_include_blogs').is(':checked') ? 1 : 0,
                sitemap_include_images: $('#sitemap_include_images').is(':checked') ? 1 : 0,
                sitemap_include_documents: $('#sitemap_include_documents').is(':checked') ? 1 : 0,
                sitemap_include_pages: $('#sitemap_include_pages').is(':checked') ? 1 : 0,
            };

            toastr.info('Đang lưu cấu hình...');

            $.ajax({
                url: "{{ route('admin.api.sitemaps.settings') }}",
                type: 'POST',
                data: formData,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message || 'Lưu cấu hình Sitemap thành công!');
                        loadSitemapInfo();
                    }
                },
                error: function(err) {
                    toastr.error('Lỗi khi lưu cấu hình: ' + (err.responseJSON?.message || 'Vui lòng thử lại'));
                }
            });
        }

        function generateSitemap() {
            const btn = $('#btnGenerateSitemap');
            const originalHtml = btn.html();

            // Lưu cài đặt trước khi tạo
            const formData = {
                _token: "{{ csrf_token() }}",
                sitemap_max_links_per_file: $('#sitemap_max_links_per_file').val(),
                sitemap_include_products: $('#sitemap_include_products').is(':checked') ? 1 : 0,
                sitemap_include_categories: $('#sitemap_include_categories').is(':checked') ? 1 : 0,
                sitemap_include_series: $('#sitemap_include_series').is(':checked') ? 1 : 0,
                sitemap_include_blogs: $('#sitemap_include_blogs').is(':checked') ? 1 : 0,
                sitemap_include_images: $('#sitemap_include_images').is(':checked') ? 1 : 0,
                sitemap_include_documents: $('#sitemap_include_documents').is(':checked') ? 1 : 0,
                sitemap_include_pages: $('#sitemap_include_pages').is(':checked') ? 1 : 0,
            };

            btn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin mr-1"></i> Đang tạo Sitemap...');
            toastr.info('Đang tiến hành tạo toàn bộ file Sitemap XML...');

            // Bước 1: Lưu cấu hình
            $.ajax({
                url: "{{ route('admin.api.sitemaps.settings') }}",
                type: 'POST',
                data: formData,
                success: function() {
                    // Bước 2: Tạo Sitemap
                    $.ajax({
                        url: "{{ route('admin.api.sitemaps.generate') }}",
                        type: 'POST',
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(res) {
                            btn.prop('disabled', false).html(originalHtml);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành Công!',
                                    text: res.message,
                                    confirmButtonText: 'Đồng ý'
                                });
                                loadSitemapInfo();
                            } else {
                                toastr.error(res.message || 'Lỗi khi tạo sitemap');
                            }
                        },
                        error: function(err) {
                            btn.prop('disabled', false).html(originalHtml);
                            toastr.error('Lỗi khi tạo sitemap: ' + (err.responseJSON?.message || 'Timeout hoặc lỗi server'));
                        }
                    });
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalHtml);
                    toastr.error('Lỗi khi lưu cấu hình trước khi tạo');
                }
            });
        }

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    toastr.success('Đã sao chép đường dẫn: ' + text);
                }, function(err) {
                    fallbackCopyTextToClipboard(text);
                });
            } else {
                fallbackCopyTextToClipboard(text);
            }
        }

        function fallbackCopyTextToClipboard(text) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                toastr.success('Đã sao chép đường dẫn: ' + text);
            } catch (err) {
                toastr.error('Không thể tự động sao chép: ' + text);
            }
            document.body.removeChild(textArea);
        }
    </script>
@endsection
