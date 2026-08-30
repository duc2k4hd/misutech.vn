@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <!-- Summernote -->
    <link href="{{ asset('admins/vendor/summernote/summernote.css') }}" rel="stylesheet">
    <style>
        .action-btns .btn { padding: 5px 10px; margin-right: 5px; }
        .brand-logo-preview { width: 60px; height: 35px; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 2px; }
        .brand-modal-preview { max-height: 80px; width: auto; object-fit: contain; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản lý Thương hiệu (Brands)</h4>
                <span class="ml-1">Quản lý danh sách các Hãng sản xuất thiết bị tự động hóa (Omron, Mitsubishi, Yaskawa, Fuji...)</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Thương hiệu</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách Hãng sản xuất</h4>
                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        <i class="mdi mdi-plus mr-1"></i>+ Thêm Thương hiệu mới
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="brandsTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th width="90">Logo</th>
                                    <th>Tên Thương hiệu</th>
                                    <th>Slug</th>
                                    <th width="120">Số Sản phẩm</th>
                                    <th width="120">Số Series</th>
                                    <th width="140">Ngày tạo</th>
                                    <th width="120">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm / Sửa Thương hiệu -->
    <div class="modal fade" id="brandModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalTitle">Thêm Thương hiệu mới</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="brandForm" enctype="multipart/form-data">
                    <input type="hidden" id="brand_id" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <label>Tên Thương hiệu (Hãng) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ví dụ: Mitsubishi Electric, Omron, Yaskawa..." required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label>Slug (Đường dẫn tĩnh)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" placeholder="Để trống tự tạo từ tên">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Logo Thương hiệu</label>
                            <div class="custom-file mb-2">
                                <input type="file" class="custom-file-input" id="logoFile" name="logo" accept="image/*" onchange="previewLogo(this)">
                                <label class="custom-file-label" for="logoFile">Chọn file ảnh logo từ máy tính...</label>
                            </div>
                            <div id="logoPreviewContainer" class="d-none mt-2">
                                <img id="logoPreviewImg" src="" alt="Logo Preview" class="brand-modal-preview">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Giới thiệu Thương hiệu</label>
                            <textarea class="form-control" id="brandContent" name="content" rows="4" placeholder="Mô tả về lịch sử, lĩnh vực chuyên sâu của hãng..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tiêu đề SEO (Meta Title)</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" placeholder="Tiêu đề hiển thị Google...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mô tả SEO (Meta Description)</label>
                                    <input type="text" class="form-control" id="meta_description" name="meta_description" placeholder="Mô tả ngắn gọn cho SEO...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitBrand">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable -->
    <script src="{{ asset('admins/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <!-- Summernote -->
    <script src="{{ asset('admins/vendor/summernote/js/summernote.min.js') }}"></script>

    <script>
        let table;

        $(document).ready(function() {
            // Auto generate slug from name
            $('#name').on('input', function() {
                if (!$('#brand_id').val()) {
                    let title = $(this).val();
                    let slug = title.toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .replace(/[đĐ]/g, 'd')
                        .replace(/([^0-9a-z-\s])/g, '')
                        .replace(/(\s+)/g, '-')
                        .replace(/-+/g, '-')
                        .replace(/^-+|-+$/g, '');
                    $('#slug').val(slug);
                }
            });

            // Initialize DataTable
            table = $('#brandsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.api.brands.list') }}",
                columns: [
                    { data: 'id' },
                    {
                        data: 'logo',
                        render: function(data) {
                            if (!data) {
                                return '<span class="text-muted" style="font-size: 11px;">Chưa có</span>';
                            }
                            let src = data.startsWith('http') ? data : "{{ asset('storage/clients/imgs/brands') }}/" + data;
                            return '<img src="' + src + '" alt="Brand Logo" class="brand-logo-preview">';
                        }
                    },
                    {
                        data: 'name',
                        render: function(data) {
                            return '<strong class="text-dark">' + data + '</strong>';
                        }
                    },
                    {
                        data: 'slug',
                        render: function(data) {
                            return '<span class="badge badge-light font-weight-normal text-muted">' + data + '</span>';
                        }
                    },
                    {
                        data: 'products_count',
                        render: function(data) {
                            return '<span class="badge badge-primary badge-pill">' + data + ' SP</span>';
                        }
                    },
                    {
                        data: 'series_count',
                        render: function(data) {
                            return '<span class="badge badge-info badge-pill">' + data + ' Series</span>';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            if (!data) return '';
                            return new Date(data).toLocaleDateString('vi-VN');
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        render: function(data) {
                            return `
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="editBrand(${data})" title="Chỉnh sửa">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteBrand(${data})" title="Xóa">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    search: "Tìm kiếm:",
                    lengthMenu: "Hiển thị _MENU_ bản ghi",
                    info: "Hiển thị _START_ đến _END_ trong _TOTAL_ thương hiệu",
                    infoEmpty: "Không có dữ liệu",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Sau",
                        previous: "Trước"
                    }
                }
            });

            // Form Submit
            $('#brandForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#brand_id').val();
                let url = id ? "{{ url('admin/api/brands') }}/" + id : "{{ route('admin.api.brands.store') }}";
                
                let formData = new FormData(this);
                if (id) {
                    formData.append('_method', 'PUT');
                }

                $('#btnSubmitBrand').prop('disabled', true).text('Đang lưu...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('#btnSubmitBrand').prop('disabled', false).text('Lưu thông tin');
                        if (res.success) {
                            toastr.success(res.message);
                            $('#brandModal').modal('hide');
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function(err) {
                        $('#btnSubmitBrand').prop('disabled', false).text('Lưu thông tin');
                        if (err.responseJSON && err.responseJSON.errors) {
                            let errors = err.responseJSON.errors;
                            let msg = Object.values(errors).flat().join('<br>');
                            toastr.error(msg);
                        } else {
                            toastr.error(err.responseJSON?.message || 'Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    }
                });
            });
        });

        function previewLogo(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#logoPreviewImg').attr('src', e.target.result);
                    $('#logoPreviewContainer').removeClass('d-none');
                };
                reader.readAsDataURL(input.files[0]);
                $('.custom-file-label').text(input.files[0].name);
            }
        }

        function openModal() {
            $('#brandForm')[0].reset();
            $('#brand_id').val('');
            $('#modalTitle').text('Thêm Thương hiệu mới');
            $('#logoPreviewContainer').addClass('d-none');
            $('#logoPreviewImg').attr('src', '');
            $('.custom-file-label').text('Chọn file ảnh logo từ máy tính...');
            $('#brandModal').modal('show');
        }

        function editBrand(id) {
            $.get("{{ url('admin/api/brands') }}/" + id, function(res) {
                if (res.success) {
                    let d = res.data;
                    $('#brand_id').val(d.id);
                    $('#name').val(d.name);
                    $('#slug').val(d.slug);
                    $('#brandContent').val(d.content || '');
                    $('#meta_title').val(d.meta_title || '');
                    $('#meta_description').val(d.meta_description || '');

                    if (d.logo) {
                        let src = d.logo.startsWith('http') ? d.logo : "{{ asset('storage/clients/imgs/brands') }}/" + d.logo;
                        $('#logoPreviewImg').attr('src', src);
                        $('#logoPreviewContainer').removeClass('d-none');
                    } else {
                        $('#logoPreviewContainer').addClass('d-none');
                    }

                    $('.custom-file-label').text('Chọn file ảnh khác để thay đổi...');
                    $('#modalTitle').text('Chỉnh sửa Thương hiệu: ' + d.name);
                    $('#brandModal').modal('show');
                }
            }).fail(function() {
                toastr.error('Không tìm thấy thông tin thương hiệu!');
            });
        }

        function deleteBrand(id) {
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                text: "Thương hiệu này sẽ bị xóa khỏi hệ thống!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ url('admin/api/brands') }}/" + id,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.success) {
                                toastr.success(res.message);
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function(err) {
                            toastr.error(err.responseJSON?.message || 'Không thể xóa thương hiệu này!');
                        }
                    });
                }
            });
        }
    </script>
@endsection
