@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .action-btns .btn { padding: 5px 10px; margin-right: 5px; }
        .badge-sale { background-color: #e0f2fe; color: #0284c7; font-weight: 700; }
        .badge-warranty { background-color: #0284c7; color: #ffffff; font-weight: 700; }
        .badge-technical { background-color: #fef3c7; color: #b45309; font-weight: 700; }
        .badge-other { background-color: #f1f5f9; color: #475569; font-weight: 700; }
        .contact-avatar-sm { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0; }
        .table td { vertical-align: middle; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản Lý Hotline & Nhân Viên Tư Vấn</h4>
                <span class="ml-1">Quản lý đội ngũ tư vấn bán hàng, bảo hành kỹ thuật và danh sách popup liên hệ nhanh</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Support Contacts</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách Hotline & Chuyên viên hỗ trợ</h4>
                    <button type="button" class="btn btn-primary" onclick="openModal()">
                        <i class="mdi mdi-plus"></i> + Thêm Nhân Viên / Hotline Mới
                    </button>
                </div>
                <div class="card-body">
                    {{-- Filter Bar --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="filterDepartmentType" class="form-control" onchange="loadContacts()">
                                <option value="">-- Tất cả phòng ban / vị trí --</option>
                                <option value="sale">Phòng Kinh Doanh / Báo Giá (Sale)</option>
                                <option value="warranty">Dịch Vụ Kỹ Thuật / Bảo Hành</option>
                                <option value="technical">Tư Vấn Kỹ Thuật & Lập Trình</option>
                                <option value="other">Vị trí khác</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="contactsTable" class="display" style="min-width: 845px">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Tên Nhân Viên / Phòng Ban</th>
                                    <th>Số Điện Thoại (Call)</th>
                                    <th>Zalo</th>
                                    <th>Phòng Ban / Vị Trí</th>
                                    <th>Loại</th>
                                    <th>Hiện Popup</th>
                                    <th>Trạng Thái</th>
                                    <th style="width: 140px;">Hành Động</th>
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

    <!-- Modal Thêm/Sửa -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm Nhân Viên Tư Vấn Mới</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form id="contactForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="contactId" name="id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tên nhân viên / Tiêu đề Hotline <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactName" name="name" placeholder="Ví dụ: Ms Mai Chi, Dịch vụ kỹ thuật/Bảo hành..." required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Số điện thoại gọi (Call) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactPhone" name="phone" placeholder="Ví dụ: 0325194688" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Số điện thoại Zalo (Nếu trùng số gọi thì để trống)</label>
                                <input type="text" class="form-control" id="contactZalo" name="zalo_phone" placeholder="Ví dụ: 0325194688">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nhóm phân loại <span class="text-danger">*</span></label>
                                <select class="form-control" id="contactDeptType" name="department_type" required onchange="onDeptTypeChange(this.value)">
                                    <option value="sale">Phòng Kinh Doanh / Báo Giá (Sale)</option>
                                    <option value="warranty">Dịch Vụ Kỹ Thuật / Bảo Hành</option>
                                    <option value="technical">Tư Vấn Giải Pháp / Lập Trình</option>
                                    <option value="other">Khác (Tùy chỉnh)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Tên phòng ban hiển thị chi tiết <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contactDept" name="department" value="Phòng Kinh Doanh / Báo Giá" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Ghi chú / Khu vực hỗ trợ (Tùy chọn)</label>
                                <input type="text" class="form-control" id="contactNote" name="note" placeholder="Ví dụ: Tư vấn biến tần, PLC 24/7">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label>Thứ tự hiển thị (Nhỏ đứng trước)</label>
                                <input type="number" class="form-control" id="contactSortOrder" name="sort_order" value="0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Hiển thị trong Popup Nổi</label>
                                <select class="form-control" id="contactShowPopup" name="show_in_popup">
                                    <option value="1" selected>Có (Hiển thị popup)</option>
                                    <option value="0">Không</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label>Trạng thái hoạt động</label>
                                <select class="form-control" id="contactIsActive" name="is_active">
                                    <option value="1" selected>Đang hoạt động</option>
                                    <option value="0">Tạm dừng</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Ảnh đại diện / Icon (Tùy chọn)</label>
                            <input type="file" class="form-control-file" id="contactAvatar" name="avatar_file" accept="image/*">
                            <div id="avatarPreviewWrap" class="mt-2" style="display: none;">
                                <img id="avatarPreview" src="" class="contact-avatar-sm" alt="Preview">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveContact">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- Datatable -->
    <script src="{{ asset('admins/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>

    <script>
        let table;

        $(document).ready(function() {
            table = $('#contactsTable').DataTable({
                pageLength: 20,
                ordering: false,
                language: {
                    search: "Tìm kiếm:",
                    lengthMenu: "Hiển thị _MENU_ mục",
                    info: "Hiển thị _START_ đến _END_ trong _TOTAL_ nhân viên",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Sau",
                        previous: "Trước"
                    },
                    emptyTable: "Chưa có dữ liệu liên hệ"
                }
            });

            loadContacts();

            $('#contactForm').on('submit', function(e) {
                e.preventDefault();
                saveContact();
            });
        });

        function onDeptTypeChange(val) {
            const deptInput = $('#contactDept');
            if (val === 'sale') {
                deptInput.val('Phòng Kinh Doanh / Báo Giá');
            } else if (val === 'warranty') {
                deptInput.val('Dịch Vụ Kỹ Thuật / Bảo Hành');
            } else if (val === 'technical') {
                deptInput.val('Tư Vấn Giải Pháp Kỹ Thuật');
            }
        }

        function loadContacts() {
            const deptType = $('#filterDepartmentType').val();
            $.ajax({
                url: "{{ route('admin.api.support_contacts.list') }}",
                type: 'GET',
                data: { department_type: deptType },
                success: function(response) {
                    table.clear();
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            let badgeClass = 'badge-sale';
                            let badgeLabel = 'Sale / Báo giá';
                            if (item.department_type === 'warranty') {
                                badgeClass = 'badge-warranty';
                                badgeLabel = 'Bảo hành / Dịch vụ';
                            } else if (item.department_type === 'technical') {
                                badgeClass = 'badge-technical';
                                badgeLabel = 'Kỹ thuật';
                            } else if (item.department_type === 'other') {
                                badgeClass = 'badge-other';
                                badgeLabel = 'Khác';
                            }

                            const popupBadge = item.show_in_popup 
                                ? `<span class="badge badge-success cursor-pointer" onclick="toggleStatus(${item.id}, 'show_in_popup')">Hiển thị</span>`
                                : `<span class="badge badge-secondary cursor-pointer" onclick="toggleStatus(${item.id}, 'show_in_popup')">Ẩn</span>`;

                            const activeBadge = item.is_active 
                                ? `<span class="badge badge-success cursor-pointer" onclick="toggleStatus(${item.id}, 'is_active')">Hoạt động</span>`
                                : `<span class="badge badge-danger cursor-pointer" onclick="toggleStatus(${item.id}, 'is_active')">Tạm tắt</span>`;

                            const actions = `
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-info" onclick="editContact(${item.id})" title="Chỉnh sửa"><i class="mdi mdi-pencil"></i> Sửa</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteContact(${item.id})" title="Xóa"><i class="mdi mdi-delete"></i> Xóa</button>
                                </div>
                            `;

                            table.row.add([
                                item.sort_order,
                                `<strong>${item.name}</strong> ${item.note ? `<br><small class="text-muted">${item.note}</small>` : ''}`,
                                `<a href="tel:${item.phone}" class="text-primary font-weight-bold">📞 ${item.phone}</a>`,
                                `<a href="https://zalo.me/${item.zalo_phone || item.phone}" target="_blank" class="text-info font-weight-bold">💬 ${item.zalo_phone || item.phone}</a>`,
                                item.department,
                                `<span class="badge ${badgeClass}">${badgeLabel}</span>`,
                                popupBadge,
                                activeBadge,
                                actions
                            ]);
                        });
                    }
                    table.draw();
                },
                error: function() {
                    toastr.error('Không thể tải danh sách liên hệ');
                }
            });
        }

        function openModal() {
            $('#contactForm')[0].reset();
            $('#contactId').val('');
            $('#modalTitle').text('Thêm Nhân Viên Tư Vấn Mới');
            $('#contactDept').val('Phòng Kinh Doanh / Báo Giá');
            $('#contactDeptType').val('sale');
            $('#contactSortOrder').val('0');
            $('#contactShowPopup').val('1');
            $('#contactIsActive').val('1');
            $('#avatarPreviewWrap').hide();
            $('#contactModal').modal('show');
        }

        function editContact(id) {
            $.ajax({
                url: `{{ url('admin/api/support-contacts') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    const data = response.data;
                    $('#contactId').val(data.id);
                    $('#contactName').val(data.name);
                    $('#contactPhone').val(data.phone);
                    $('#contactZalo').val(data.zalo_phone);
                    $('#contactDeptType').val(data.department_type);
                    $('#contactDept').val(data.department);
                    $('#contactNote').val(data.note);
                    $('#contactSortOrder').val(data.sort_order);
                    $('#contactShowPopup').val(data.show_in_popup ? '1' : '0');
                    $('#contactIsActive').val(data.is_active ? '1' : '0');

                    if (data.avatar) {
                        $('#avatarPreview').attr('src', `{{ asset('storage/clients/imgs/support') }}/${data.avatar}`);
                        $('#avatarPreviewWrap').show();
                    } else {
                        $('#avatarPreviewWrap').hide();
                    }

                    $('#modalTitle').text('Chỉnh Sửa Thông Tin Nhân Viên');
                    $('#contactModal').modal('show');
                },
                error: function() {
                    toastr.error('Không thể lấy thông tin liên hệ');
                }
            });
        }

        function saveContact() {
            const formData = new FormData($('#contactForm')[0]);
            $('#btnSaveContact').prop('disabled', true).text('Đang lưu...');

            $.ajax({
                url: "{{ route('admin.api.support_contacts.store') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#btnSaveContact').prop('disabled', false).text('Lưu thông tin');
                    $('#contactModal').modal('hide');
                    toastr.success(response.message);
                    loadContacts();
                },
                error: function(xhr) {
                    $('#btnSaveContact').prop('disabled', false).text('Lưu thông tin');
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Có lỗi xảy ra, vui lòng thử lại');
                    }
                }
            });
        }

        function toggleStatus(id, field) {
            $.ajax({
                url: `{{ url('admin/api/support-contacts/toggle-status') }}/${id}`,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    field: field
                },
                success: function(response) {
                    toastr.success(response.message);
                    loadContacts();
                },
                error: function() {
                    toastr.error('Lỗi cập nhật trạng thái');
                }
            });
        }

        function deleteContact(id) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa nhân viên / hotline này?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{ url('admin/api/support-contacts') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            loadContacts();
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa liên hệ');
                        }
                    });
                }
            });
        }
    </script>
@endsection
