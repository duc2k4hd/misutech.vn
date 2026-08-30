@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .contact-stat-card { border-radius: 8px; padding: 18px; color: #fff; margin-bottom: 20px; transition: transform 0.2s; }
        .contact-stat-card:hover { transform: translateY(-3px); }
        .contact-stat-card h3 { font-size: 24px; font-weight: 800; margin-bottom: 4px; color: #fff; }
        .contact-stat-card p { margin: 0; font-size: 13px; opacity: 0.9; }

        .stat-blue { background: linear-gradient(135deg, #003b70 0%, #0284c7 100%); }
        .stat-amber { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
        .stat-cyan { background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%); }
        .stat-green { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }

        .badge-pending { background: #fee2e2; color: #dc2626; font-weight: 700; }
        .badge-contacted { background: #e0f2fe; color: #0284c7; font-weight: 700; }
        .badge-completed { background: #dcfce7; color: #16a34a; font-weight: 700; }
        .badge-cancelled { background: #f1f5f9; color: #64748b; font-weight: 700; }

        .action-btns .btn { padding: 4px 8px; font-size: 12px; margin-right: 4px; }
        .table td { vertical-align: middle; }

        .contact-message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; white-space: pre-wrap; font-size: 13.5px; color: #1e293b; line-height: 1.6; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản Lý Yêu Cầu Liên Hệ & Tư Vấn</h4>
                <span class="ml-1">Danh sách tin nhắn, câu hỏi và yêu cầu tư vấn kỹ thuật từ khách hàng</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Liên hệ</a></li>
            </ol>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="contact-stat-card stat-blue">
                <h3 id="statTotal">0</h3>
                <p>Tổng số tin nhắn liên hệ</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="contact-stat-card stat-amber">
                <h3 id="statPending">0</h3>
                <p>Chờ xử lý / Mới gửi</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="contact-stat-card stat-cyan">
                <h3 id="statContacted">0</h3>
                <p>Đã liên hệ phản hồi</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="contact-stat-card stat-green">
                <h3 id="statCompleted">0</h3>
                <p>Đã giải quyết xong</p>
            </div>
        </div>
    </div>

    <!-- Danh sách liên hệ -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách Tin Nhắn Liên Hệ</h4>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm mr-2" id="btnBulkDelete" onclick="bulkDelete()" style="display: none;">
                            <i class="mdi mdi-delete"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadContacts()">
                            <i class="mdi mdi-refresh"></i> Làm mới
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter bar -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-control" onchange="loadContacts()">
                                <option value="all">-- Tất cả trạng thái --</option>
                                <option value="pending">Chờ xử lý (Mới)</option>
                                <option value="contacted">Đã liên hệ</option>
                                <option value="completed">Đã hoàn tất</option>
                                <option value="cancelled">Hủy / Spam</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="contactsTable" class="display" style="min-width: 950px">
                            <thead>
                                <tr>
                                    <th style="width: 20px;"><input type="checkbox" id="checkAll" onclick="toggleCheckAll(this)"></th>
                                    <th>Khách Hàng</th>
                                    <th>Điện Thoại</th>
                                    <th>Email</th>
                                    <th>Tiêu Đề</th>
                                    <th>Thời Gian</th>
                                    <th>Trạng Thái</th>
                                    <th style="width: 140px;">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dữ liệu qua AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chi Tiết Liên Hệ -->
    <div class="modal fade" id="contactDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Chi Tiết Tin Nhắn Liên Hệ</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Khách hàng:</small>
                            <div class="font-weight-bold text-dark" style="font-size: 15px;" id="cName">---</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Thời gian gửi:</small>
                            <div class="font-weight-bold text-dark" id="cCreatedAt">---</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Số điện thoại:</small>
                            <div>
                                <a href="#" id="cPhoneLink" class="font-weight-bold text-primary" style="font-size: 15px;">---</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Email:</small>
                            <div>
                                <a href="#" id="cEmailLink" class="font-weight-bold text-info">---</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Địa chỉ IP:</small>
                            <div class="text-dark" id="cIp">---</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted">Thiết bị & Nguồn:</small>
                            <div class="text-dark" id="cDevice">---</div>
                        </div>
                        <div class="col-12 mb-3">
                            <small class="text-muted">Chủ đề / Tiêu đề:</small>
                            <div class="font-weight-bold text-dark" style="font-size: 15px;" id="cSubject">---</div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold text-dark">Nội dung tin nhắn:</label>
                            <div class="contact-message-box" id="cMessage">---</div>
                        </div>
                        <div class="col-12 mt-2">
                            <label class="font-weight-bold text-dark mb-1">Cập nhật trạng thái xử lý:</label>
                            <div class="d-flex">
                                <select class="form-control form-control-sm mr-2" id="cStatusSelect">
                                    <option value="pending">Chờ xử lý</option>
                                    <option value="contacted">Đã liên hệ lại với khách</option>
                                    <option value="completed">Đã hoàn tất tư vấn / Giải quyết</option>
                                    <option value="cancelled">Hủy / Đánh dấu Spam</option>
                                </select>
                                <button type="button" class="btn btn-primary btn-sm" onclick="saveContactStatus()">Cập nhật</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <a href="#" id="btnCallDirect" class="btn btn-success"><i class="mdi mdi-phone"></i> Gọi Điện Thoại</a>
                    <a href="#" id="btnEmailDirect" class="btn btn-info"><i class="mdi mdi-email"></i> Gửi Email</a>
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
        let currentContactId = null;
        let selectedIds = [];

        $(document).ready(function() {
            table = $('#contactsTable').DataTable({
                pageLength: 20,
                ordering: false,
                language: {
                    search: "Tìm kiếm:",
                    lengthMenu: "Hiển thị _MENU_ mục",
                    info: "Hiển thị _START_ đến _END_ trong _TOTAL_ liên hệ",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Sau",
                        previous: "Trước"
                    },
                    emptyTable: "Chưa có liên hệ nào từ khách hàng"
                }
            });

            loadContacts();
        });

        function loadContacts() {
            const status = $('#filterStatus').val();
            $.ajax({
                url: "{{ route('admin.api.contacts.list') }}",
                type: 'GET',
                data: { status: status },
                success: function(response) {
                    if (response.stats) {
                        $('#statTotal').text(response.stats.total);
                        $('#statPending').text(response.stats.pending);
                        $('#statContacted').text(response.stats.contacted);
                        $('#statCompleted').text(response.stats.completed);
                    }

                    table.clear();
                    selectedIds = [];
                    updateBulkButton();
                    $('#checkAll').prop('checked', false);

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            let statusBadge = '';
                            if (item.status === 'pending') {
                                statusBadge = '<span class="badge badge-pending">Chờ xử lý</span>';
                            } else if (item.status === 'contacted') {
                                statusBadge = '<span class="badge badge-contacted">Đã liên hệ</span>';
                            } else if (item.status === 'completed') {
                                statusBadge = '<span class="badge badge-completed">Hoàn tất</span>';
                            } else if (item.status === 'cancelled') {
                                statusBadge = '<span class="badge badge-cancelled">Hủy/Spam</span>';
                            }

                            const createdAt = new Date(item.created_at).toLocaleString('vi-VN', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            });

                            const subjectSnippet = `<strong>${item.subject || 'Không có tiêu đề'}</strong><br><small class="text-muted">${(item.message || '').substring(0, 50)}...</small>`;

                            const actions = `
                                <div class="action-btns">
                                    <button class="btn btn-info" onclick="viewContactDetail(${item.id})" title="Xem chi tiết"><i class="mdi mdi-eye"></i> Xem</button>
                                    <button class="btn btn-danger" onclick="deleteContact(${item.id})" title="Xóa"><i class="mdi mdi-delete"></i></button>
                                </div>
                            `;

                            table.row.add([
                                `<input type="checkbox" class="contact-check-item" value="${item.id}" onchange="toggleCheckItem(${item.id})">`,
                                `<strong>${item.name}</strong>`,
                                `<a href="tel:${item.phone}" class="text-primary font-weight-bold">${item.phone || '---'}</a>`,
                                `<a href="mailto:${item.email}" class="text-info">${item.email || '---'}</a>`,
                                subjectSnippet,
                                `<small class="text-muted">${createdAt}</small>`,
                                statusBadge,
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

        function viewContactDetail(id) {
            currentContactId = id;
            $.ajax({
                url: `{{ url('admin/api/contacts') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    const c = response.data;
                    $('#cName').text(c.name);
                    $('#cCreatedAt').text(new Date(c.created_at).toLocaleString('vi-VN'));
                    $('#cPhoneLink').text(c.phone || '---').attr('href', c.phone ? `tel:${c.phone}` : '#');
                    $('#btnCallDirect').attr('href', c.phone ? `tel:${c.phone}` : '#');
                    $('#cEmailLink').text(c.email || '---').attr('href', c.email ? `mailto:${c.email}` : '#');
                    $('#btnEmailDirect').attr('href', c.email ? `mailto:${c.email}` : '#');
                    $('#cIp').text(c.ip_address || '127.0.0.1');
                    $('#cDevice').text(`${(c.device_type || 'Desktop').toUpperCase()} ${c.referer ? `(Từ: ${c.referer})` : ''}`);
                    $('#cSubject').text(c.subject || 'Không có tiêu đề');
                    $('#cMessage').text(c.message || 'Không có nội dung');
                    $('#cStatusSelect').val(c.status);

                    $('#contactDetailModal').modal('show');
                },
                error: function() {
                    toastr.error('Không thể lấy chi tiết tin nhắn');
                }
            });
        }

        function saveContactStatus() {
            if (!currentContactId) return;
            const status = $('#cStatusSelect').val();

            $.ajax({
                url: `{{ url('admin/api/contacts') }}/${currentContactId}/status`,
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#contactDetailModal').modal('hide');
                    loadContacts();
                },
                error: function() {
                    toastr.error('Lỗi khi cập nhật trạng thái liên hệ');
                }
            });
        }

        function deleteContact(id) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa tin nhắn liên hệ này?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{ url('admin/api/contacts') }}/${id}`,
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

        function toggleCheckAll(master) {
            const isChecked = $(master).is(':checked');
            $('.contact-check-item').prop('checked', isChecked);
            selectedIds = [];
            if (isChecked) {
                $('.contact-check-item').each(function() {
                    selectedIds.push(parseInt($(this).val()));
                });
            }
            updateBulkButton();
        }

        function toggleCheckItem(id) {
            const index = selectedIds.indexOf(id);
            if (index > -1) {
                selectedIds.splice(index, 1);
            } else {
                selectedIds.push(id);
            }
            updateBulkButton();
        }

        function updateBulkButton() {
            if (selectedIds.length > 0) {
                $('#selectedCount').text(selectedIds.length);
                $('#btnBulkDelete').show();
            } else {
                $('#btnBulkDelete').hide();
            }
        }

        function bulkDelete() {
            if (selectedIds.length === 0) return;
            Swal.fire({
                title: `Xác nhận xóa ${selectedIds.length} liên hệ?`,
                text: "Thao tác này không thể hoàn tác!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Đồng ý xóa hàng loạt',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{ route('admin.api.contacts.bulk.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            loadContacts();
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa hàng loạt liên hệ');
                        }
                    });
                }
            });
        }
    </script>
@endsection
