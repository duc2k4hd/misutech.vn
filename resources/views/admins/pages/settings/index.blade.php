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
                <h4>Cài đặt hệ thống</h4>
                <span class="ml-1">Quản lý các cấu hình chung của website</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Cài đặt</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Danh sách cài đặt</h4>
                    <button type="button" class="btn btn-primary" onclick="openModal()">+ Thêm mới</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="settingsTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Key</th>
                                    <th>Value</th>
                                    <th>Type</th>
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
    <div class="modal fade" id="settingModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm cài đặt mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="settingForm">
                        <input type="hidden" id="setting_id" name="id">
                        <div class="form-group">
                            <label>Key</label>
                            <input type="text" class="form-control" id="key" name="key" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" id="type" name="type" onchange="renderDynamicInput()">
                                <option value="string">String</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="integer">Integer</option>
                                <option value="float">Float</option>
                                <option value="number">Number</option>
                                <option value="boolean">Boolean</option>
                                <option value="json">Json</option>
                                <option value="email">Email</option>
                                <option value="url">Url</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                        <div class="form-group" id="dynamicInputContainer">
                            <label>Value</label>
                            <input type="text" class="form-control" id="value" name="value" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" onclick="saveSetting()">Lưu thay đổi</button>
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
            table = $('#settingsTable').DataTable({
                ajax: '{{ route('admin.api.settings.list') }}',
                columns: [
                    { data: 'id' },
                    { data: 'key' },
                    { 
                        data: 'value',
                        render: function(data, type, row) {
                            if(row.type === 'image' && data) {
                                return `<img src="{{ asset('storage/clients/imgs/settings') }}/${data}" height="30">`;
                            }
                            if(data && data.length > 50) return data.substr(0, 50) + '...';
                            return data;
                        }
                    },
                    { 
                        data: 'type',
                        render: function(data, type, row) {
                            var badges = {
                                'text': 'badge-primary',
                                'textarea': 'badge-info',
                                'image': 'badge-success'
                            };
                            return `<span class="badge ${badges[data] || 'badge-secondary'}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-btns">
                                    <button class="btn btn-warning btn-sm" onclick="editSetting(${row.id})"><i class="fa fa-pencil"></i></button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteSetting(${row.id})"><i class="fa fa-trash"></i></button>
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
            $('#settingForm')[0].reset();
            $('#setting_id').val('');
            $('#modalTitle').text('Thêm cài đặt mới');
            renderDynamicInput();
            $('#settingModal').modal('show');
        }

        // Hàm thay đổi Input động
        function renderDynamicInput(initialValue = '') {
            var type = $('#type').val();
            var container = $('#dynamicInputContainer');
            var html = '<label>Value</label>';
            
            if (['string', 'text', 'email', 'url'].includes(type)) {
                let inputType = type === 'email' ? 'email' : (type === 'url' ? 'url' : 'text');
                let placeholder = type === 'email' ? 'Ví dụ: admin@domain.com' : (type === 'url' ? 'Ví dụ: https://domain.com' : '');
                html += `<input type="${inputType}" class="form-control" id="value" name="value" placeholder="${placeholder}" required>`;
            } else if (type === 'image') {
                html += `
                    <input type="file" class="form-control-file" id="value" name="value" accept="image/*">
                    <small class="form-text text-muted">Chọn ảnh mới để tải lên (Để trống nếu giữ nguyên ảnh cũ khi sửa)</small>
                `;
                if (initialValue) {
                    let imagePath = '{{ asset("storage/clients/imgs/settings") }}/' + initialValue;
                    html += `<div class="mt-2"><img src="${imagePath}" width="100" class="img-thumbnail"></div>`;
                }
            } else if (['integer', 'float', 'number'].includes(type)) {
                let step = type === 'float' ? '0.01' : '1';
                html += `<input type="number" step="${step}" class="form-control" id="value" name="value" required>`;
            } else if (type === 'boolean') {
                html += `
                    <select class="form-control" id="value" name="value">
                        <option value="true">True (Bật)</option>
                        <option value="false">False (Tắt)</option>
                    </select>
                `;
            } else if (['textarea', 'json'].includes(type)) {
                let placeholder = type === 'json' ? '{"key": "value"}' : '';
                html += `<textarea class="form-control" id="value" name="value" rows="4" placeholder='${placeholder}' required></textarea>`;
            } else {
                html += `<input type="text" class="form-control" id="value" name="value" required>`;
            }
            
            container.html(html);
            if (initialValue !== '' && type !== 'image') {
                $('#value').val(initialValue);
            }
        }

        // Hàm Sửa (Lấy dữ liệu & Đổ vào Modal)
        function editSetting(id) {
            $.get('{{ url('admin/api/settings') }}/' + id, function(response) {
                if(response.success) {
                    var data = response.data;
                    $('#setting_id').val(data.id);
                    $('#key').val(data.key);
                    $('#type').val(data.type);
                    renderDynamicInput(data.value);
                    $('#modalTitle').text('Sửa cài đặt: ' + data.key);
                    $('#settingModal').modal('show');
                }
            });
        }

        // Hàm Lưu (Thêm/Sửa bằng Ajax)
        function saveSetting() {
            var form = $('#settingForm')[0];
            var formData = new FormData(form);
            
            $.ajax({
                url: '{{ route('admin.api.settings.store') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.success) {
                        $('#settingModal').modal('hide');
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
        function deleteSetting(id) {
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
                        url: '{{ url('admin/api/settings') }}/' + id,
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
