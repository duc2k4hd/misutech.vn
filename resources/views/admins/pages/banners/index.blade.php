@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .action-btns .btn { padding: 5px 10px; margin-right: 5px; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản lý Banner</h4>
                <span class="ml-1">Quản lý banner trên trang chủ và các trang khác</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Banners</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách Banner</h4>
                    <button type="button" class="btn btn-primary" onclick="openModal()">+ Thêm mới</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="bannersTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tiêu đề</th>
                                    <th>Hình ảnh</th>
                                    <th>Đường dẫn</th>
                                    <th>Vị trí</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dữ liệu load qua AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal form -->
    <div class="modal fade" id="bannerModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Banner mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bannerForm">
                        <input type="hidden" id="banner_id" name="id">
                        <div class="form-group">
                            <label>Tiêu đề</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label>Đường dẫn (Link)</label>
                            <input type="text" class="form-control" id="link" name="link" placeholder="Ví dụ: https://domain.com/khuyen-mai hoặc #">
                        </div>
                        <div class="form-group">
                            <label>Thứ tự hiển thị (Position)</label>
                            <input type="number" class="form-control" id="position" name="position" placeholder="Ví dụ: 1, 2, 3...">
                        </div>
                        <div class="form-group">
                            <label>Trạng thái</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Hiển thị</option>
                                <option value="draft">Ẩn</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hình ảnh</label>
                            <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted" id="imageHelp">Chọn ảnh mới để tải lên (Để trống nếu giữ nguyên ảnh cũ khi sửa)</small>
                            <div id="imagePreviewContainer" class="mt-2" style="display: none;">
                                <img id="imagePreview" src="" width="150" class="img-thumbnail">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveBanner()">Lưu thay đổi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable -->
    <script src="{{ asset('admins/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        var table;
        
        // Setup CSRF cho Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        $(document).ready(function() {
            // Khởi tạo DataTable load dữ liệu từ API
            table = $('#bannersTable').DataTable({
                ajax: '{{ route('admin.api.banners.list') }}',
                columns: [
                    { data: 'id' },
                    { data: 'title' },
                    { 
                        data: 'image',
                        render: function(data, type, row) {
                            if(data) {
                                return `<img src="{{ asset('storage/clients/imgs/banners') }}/${data}" height="50" style="object-fit: cover;">`;
                            }
                            return 'Không có ảnh';
                        }
                    },
                    { 
                        data: 'link',
                        render: function(data) {
                            if(data) return `<a href="${data}" target="_blank">Link</a>`;
                            return '';
                        }
                    },
                    { data: 'position' },
                    { 
                        data: 'status',
                        render: function(data) {
                            return data === 'active' 
                                ? '<span class="badge badge-success">Hiển thị</span>' 
                                : '<span class="badge badge-secondary">Ẩn</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm" onclick="editBanner(${row.id})"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteBanner(${row.id})"><i class="fa fa-trash"></i></button>
                                </div>
                            `;
                        }
                    }
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });
        });

        // Hàm mở Modal (Thêm mới)
        function openModal() {
            $('#bannerForm')[0].reset();
            $('#banner_id').val('');
            $('#modalTitle').text('Thêm Banner mới');
            $('#imagePreviewContainer').hide();
            $('#image').prop('required', true); // Thêm mới bắt buộc có ảnh
            $('#bannerModal').modal('show');
        }

        // Hàm Sửa (Lấy dữ liệu & Đổ vào Modal)
        function editBanner(id) {
            $.get('{{ url('admin/api/banners') }}/' + id, function(response) {
                if(response.success) {
                    var data = response.data;
                    $('#banner_id').val(data.id);
                    $('#title').val(data.title);
                    $('#link').val(data.link);
                    $('#position').val(data.position);
                    $('#status').val(data.status);
                    
                    $('#image').prop('required', false); // Sửa không bắt buộc ảnh
                    
                    if(data.image) {
                        $('#imagePreview').attr('src', '{{ asset("storage/clients/imgs/banners") }}/' + data.image);
                        $('#imagePreviewContainer').show();
                    } else {
                        $('#imagePreviewContainer').hide();
                    }
                    
                    $('#modalTitle').text('Sửa Banner: ' + data.title);
                    $('#bannerModal').modal('show');
                }
            });
        }

        // Hàm Lưu (Thêm/Sửa bằng Ajax)
        function saveBanner() {
            var form = $('#bannerForm')[0];
            if (!form.reportValidity()) return;
            
            var formData = new FormData(form);
            
            $.ajax({
                url: '{{ route('admin.api.banners.store') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        $('#bannerModal').modal('hide');
                        toastr.success(response.message, "Thành công", { timeOut: 3000, closeButton: true });
                        table.ajax.reload(null, false); // Reload table ko refresh trang
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    var errorMsg = "Có lỗi xảy ra!";
                    if(errors) {
                        errorMsg = Object.values(errors).map(e => e.join('<br>')).join('<br>');
                    }
                    toastr.error(errorMsg, "Lỗi", { timeOut: 5000, closeButton: true });
                }
            });
        }

        // Hàm Xóa (Dùng SweetAlert2 xác nhận & Ajax xóa)
        function deleteBanner(id) {
            swal({
                title: "Bạn có chắc chắn?",
                text: "Dữ liệu sau khi xóa sẽ không thể khôi phục!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Vâng, xóa nó!",
                cancelButtonText: "Hủy",
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('admin/api/banners') }}/' + id,
                        type: 'DELETE',
                        success: function(response) {
                            if(response.success) {
                                swal("Đã xóa!", response.message, "success");
                                table.ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        }
    </script>
@endsection
