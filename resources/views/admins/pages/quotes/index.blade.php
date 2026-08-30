@extends('admins.layouts.master')

@section('styles')
    <!-- Datatable -->
    <link href="{{ asset('admins/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .quote-stat-card { border-radius: 8px; padding: 18px; color: #fff; margin-bottom: 20px; transition: transform 0.2s; }
        .quote-stat-card:hover { transform: translateY(-3px); }
        .quote-stat-card h3 { font-size: 24px; font-weight: 800; margin-bottom: 4px; color: #fff; }
        .quote-stat-card p { margin: 0; font-size: 13px; opacity: 0.9; }
        
        .stat-blue { background: linear-gradient(135deg, #003b70 0%, #0284c7 100%); }
        .stat-amber { background: linear-gradient(135deg, #d97706 0%, #f59e0b 100%); }
        .stat-green { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
        .stat-purple { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }

        .badge-draft { background: #e2e8f0; color: #475569; font-weight: 700; }
        .badge-sent { background: #e0f2fe; color: #0284c7; font-weight: 700; }
        .badge-confirmed { background: #dcfce7; color: #16a34a; font-weight: 700; }
        .badge-completed { background: #f0fdf4; color: #15803d; font-weight: 700; border: 1px solid #bbf7d0; }
        .badge-cancelled { background: #fee2e2; color: #dc2626; font-weight: 700; }

        .quote-code-badge { background: #f1f5f9; color: #003b70; font-weight: 800; padding: 4px 8px; border-radius: 4px; border: 1px solid #cbd5e1; cursor: pointer; }
        .quote-code-badge:hover { background: #003b70; color: #fff; }
        
        .action-btns .btn { padding: 4px 8px; font-size: 12px; margin-right: 4px; }
        .table td { vertical-align: middle; }
        
        .quote-detail-section { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; margin-bottom: 16px; }
        .quote-detail-label { font-size: 11.5px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px; }
        .quote-detail-value { font-size: 14px; font-weight: 600; color: #1e293b; }
    </style>
@endsection

@section('content')
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Quản Lý Báo Giá Trực Tuyến & Dự Án</h4>
                <span class="ml-1">Danh sách bảng báo giá tự động do khách hàng tự lập và yêu cầu từ website</span>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Báo giá</a></li>
            </ol>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="quote-stat-card stat-blue">
                <h3 id="statTotal">0</h3>
                <p>Tổng số bảng báo giá</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="quote-stat-card stat-amber">
                <h3 id="statDraft">0</h3>
                <p>Báo giá mới lập (Draft)</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="quote-stat-card stat-green">
                <h3 id="statConfirmed">0</h3>
                <p>Báo giá đã xác nhận / chốt</p>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="quote-stat-card stat-purple">
                <h3 id="statTotalAmount">0 đ</h3>
                <p>Tổng giá trị ước tính</p>
            </div>
        </div>
    </div>

    <!-- Danh sách báo giá -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Danh sách Báo Giá</h4>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-danger btn-sm mr-2" id="btnBulkDelete" onclick="bulkDelete()" style="display: none;">
                            <i class="mdi mdi-delete"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadQuotes()">
                            <i class="mdi mdi-refresh"></i> Làm mới
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter bar -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="filterStatus" class="form-control" onchange="loadQuotes()">
                                <option value="all">-- Tất cả trạng thái --</option>
                                <option value="draft">Mới lập (Draft)</option>
                                <option value="sent">Đã gửi khách</option>
                                <option value="confirmed">Đã chốt đơn</option>
                                <option value="completed">Đã hoàn tất</option>
                                <option value="cancelled">Đã hủy</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="quotesTable" class="display" style="min-width: 950px">
                            <thead>
                                <tr>
                                    <th style="width: 20px;"><input type="checkbox" id="checkAll" onclick="toggleCheckAll(this)"></th>
                                    <th>Mã Báo Giá</th>
                                    <th>Khách Hàng / Công Ty</th>
                                    <th>Số Điện Thoại</th>
                                    <th>Số Món</th>
                                    <th>Tổng Tiền</th>
                                    <th>Ngày Tạo</th>
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

    <!-- Modal Chi Tiết Báo Giá -->
    <div class="modal fade" id="quoteDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="modalQuoteCode">Chi Tiết Báo Giá: #...</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Thông tin khách hàng -->
                        <div class="col-md-6">
                            <div class="quote-detail-section">
                                <div class="quote-detail-label">Thông tin khách hàng & Doanh nghiệp</div>
                                <div class="row mt-2">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Họ và tên:</small>
                                        <div class="quote-detail-value" id="qCustomerName">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Số điện thoại:</small>
                                        <div class="quote-detail-value" id="qCustomerPhone">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Email:</small>
                                        <div class="quote-detail-value" id="qCustomerEmail">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Công ty / Đơn vị:</small>
                                        <div class="quote-detail-value" id="qCustomerCompany">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Mã số thuế:</small>
                                        <div class="quote-detail-value" id="qCustomerTax">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Địa chỉ:</small>
                                        <div class="quote-detail-value" id="qCustomerAddress">---</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thông tin hệ thống & Telemetry -->
                        <div class="col-md-6">
                            <div class="quote-detail-section">
                                <div class="quote-detail-label">Thông tin phân tích & Trạng thái xử lý</div>
                                <div class="row mt-2">
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Thời gian lập:</small>
                                        <div class="quote-detail-value" id="qCreatedAt">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Địa chỉ IP:</small>
                                        <div class="quote-detail-value" id="qIpAddress">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Thiết bị:</small>
                                        <div class="quote-detail-value" id="qDeviceType">---</div>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <small class="text-muted">Thời gian onsite:</small>
                                        <div class="quote-detail-value" id="qDuration">---</div>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <label class="font-weight-bold text-dark mb-1">Cập nhật trạng thái xử lý:</label>
                                        <div class="d-flex">
                                            <select class="form-control form-control-sm mr-2" id="qStatusSelect">
                                                <option value="draft">Mới lập (Draft)</option>
                                                <option value="sent">Đã gửi báo giá cho khách</option>
                                                <option value="confirmed">Khách đã chốt đơn / Đặt cọc</option>
                                                <option value="completed">Đã hoàn tất đơn hàng</option>
                                                <option value="cancelled">Hủy báo giá</option>
                                            </select>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="saveQuoteStatus()">Lưu</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng danh sách sản phẩm trong báo giá -->
                    <h5 class="font-weight-bold mt-2 mb-3">Danh Mục Thiết Bị Báo Giá</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="qItemsTable">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 40px;">STT</th>
                                    <th>Tên Thiết Bị / Model</th>
                                    <th>Thương Hiệu</th>
                                    <th class="text-right">Đơn Giá (VNĐ)</th>
                                    <th class="text-center" style="width: 90px;">Số Lượng</th>
                                    <th class="text-right">Thành Tiền (VNĐ)</th>
                                </tr>
                            </thead>
                            <tbody id="qItemsBody">
                                <!-- Render danh sách sản phẩm -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Tổng tiền hàng:</td>
                                    <td class="text-right font-weight-bold" id="qSubtotal">0 đ</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right text-danger font-weight-bold">Chiết khấu (<span id="qDiscountPercent">0</span>%):</td>
                                    <td class="text-right text-danger font-weight-bold" id="qDiscountAmount">-0 đ</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-weight-bold">Thuế VAT (<span id="qVatPercent">0</span>%):</td>
                                    <td class="text-right font-weight-bold" id="qVatAmount">+0 đ</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-right font-weight-bold" style="font-size: 15px;">TỔNG CỘNG THANH TOÁN:</td>
                                    <td class="text-right font-weight-bold text-primary" style="font-size: 16px;" id="qTotalAmount">0 đ</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Ghi chú nội bộ -->
                    <div class="form-group mt-3">
                        <label class="font-weight-bold">Ghi chú của tư vấn viên / Nhân viên kinh doanh:</label>
                        <textarea class="form-control" id="qNotesInput" rows="2" placeholder="Ghi chú về khách hàng, yêu cầu giao hàng hoặc tiến độ báo giá..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-success" onclick="printQuoteDetail()">
                        <i class="mdi mdi-printer"></i> In / Xuất PDF
                    </button>
                    <button type="button" class="btn btn-primary" onclick="saveQuoteStatus()">Lưu Trạng Thái & Ghi Chú</button>
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
        let currentQuoteId = null;
        let selectedIds = [];

        $(document).ready(function() {
            table = $('#quotesTable').DataTable({
                pageLength: 20,
                ordering: false,
                language: {
                    search: "Tìm kiếm:",
                    lengthMenu: "Hiển thị _MENU_ mục",
                    info: "Hiển thị _START_ đến _END_ trong _TOTAL_ báo giá",
                    paginate: {
                        first: "Đầu",
                        last: "Cuối",
                        next: "Sau",
                        previous: "Trước"
                    },
                    emptyTable: "Chưa có bản ghi báo giá nào"
                }
            });

            loadQuotes();
        });

        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        function loadQuotes() {
            const status = $('#filterStatus').val();
            $.ajax({
                url: "{{ route('admin.api.quotes.list') }}",
                type: 'GET',
                data: { status: status },
                success: function(response) {
                    // Update stats
                    if (response.stats) {
                        $('#statTotal').text(response.stats.total);
                        $('#statDraft').text(response.stats.draft);
                        $('#statConfirmed').text(response.stats.confirmed);
                        $('#statTotalAmount').text(formatMoney(response.stats.total_amount));
                    }

                    table.clear();
                    selectedIds = [];
                    updateBulkButton();
                    $('#checkAll').prop('checked', false);

                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(item) {
                            let statusBadge = '';
                            if (item.status === 'draft') {
                                statusBadge = '<span class="badge badge-draft">Mới lập</span>';
                            } else if (item.status === 'sent') {
                                statusBadge = '<span class="badge badge-sent">Đã gửi</span>';
                            } else if (item.status === 'confirmed') {
                                statusBadge = '<span class="badge badge-confirmed">Đã chốt</span>';
                            } else if (item.status === 'completed') {
                                statusBadge = '<span class="badge badge-completed">Hoàn tất</span>';
                            } else if (item.status === 'cancelled') {
                                statusBadge = '<span class="badge badge-cancelled">Đã hủy</span>';
                            }

                            const customerInfo = `
                                <strong>${item.customer_name || 'Khách vãng lai'}</strong>
                                ${item.customer_company ? `<br><small class="text-muted">${item.customer_company}</small>` : ''}
                            `;

                            const createdAt = new Date(item.created_at).toLocaleString('vi-VN', {
                                day: '2-digit', month: '2-digit', year: 'numeric',
                                hour: '2-digit', minute: '2-digit'
                            });

                            const actions = `
                                <div class="action-btns">
                                    <button class="btn btn-info" onclick="viewQuoteDetail(${item.id})" title="Xem chi tiết"><i class="mdi mdi-eye"></i> Xem</button>
                                    <button class="btn btn-danger" onclick="deleteQuote(${item.id})" title="Xóa"><i class="mdi mdi-delete"></i></button>
                                </div>
                            `;

                            table.row.add([
                                `<input type="checkbox" class="quote-check-item" value="${item.id}" onchange="toggleCheckItem(${item.id})">`,
                                `<span class="quote-code-badge" onclick="viewQuoteDetail(${item.id})">${item.quote_code}</span>`,
                                customerInfo,
                                `<a href="tel:${item.customer_phone}" class="font-weight-bold text-primary">${item.customer_phone || '---'}</a>`,
                                `<span class="badge badge-light">${item.items_count || 0} món</span>`,
                                `<strong class="text-dark font-weight-bold">${formatMoney(item.total_amount)}</strong>`,
                                `<small class="text-muted">${createdAt}</small>`,
                                statusBadge,
                                actions
                            ]);
                        });
                    }
                    table.draw();
                },
                error: function() {
                    toastr.error('Không thể tải danh sách báo giá');
                }
            });
        }

        function viewQuoteDetail(id) {
            currentQuoteId = id;
            $.ajax({
                url: `{{ url('admin/api/quotes') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    const q = response.data;
                    $('#modalQuoteCode').text(`Chi Tiết Báo Giá: #${q.quote_code}`);
                    $('#qCustomerName').text(q.customer_name || '---');
                    $('#qCustomerPhone').text(q.customer_phone || '---');
                    $('#qCustomerEmail').text(q.customer_email || '---');
                    $('#qCustomerCompany').text(q.customer_company || '---');
                    $('#qCustomerTax').text(q.customer_tax_code || '---');
                    $('#qCustomerAddress').text(q.customer_address || '---');

                    const createdAt = new Date(q.created_at).toLocaleString('vi-VN');
                    $('#qCreatedAt').text(createdAt);
                    $('#qIpAddress').text(q.ip_address || '127.0.0.1');
                    $('#qDeviceType').text((q.device_type || 'Desktop').toUpperCase());
                    $('#qDuration').text(q.duration_seconds ? `${q.duration_seconds} giây` : '---');

                    $('#qStatusSelect').val(q.status);
                    $('#qNotesInput').val(q.notes || '');

                    // Render Items
                    let itemsHtml = '';
                    if (q.items && q.items.length > 0) {
                        q.items.forEach(function(item, index) {
                            itemsHtml += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>
                                        <strong>${item.product_name}</strong>
                                        ${item.product_model ? `<br><small class="text-muted">Model: ${item.product_model}</small>` : ''}
                                    </td>
                                    <td><span class="badge badge-light">${item.product_brand || 'Chính hãng'}</span></td>
                                    <td class="text-right">${formatMoney(item.unit_price)}</td>
                                    <td class="text-center font-weight-bold">${item.quantity} ${item.unit || 'Cái'}</td>
                                    <td class="text-right font-weight-bold text-dark">${formatMoney(item.total_price)}</td>
                                </tr>
                            `;
                        });
                    } else {
                        itemsHtml = `<tr><td colspan="6" class="text-center text-muted">Không có sản phẩm</td></tr>`;
                    }
                    $('#qItemsBody').html(itemsHtml);

                    // Calculation
                    $('#qSubtotal').text(formatMoney(q.subtotal));
                    $('#qDiscountPercent').text(q.discount_percent || 0);
                    const discountAmount = q.subtotal * ((q.discount_percent || 0) / 100);
                    $('#qDiscountAmount').text('-' + formatMoney(discountAmount));

                    $('#qVatPercent').text(q.vat_percent || 0);
                    const afterDiscount = q.subtotal - discountAmount;
                    const vatAmount = afterDiscount * ((q.vat_percent || 0) / 100);
                    $('#qVatAmount').text('+' + formatMoney(vatAmount));

                    $('#qTotalAmount').text(formatMoney(q.total_amount));

                    $('#quoteDetailModal').modal('show');
                },
                error: function() {
                    toastr.error('Không thể lấy thông tin chi tiết báo giá');
                }
            });
        }

        function saveQuoteStatus() {
            if (!currentQuoteId) return;
            const status = $('#qStatusSelect').val();
            const notes = $('#qNotesInput').val();

            $.ajax({
                url: `{{ url('admin/api/quotes') }}/${currentQuoteId}/status`,
                type: 'PUT',
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status,
                    notes: notes
                },
                success: function(response) {
                    toastr.success(response.message);
                    $('#quoteDetailModal').modal('hide');
                    loadQuotes();
                },
                error: function() {
                    toastr.error('Lỗi khi cập nhật trạng thái báo giá');
                }
            });
        }

        function deleteQuote(id) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa bản ghi báo giá này?",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: `{{ url('admin/api/quotes') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            loadQuotes();
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa báo giá');
                        }
                    });
                }
            });
        }

        function toggleCheckAll(master) {
            const isChecked = $(master).is(':checked');
            $('.quote-check-item').prop('checked', isChecked);
            selectedIds = [];
            if (isChecked) {
                $('.quote-check-item').each(function() {
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
                title: `Xác nhận xóa ${selectedIds.length} báo giá?`,
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
                        url: "{{ route('admin.api.quotes.bulk.destroy') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            ids: selectedIds
                        },
                        success: function(response) {
                            toastr.success(response.message);
                            loadQuotes();
                        },
                        error: function() {
                            toastr.error('Lỗi khi xóa hàng loạt báo giá');
                        }
                    });
                }
            });
        }

        function printQuoteDetail() {
            window.print();
        }
    </script>
@endsection
