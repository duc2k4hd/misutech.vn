@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .action-btns .btn { padding: 5px 10px; margin-right: 5px; }
        .tox-promotion { display: none !important; }
        .ss-main { border-radius: 0.25rem; min-height: 40px; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản lý Dòng sản phẩm (Series)</h4>
                <span class="ml-1">Nhóm các model sản phẩm độc lập có cùng dòng/series lại với nhau</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Dòng sản phẩm</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách Series</h4>
                    <button type="button" class="btn btn-primary" onclick="openModal()">+ Thêm Series mới</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="seriesTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên Series</th>
                                    <th>Slug</th>
                                    <th>Thương hiệu</th>
                                    <th>Danh mục</th>
                                    <th>Số Model</th>
                                    <th>Thứ tự</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables ajax -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="seriesModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Dòng sản phẩm mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <form id="seriesForm">
                        <input type="hidden" id="series_id" name="id">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tên dòng sản phẩm (Series) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: Omron E3Z-D" required>
                                </div>
                                <div class="form-group">
                                    <label>Slug (Đường dẫn tĩnh)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Để trống tự tạo từ tên">
                                </div>
                                <div class="form-group">
                                    <label>Thương hiệu</label>
                                    <select class="form-control" id="brand_id" name="brand_id">
                                        <option value="">-- Không chọn --</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Danh mục chính</label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option value="">-- Không chọn --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Mô tả ngắn dòng sản phẩm</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Đặc điểm chung, phân loại..."></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Thứ tự sắp xếp</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Trạng thái</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="active">Hiển thị (Active)</option>
                                                <option value="draft">Ẩn (Draft)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tiêu đề SEO (Meta Title)</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title">
                                </div>
                                <div class="form-group">
                                    <label>Mô tả SEO (Meta Description)</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Nội dung giới thiệu chi tiết Series</label>
                                    <textarea id="content" name="content" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveSeries()">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable -->
    <script src="{{ asset('admins/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        let table;
        let ssBrand, ssCategory, ssStatus;

        $(document).ready(function() {
            initTinyMCE();

            // Initialize SlimSelect
            ssBrand = new SlimSelect({
                select: '#brand_id',
                settings: {
                    placeholderText: 'Chọn hoặc tìm thương hiệu...',
                    allowDeselect: true
                }
            });

            ssCategory = new SlimSelect({
                select: '#category_id',
                settings: {
                    placeholderText: 'Chọn hoặc tìm danh mục...',
                    allowDeselect: true
                }
            });

            ssStatus = new SlimSelect({
                select: '#status',
                settings: {
                    showSearch: false
                }
            });

            table = $('#seriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.api.series.list") }}',
                    type: 'GET'
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'slug', name: 'slug' },
                    {
                        data: 'brand',
                        name: 'brand.name',
                        render: function(data) {
                            return data ? data.name : '<span class="text-muted">--</span>';
                        }
                    },
                    {
                        data: 'category',
                        name: 'category.name',
                        render: function(data) {
                            return data ? data.name : '<span class="text-muted">--</span>';
                        }
                    },
                    {
                        data: 'products_count',
                        name: 'products_count',
                        render: function(data) {
                            return '<span class="badge badge-info">' + (data || 0) + ' model</span>';
                        }
                    },
                    { data: 'sort_order', name: 'sort_order' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return data === 'active' 
                                ? '<span class="badge badge-success">Hiển thị</span>' 
                                : '<span class="badge badge-danger">Ẩn</span>';
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'action-btns',
                        render: function(data, type, row) {
                            return `
                                <button type="button" class="btn btn-warning btn-sm" onclick="editSeries(${data})" title="Sửa">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSeries(${data})" title="Xóa">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                order: [[6, 'asc']],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/vi.json'
                }
            });
        });

        function initTinyMCE() {
            tinymce.init({
                license_key: 'gpl',
                selector: '#content',
                height: 250,
                menubar: false,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table paste code help wordcount'
                ],
                toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help'
            });
        }

        function openModal() {
            $('#seriesForm')[0].reset();
            $('#series_id').val('');
            if (ssBrand) ssBrand.setSelected('');
            if (ssCategory) ssCategory.setSelected('');
            if (ssStatus) ssStatus.setSelected('active');
            if (tinymce.get('content')) {
                tinymce.get('content').setContent('');
            }
            $('#modalTitle').text('Thêm Dòng sản phẩm mới');
            $('#seriesModal').modal('show');
        }

        function editSeries(id) {
            $.ajax({
                url: `/admin/api/series/${id}`,
                type: 'GET',
                success: function(res) {
                    if (res.success) {
                        let data = res.data;
                        $('#series_id').val(data.id);
                        $('#name').val(data.name);
                        $('#slug').val(data.slug);
                        if (ssBrand) ssBrand.setSelected(data.brand_id ? String(data.brand_id) : '');
                        if (ssCategory) ssCategory.setSelected(data.category_id ? String(data.category_id) : '');
                        if (ssStatus) ssStatus.setSelected(data.status || 'active');
                        $('#description').val(data.description);
                        $('#sort_order').val(data.sort_order);
                        $('#meta_title').val(data.meta_title);
                        $('#meta_description').val(data.meta_description);

                        if (tinymce.get('content')) {
                            tinymce.get('content').setContent(data.content || '');
                        }

                        $('#modalTitle').text('Chỉnh sửa Dòng sản phẩm: ' + data.name);
                        $('#seriesModal').modal('show');
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('Lỗi khi tải thông tin series');
                }
            });
        }

        function saveSeries() {
            let id = $('#series_id').val();
            let url = id ? `/admin/api/series/${id}` : '{{ route("admin.api.series.store") }}';
            let method = id ? 'PUT' : 'POST';

            let formData = {
                name: $('#name').val(),
                slug: $('#slug').val(),
                brand_id: $('#brand_id').val() || null,
                category_id: $('#category_id').val() || null,
                description: $('#description').val(),
                sort_order: $('#sort_order').val(),
                status: $('#status').val(),
                meta_title: $('#meta_title').val(),
                meta_description: $('#meta_description').val(),
                content: tinymce.get('content') ? tinymce.get('content').getContent() : '',
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: url,
                type: method,
                data: formData,
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        $('#seriesModal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, val) {
                            toastr.error(val[0]);
                        });
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng thử lại');
                    }
                }
            });
        }

        function deleteSeries(id) {
            swal({
                title: "Bạn có chắc chắn muốn xóa?",
                text: "Các sản phẩm thuộc dòng này sẽ được tách ra độc lập (không bị xóa)!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Đồng ý xóa",
                cancelButtonText: "Hủy bỏ"
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    $.ajax({
                        url: `/admin/api/series/${id}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                table.ajax.reload(null, false);
                            } else {
                                toastr.error(res.message);
                            }
                        },
                        error: function() {
                            toastr.error('Có lỗi xảy ra khi xóa');
                        }
                    });
                }
            });
        }
    </script>
@endsection
