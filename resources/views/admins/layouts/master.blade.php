<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Misutech Admin')</title>
    <!-- Favicon icon -->
    @php
        $faviconUrl = !empty($settings->site_favicon) 
            ? (Str::startsWith($settings->site_favicon, ['http://', 'https://']) ? $settings->site_favicon : asset('storage/clients/imgs/settings/' . $settings->site_favicon))
            : asset('storage/clients/imgs/settings/favicon.png');
    @endphp
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconUrl }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $faviconUrl }}">
    <link rel="icon" href="{{ $faviconUrl }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('admins/vendor/owl-carousel/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admins/vendor/owl-carousel/css/owl.theme.default.min.css') }}">
    <link href="{{ asset('admins/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admins/css/style.css') }}" rel="stylesheet">

    <!-- Common Admin Vendor Styles -->
    <link rel="stylesheet" href="{{ asset('admins/vendor/toastr/css/toastr.min.css') }}">
    <link href="{{ asset('admins/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/2.8.2/slimselect.min.css" rel="stylesheet">

    @yield('styles')
    @stack('styles')
</head>

<body>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->


    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
        @php
            $adminSetting = \App\Models\Setting::first();
            $adminLogoUrl = !empty($adminSetting->site_logo) ? (Str::startsWith($adminSetting->site_logo, ['http://', 'https://']) ? $adminSetting->site_logo : asset('storage/clients/imgs/settings/' . $adminSetting->site_logo)) : asset('storage/clients/imgs/settings/logo-misutech.png');
            $adminFaviconUrl = !empty($adminSetting->site_favicon) ? (Str::startsWith($adminSetting->site_favicon, ['http://', 'https://']) ? $adminSetting->site_favicon : asset('storage/clients/imgs/settings/' . $adminSetting->site_favicon)) : asset('storage/clients/imgs/settings/favicon-misutech.png');
        @endphp
        <div class="nav-header">
            <a href="{{ route('admin.dashboard') }}" class="brand-logo" title="{{ $adminSetting->company ?? 'MISUTECH' }}">
                <img class="logo-abbr" src="{{ $adminFaviconUrl }}" alt="MISUTECH" style="max-height: 38px; width: auto; object-fit: contain; background: #ffffff; border-radius: 6px; padding: 2px;">
                <img class="logo-compact" src="{{ $adminLogoUrl }}" alt="MISUTECH" style="max-height: 38px; max-width: 100%; width: auto; object-fit: contain; background: #ffffff; padding: 4px 8px; border-radius: 6px;">
                <img class="brand-title" src="{{ $adminLogoUrl }}" alt="MISUTECH" style="max-width: 200px; width: auto; object-fit: contain; background: #ffffff; padding: 4px 8px; border-radius: 6px; margin-left: 10px;">
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

        <!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="admin_global_search_wrap position-relative my-auto">
                                <div class="input-group align-items-center" style="width: 420px; max-width: 100%; position: relative;">
                                    <span class="position-absolute text-muted" style="left: 12px; top: 9px; z-index: 5; pointer-events: none;">
                                        <i class="mdi mdi-magnify" style="font-size: 18px;"></i>
                                    </span>
                                    <input type="search" id="adminGlobalSearchInput" class="form-control border-0 bg-light rounded-pill pl-5 pr-5" 
                                        placeholder="Tìm sản phẩm, báo giá, liên hệ, bài viết... (Ctrl+K)" 
                                        style="height: 38px; font-size: 13px; box-shadow: none;" autocomplete="off">
                                    <span class="position-absolute d-none d-lg-inline-block text-muted" style="right: 12px; top: 10px; font-size: 10px; pointer-events: none; opacity: 0.7; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-family: monospace; font-weight: bold;">Ctrl K</span>
                                </div>

                                <!-- Live Search Results Dropdown -->
                                <div id="adminGlobalSearchResults" class="dropdown-menu shadow-lg border-0 p-0" 
                                    style="width: 480px; max-width: 90vw; max-height: 460px; overflow-y: auto; display: none; position: absolute; top: 100%; left: 0; margin-top: 8px; z-index: 1050; border-radius: 10px;">
                                </div>
                            </div>
                        </div>

                        @php
                            $realQuotes = \App\Models\Quote::latest()->take(5)->get();
                            $realContacts = \App\Models\Contact::latest()->take(5)->get();
                            
                            $realNotifs = collect();
                            foreach ($realQuotes as $q) {
                                $realNotifs->push((object)[
                                    'type'    => 'quote',
                                    'title'   => ($q->customer_name ?: 'Khách hàng') . ' lập báo giá',
                                    'desc'    => '#' . $q->quote_code . ' - ' . number_format($q->total_amount, 0, ',', '.') . ' đ',
                                    'time'    => $q->created_at,
                                    'url'     => route('admin.quotes.index'),
                                    'icon'    => 'mdi mdi-file-document-box',
                                    'badge'   => 'primary',
                                    'is_new'  => in_array($q->status, ['draft', 'submitted']),
                                ]);
                            }
                            foreach ($realContacts as $c) {
                                $realNotifs->push((object)[
                                    'type'    => 'contact',
                                    'title'   => $c->name . ' gửi liên hệ',
                                    'desc'    => Str::limit($c->subject ?: ($c->message ?: 'Yêu cầu tư vấn'), 35),
                                    'time'    => $c->created_at,
                                    'url'     => route('admin.contacts.index'),
                                    'icon'    => 'mdi mdi-email-open',
                                    'badge'   => 'success',
                                    'is_new'  => $c->status === 'pending',
                                ]);
                            }
                            $realNotifs = $realNotifs->sortByDesc('time')->take(6);
                            $pendingCount = \App\Models\Quote::whereIn('status', ['draft', 'submitted'])->count() + \App\Models\Contact::where('status', 'pending')->count();
                        @endphp

                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown" title="{{ $pendingCount }} thông báo mới">
                                    <i class="mdi mdi-bell"></i>
                                    @if($pendingCount > 0)
                                        <div class="pulse-css"></div>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" style="min-width: 320px; max-width: 360px;">
                                    <div class="dropdown-header d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                                        <h6 class="mb-0 font-weight-bold text-dark">Thông Báo Mới</h6>
                                        @if($pendingCount > 0)
                                            <span class="badge badge-danger badge-pill">{{ $pendingCount }} mới</span>
                                        @endif
                                    </div>
                                    <ul class="list-unstyled mb-0" style="max-height: 340px; overflow-y: auto;">
                                        @forelse($realNotifs as $notif)
                                            <li class="media dropdown-item py-2 px-3 border-bottom d-flex align-items-center" style="{{ $notif->is_new ? 'background: #f8fafc;' : '' }}">
                                                <span class="{{ $notif->badge }} mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 50%;">
                                                    <i class="{{ $notif->icon }}" style="font-size: 16px;"></i>
                                                </span>
                                                <div class="media-body" style="font-size: 13px;">
                                                    <a href="{{ $notif->url }}" class="text-dark d-block text-decoration-none">
                                                        <p class="mb-0 font-weight-bold" style="line-height: 1.3;">{{ $notif->title }}</p>
                                                        <small class="text-muted d-block">{{ $notif->desc }}</small>
                                                    </a>
                                                </div>
                                                <span class="notify-time text-muted ml-2" style="font-size: 11px; white-space: nowrap;">
                                                    {{ $notif->time ? $notif->time->locale('vi')->diffForHumans() : '' }}
                                                </span>
                                            </li>
                                        @empty
                                            <li class="dropdown-item text-center text-muted py-4">
                                                <i class="mdi mdi-bell-off-outline d-block mb-1" style="font-size: 24px;"></i>
                                                Không có thông báo mới nào
                                            </li>
                                        @endforelse
                                    </ul>
                                    <div class="d-flex justify-content-between p-2 bg-light">
                                        <a class="btn btn-xs btn-outline-primary" href="{{ route('admin.quotes.index') }}">Báo giá</a>
                                        <a class="btn btn-xs btn-outline-success" href="{{ route('admin.contacts.index') }}">Liên hệ</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link d-flex align-items-center" href="#" role="button" data-toggle="dropdown">
                                    <div class="header-profile-avatar mr-2 d-none d-sm-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: #003b70; color: #fff; border-radius: 50%; font-weight: 700; font-size: 13px;">
                                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <span class="d-none d-md-inline-block font-weight-bold text-dark mr-1" style="font-size: 13px;">
                                        {{ Auth::user()->name ?? 'Administrator' }}
                                    </span>
                                    <i class="mdi mdi-chevron-down text-muted"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" style="min-width: 250px; padding: 0; overflow: hidden; border-radius: 8px;">
                                    <!-- User Info Header -->
                                    <div class="p-3 bg-light border-bottom">
                                        <div class="font-weight-bold text-dark" style="font-size: 14px;">{{ Auth::user()->name ?? 'Quản Trị Viên' }}</div>
                                        <small class="text-muted d-block">{{ Auth::user()->email ?? 'admin@misutech.vn' }}</small>
                                        <span class="badge badge-success mt-1" style="font-size: 10px; font-weight: 600;">● Online • Quản trị viên</span>
                                    </div>

                                    <!-- Actions Menu -->
                                    <div class="py-1">
                                        <a href="{{ route('home.index') }}" target="_blank" class="dropdown-item py-2 d-flex align-items-center">
                                            <i class="mdi mdi-open-in-new text-primary mr-2" style="font-size: 16px;"></i>
                                            <span>Xem Website (Trang chủ)</span>
                                        </a>
                                        <a href="{{ route('admin.settings.index') }}" class="dropdown-item py-2 d-flex align-items-center">
                                            <i class="mdi mdi-settings text-info mr-2" style="font-size: 16px;"></i>
                                            <span>Cài đặt hệ thống</span>
                                        </a>
                                        <a href="javascript:void(0)" onclick="adminClearCache()" class="dropdown-item py-2 d-flex align-items-center">
                                            <i class="mdi mdi-broom text-warning mr-2" style="font-size: 16px;"></i>
                                            <span>Làm mới Cache hệ thống</span>
                                        </a>
                                        <a href="javascript:void(0)" onclick="openChangePasswordModal()" class="dropdown-item py-2 d-flex align-items-center">
                                            <i class="mdi mdi-key-variant text-secondary mr-2" style="font-size: 16px;"></i>
                                            <span>Đổi mật khẩu</span>
                                        </a>
                                    </div>

                                    <!-- Logout -->
                                    <div class="border-top p-1 bg-light">
                                        <a href="javascript:void(0)" onclick="adminLogout()" class="dropdown-item text-danger py-2 d-flex align-items-center font-weight-bold">
                                            <i class="mdi mdi-logout text-danger mr-2" style="font-size: 16px;"></i>
                                            <span>Đăng xuất tài khoản</span>
                                        </a>
                                        <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        <div class="quixnav">
            <div class="quixnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Tổng Quan</li>
                    <li><a href="{{ route('admin.dashboard') }}" aria-expanded="false"><i class="mdi mdi-view-dashboard"></i><span class="nav-text">Dashboard</span></a></li>

                    <li class="nav-label">Kinh Doanh & Khách Hàng</li>
                    <li><a href="{{ route('admin.quotes.index') }}" aria-expanded="false"><i class="mdi mdi-file-document-box"></i><span class="nav-text">Quản lý Báo Giá</span></a></li>
                    <li><a href="{{ route('admin.contacts.index') }}" aria-expanded="false"><i class="mdi mdi-email-open"></i><span class="nav-text">Khách Hàng Liên Hệ</span></a></li>
                    <li><a href="{{ route('admin.support_contacts.index') }}" aria-expanded="false"><i class="mdi mdi-phone-classic"></i><span class="nav-text">Hotline & Tư Vấn</span></a></li>

                    <li class="nav-label">Sản Phẩm & Danh Mục</li>
                    <li><a href="{{ route('admin.categories.index') }}" aria-expanded="false"><i class="mdi mdi-format-list-bulleted"></i><span class="nav-text">Quản lý Danh mục</span></a></li>
                    <li><a href="{{ route('admin.brands.index') }}" aria-expanded="false"><i class="mdi mdi-tag-text-outline"></i><span class="nav-text">Quản lý Thương hiệu</span></a></li>
                    <li><a href="{{ route('admin.series.index') }}" aria-expanded="false"><i class="mdi mdi-layers"></i><span class="nav-text">Dòng sản phẩm</span></a></li>
                    <li><a href="{{ route('admin.products.index') }}" aria-expanded="false"><i class="mdi mdi-store"></i><span class="nav-text">Quản lý Sản phẩm</span></a></li>

                    <li class="nav-label">Nội Dung & Truyền Thông</li>
                    <li><a href="{{ route('admin.posts.index') }}" aria-expanded="false"><i class="mdi mdi-newspaper"></i><span class="nav-text">Quản lý Bài viết</span></a></li>
                    <li><a href="{{ route('admin.banners.index') }}" aria-expanded="false"><i class="mdi mdi-image-multiple"></i><span class="nav-text">Quản lý Banner</span></a></li>
                    <li><a href="{{ route('admin.media.index') }}" aria-expanded="false"><i class="mdi mdi-folder-multiple-image"></i><span class="nav-text">Quản lý Media</span></a></li>

                    <li class="nav-label">Hệ Thống</li>
                    <li><a href="{{ route('admin.settings.index') }}" aria-expanded="false"><i class="mdi mdi-settings"></i><span class="nav-text">Cài đặt hệ thống</span></a></li>
                    <li><a href="{{ route('admin.sitemaps.index') }}" aria-expanded="false"><i class="mdi mdi-sitemap"></i><span class="nav-text">Cấu hình Sitemap</span></a></li>
                </ul>
            </div>
        </div>
        <!--**********************************
            Sidebar end
        ***********************************-->

        <!-- Content body start -->
        <div class="content-body">
            @yield('content')
        </div>
        <!-- Content body end -->

        <!-- Modal Đổi Mật Khẩu -->
        <div class="modal fade" id="adminChangePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white">Đổi Mật Khẩu Quản Trị</h5>
                        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <form id="adminChangePasswordForm" onsubmit="submitChangePassword(event)">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="cpCurrentPassword" name="current_password" required>
                            </div>
                            <div class="form-group">
                                <label>Mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="cpNewPassword" name="new_password" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Xác nhận mật khẩu mới <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="cpConfirmPassword" name="new_password_confirmation" required minlength="6">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitChangePw">Lưu Mật Khẩu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>Bản quyền © {{ date('Y') }} <a href="{{ route('home.index') }}" target="_blank">MISUTECH</a> - Hệ Thống Quản Trị Tự Động Hóa</p>
            </div>
        </div>
        <!--**********************************
            Footer end
        ***********************************-->
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!-- Required vendors -->
    <script src="{{ asset('admins/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admins/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admins/js/custom.min.js') }}"></script>
    <script src="{{ asset('admins/vendor/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('admins/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/2.8.2/slimselect.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/{{ env('APP_KEY_TINYMCE', 'no-api-key') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <!-- Global Admin Utility Scripts -->
    <script>
        $(document).ready(function() {
            $('#preloader').fadeOut(300);
            $('#main-wrapper').addClass('show');
        });

        // Thiết lập cấu hình AJAX mặc định cho toàn bộ Admin (CSRF + Headers)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        // Chặn popup cảnh báo khó chịu của DataTables trên toàn bộ hệ thống
        if ($.fn && $.fn.dataTable) {
            $.fn.dataTable.ext.errMode = 'none';
        }
        $(document).on('error.dt', function(e, settings, techNote, message) {
            console.warn('DataTables warning (handled):', message);
        });

        function adminClearCache() {
            if (typeof toastr !== 'undefined') {
                toastr.info('Đang làm mới cache hệ thống...');
            }
            $.ajax({
                url: "{{ route('admin.clear_cache') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Làm mới cache thành công!');
                    } else {
                        alert(response.message || 'Làm mới cache thành công!');
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('Có lỗi xảy ra khi xóa cache');
                    } else {
                        alert('Có lỗi xảy ra khi xóa cache');
                    }
                }
            });
        }

        function openChangePasswordModal() {
            $('#adminChangePasswordForm')[0].reset();
            $('#adminChangePasswordModal').modal('show');
        }

        function submitChangePassword(e) {
            e.preventDefault();
            const btn = $('#btnSubmitChangePw');
            btn.prop('disabled', true).text('Đang cập nhật...');

            $.ajax({
                url: "{{ route('admin.change_password') }}",
                type: 'POST',
                data: $('#adminChangePasswordForm').serialize(),
                success: function(response) {
                    btn.prop('disabled', false).text('Lưu Mật Khẩu');
                    $('#adminChangePasswordModal').modal('hide');
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Đổi mật khẩu thành công!');
                    } else {
                        alert(response.message || 'Đổi mật khẩu thành công!');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Lưu Mật Khẩu');
                    let msg = 'Lỗi cập nhật mật khẩu';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(msg);
                    } else {
                        alert(msg);
                    }
                }
            });
        }

        function adminLogout() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Đăng xuất?',
                    text: "Bạn có chắc chắn muốn đăng xuất khỏi phiên làm việc?",
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#003b70',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Đăng xuất',
                    cancelButtonText: 'Ở lại'
                }).then((result) => {
                    if (result.value) {
                        document.getElementById('admin-logout-form').submit();
                    }
                });
            } else {
                if (confirm('Bạn có chắc chắn muốn đăng xuất?')) {
                    document.getElementById('admin-logout-form').submit();
                }
            }
        }

        // Global Live Search Logic
        $(document).ready(function() {
            const searchInput = $('#adminGlobalSearchInput');
            const searchResults = $('#adminGlobalSearchResults');
            let searchTimeout = null;

            // Shortcut Ctrl + K or Cmd + K
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus().select();
                } else if (e.key === 'Escape') {
                    searchResults.hide();
                }
            });

            searchInput.on('input', function() {
                const query = $(this).val().trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    searchResults.hide().empty();
                    return;
                }

                searchResults.show().html('<div class="p-3 text-center text-muted"><i class="mdi mdi-loading mdi-spin mr-1"></i> Đang tìm kiếm...</div>');

                searchTimeout = setTimeout(function() {
                    $.ajax({
                        url: "{{ route('admin.global_search') }}",
                        type: 'GET',
                        data: { q: query },
                        success: function(response) {
                            if (!response.results || response.results.length === 0) {
                                searchResults.html('<div class="p-3 text-center text-muted"><i class="mdi mdi-information-outline mr-1"></i> Không tìm thấy kết quả cho "<b>' + $('<div>').text(query).html() + '</b>"</div>');
                                return;
                            }

                            let html = '<div class="p-2 bg-light border-bottom d-flex justify-content-between align-items-center">' +
                                       '<small class="text-muted font-weight-bold text-uppercase">Kết quả tìm kiếm (' + response.results.length + ')</small>' +
                                       '<small class="text-muted">Nhấn ESC để đóng</small></div>' +
                                       '<div class="list-group list-group-flush">';

                            let currentGroup = '';
                            response.results.forEach(function(item) {
                                if (item.group !== currentGroup) {
                                    currentGroup = item.group;
                                    html += '<div class="px-3 py-1 bg-light font-weight-bold text-muted" style="font-size: 11px;">' + currentGroup + '</div>';
                                }

                                html += '<a href="' + item.url + '" class="list-group-item list-group-item-action d-flex align-items-center py-2 px-3 border-0">' +
                                        '<span class="mr-2 text-primary" style="font-size: 18px;"><i class="' + (item.icon || 'mdi mdi-link') + '"></i></span>' +
                                        '<div class="flex-grow-1" style="line-height: 1.3;">' +
                                            '<div class="font-weight-bold text-dark" style="font-size: 13px;">' + $('<div>').text(item.title).html() + '</div>' +
                                            '<small class="text-muted d-block">' + $('<div>').text(item.desc || '').html() + '</small>' +
                                        '</div>' +
                                        (item.badge ? '<span class="badge ' + (item.badge_class || 'badge-secondary') + ' ml-2" style="font-size: 10px;">' + item.badge + '</span>' : '') +
                                        '</a>';
                            });

                            html += '</div>';
                            searchResults.html(html);
                        },
                        error: function() {
                            searchResults.html('<div class="p-3 text-center text-danger">Có lỗi xảy ra khi tìm kiếm</div>');
                        }
                    });
                }, 200);
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.admin_global_search_wrap').length) {
                    searchResults.hide();
                }
            });

            // Focus open dropdown if query exists
            searchInput.on('focus', function() {
                if ($(this).val().trim().length >= 2 && searchResults.children().length > 0) {
                    searchResults.show();
                }
            });
        });
    </script>

    <!-- Page-specific vendor scripts -->
    @stack('vendor_scripts')
    @yield('scripts')
    @stack('scripts')
</body>
</html>
