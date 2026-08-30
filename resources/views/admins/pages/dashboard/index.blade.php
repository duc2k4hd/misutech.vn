@extends('admins.layouts.master')

@section('title', 'Dashboard – Misutech Admin')

@push('styles')
<style>
/* ── Dashboard Custom Styles ─────────────────────────────── */
:root {
    --dash-primary: #4f46e5;
    --dash-success: #10b981;
    --dash-warning: #f59e0b;
    --dash-danger:  #ef4444;
    --dash-info:    #06b6d4;
    --dash-purple:  #8b5cf6;
    --dash-pink:    #ec4899;
    --dash-teal:    #14b8a6;
}

/* KPI Cards */
.kpi-card {
    border: none;
    border-radius: 14px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    box-shadow: 0 2px 12px rgba(0,0,0,.08);
    height: 100%;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.13); }
.kpi-card .card-body { padding: 18px 20px; }

.kpi-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.kpi-value { font-size: 26px; font-weight: 700; line-height: 1.1; }
.kpi-label { font-size: 11px; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; }
.kpi-sub   { font-size: 12px; color: #9ca3af; margin-top: 6px; }

.kpi-mini-wrap { display: flex; gap: 10px; margin-top: 12px; }
.kpi-mini      { flex: 1; background: #f9fafb; border-radius: 10px; padding: 8px 12px; min-width: 0; }
.kpi-mini .kv  { font-size: 17px; font-weight: 700; }
.kpi-mini .kl  { font-size: 11px; color: #9ca3af; margin-top: 1px; }

.kpi-growth      { font-size: 12px; font-weight: 600; }
.kpi-growth.up   { color: var(--dash-success); }
.kpi-growth.down { color: var(--dash-danger); }

/* Section card */
.dash-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(0,0,0,.07);
}
.dash-card .card-header {
    background: transparent;
    border-bottom: 1px solid #f3f4f6;
    padding: 14px 18px 12px;
    display: flex; align-items: center; justify-content: space-between;
}
.dash-card .card-header h5 {
    font-size: 14px; font-weight: 700; margin: 0; color: #111827;
}
.badge-count {
    font-size: 11px; background: #f3f4f6; color: #6b7280;
    border-radius: 20px; padding: 2px 10px; font-weight: 600;
}

/* Status badges */
.status-badge {
    display: inline-block; padding: 2px 10px;
    border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap;
}
.status-badge.new        { background: #dbeafe; color: #1d4ed8; }
.status-badge.processing { background: #fef3c7; color: #b45309; }
.status-badge.completed  { background: #d1fae5; color: #065f46; }
.status-badge.cancelled  { background: #fee2e2; color: #b91c1c; }
.status-badge.draft      { background: #f3f4f6; color: #6b7280; }
.status-badge.published  { background: #d1fae5; color: #065f46; }
.status-badge.pending    { background: #fef3c7; color: #b45309; }
.status-badge.approved   { background: #d1fae5; color: #065f46; }
.status-badge.rejected   { background: #fee2e2; color: #b91c1c; }
.status-badge.read       { background: #f3f4f6; color: #6b7280; }
.status-badge.active     { background: #d1fae5; color: #065f46; }

/* Stars */
.stars { color: #f59e0b; }

/* Progress bar slim */
.progress-sm { height: 6px; border-radius: 3px; }

/* Top products rank number */
.rank-num {
    width: 26px; height: 26px; border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700; flex-shrink: 0;
}
.rank-1 { background: #fef3c7; color: #b45309; }
.rank-2 { background: #f3f4f6; color: #374151; }
.rank-3 { background: #fde8d8; color: #c2410c; }
.rank-n { background: #f3f4f6; color: #9ca3af; }

/* Mini stat card in right column */
.mini-info-card { border: none; border-radius: 12px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
.mini-info-card .card-body { padding: 14px 16px; }
.mini-info-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}

#quoteChart { max-height: 260px; }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">

    {{-- ── ROW 1: KPI Cards ─────────────────────────────── --}}
    <div class="row mb-3">

        {{-- Báo giá --}}
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="kpi-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="kpi-icon mr-3" style="background:#ede9fe;">
                            <i class="mdi mdi-file-document-box" style="color:var(--dash-primary);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Tổng báo giá</div>
                            <div class="kpi-value">{{ number_format($totalQuotes) }}</div>
                        </div>
                    </div>
                    <div class="kpi-mini-wrap">
                        <div class="kpi-mini">
                            <div class="kv text-warning">{{ number_format($totalQuotesPending) }}</div>
                            <div class="kl">Chờ xử lý</div>
                        </div>
                        <div class="kpi-mini">
                            <div class="kv text-primary">{{ number_format($quotesThisMonth) }}</div>
                            <div class="kl">Tháng này</div>
                        </div>
                    </div>
                    <div class="kpi-sub">
                        <span class="kpi-growth {{ $quoteGrowth >= 0 ? 'up' : 'down' }}">
                            <i class="mdi mdi-{{ $quoteGrowth >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                            {{ abs($quoteGrowth) }}%
                        </span>
                        <span class="text-muted ml-1">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Giá trị báo giá --}}
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="kpi-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="kpi-icon mr-3" style="background:#d1fae5;">
                            <i class="mdi mdi-currency-usd" style="color:var(--dash-success);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Giá trị báo giá</div>
                            <div class="kpi-value" style="font-size:20px;">
                                @php
                                    $rev = $totalQuoteRevenue;
                                    echo $rev >= 1_000_000_000
                                        ? number_format($rev/1_000_000_000, 1) . ' tỷ'
                                        : ($rev >= 1_000_000 ? number_format($rev/1_000_000, 1) . ' tr' : number_format($rev, 0, ',', '.'));
                                @endphp
                            </div>
                        </div>
                    </div>
                    <div class="kpi-mini-wrap">
                        <div class="kpi-mini" style="flex:none; width:100%;">
                            <div class="kv" style="font-size:13px; color:var(--dash-success);">{{ number_format($totalRevenue, 0, ',', '.') }} đ</div>
                            <div class="kl">Doanh thu đơn hàng</div>
                        </div>
                    </div>
                    <div class="kpi-sub">
                        <i class="mdi mdi-cart-outline"></i> {{ number_format($totalOrders) }} đơn hàng tổng
                    </div>
                </div>
            </div>
        </div>

        {{-- Liên hệ --}}
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="kpi-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="kpi-icon mr-3" style="background:#fef3c7;">
                            <i class="mdi mdi-email-outline" style="color:var(--dash-warning);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Liên hệ khách hàng</div>
                            <div class="kpi-value">{{ number_format($totalContacts) }}</div>
                        </div>
                    </div>
                    <div class="kpi-mini-wrap">
                        <div class="kpi-mini">
                            <div class="kv text-danger">{{ number_format($totalContactsNew) }}</div>
                            <div class="kl">Chưa đọc</div>
                        </div>
                        <div class="kpi-mini">
                            <div class="kv text-primary">{{ number_format($contactsThisMonth) }}</div>
                            <div class="kl">Tháng này</div>
                        </div>
                    </div>
                    <div class="kpi-sub">
                        <span class="kpi-growth {{ $contactGrowth >= 0 ? 'up' : 'down' }}">
                            <i class="mdi mdi-{{ $contactGrowth >= 0 ? 'trending-up' : 'trending-down' }}"></i>
                            {{ abs($contactGrowth) }}%
                        </span>
                        <span class="text-muted ml-1">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sản phẩm --}}
        <div class="col-xl-3 col-lg-6 col-sm-6 mb-3">
            <div class="kpi-card card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="kpi-icon mr-3" style="background:#cffafe;">
                            <i class="mdi mdi-cube-outline" style="color:var(--dash-info);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Sản phẩm</div>
                            <div class="kpi-value">{{ number_format($totalProducts) }}</div>
                        </div>
                    </div>
                    <div class="kpi-mini-wrap">
                        <div class="kpi-mini">
                            <div class="kv" style="color:var(--dash-success);">{{ number_format($totalProductsActive) }}</div>
                            <div class="kl">Active</div>
                        </div>
                        <div class="kpi-mini">
                            <div class="kv" style="color:var(--dash-purple);">{{ number_format($totalBrands) }}</div>
                            <div class="kl">Thương hiệu</div>
                        </div>
                    </div>
                    <div class="kpi-sub">
                        <i class="mdi mdi-tag-multiple-outline"></i> {{ number_format($totalCategories) }} danh mục
                        &bull; <i class="mdi mdi-layers-outline"></i> {{ number_format($totalSeries) }} dòng SP
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 2: Chart + Mini Stats ─────────────────────── --}}
    <div class="row mb-3">

        {{-- Chart 12 tháng --}}
        <div class="col-xl-8 mb-3">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <h5><i class="mdi mdi-chart-bar text-primary mr-1"></i> Báo giá 12 tháng gần nhất</h5>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2">Xem tất cả</a>
                </div>
                <div class="card-body">
                    <canvas id="quoteChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Mini stats column --}}
        <div class="col-xl-4 mb-3">
            {{-- Bài viết --}}
            <div class="card mini-info-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="mini-info-icon mr-3" style="background:#ede9fe;">
                            <i class="mdi mdi-newspaper" style="color:var(--dash-purple);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Bài viết</div>
                            <div style="font-size:22px; font-weight:700; line-height:1.1;">{{ number_format($totalPosts) }}</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between" style="font-size:13px; margin-bottom:6px;">
                        <span><strong class="text-success">{{ $totalPostsPublished }}</strong> Đã đăng</span>
                        <span><strong class="text-muted">{{ $totalPosts - $totalPostsPublished }}</strong> Nháp</span>
                    </div>
                    <div class="progress progress-sm">
                        @php $pct = $totalPosts > 0 ? round($totalPostsPublished/$totalPosts*100) : 0; @endphp
                        <div class="progress-bar bg-success" style="width:{{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Đánh giá --}}
            <div class="card mini-info-card mb-3">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="mini-info-icon mr-3" style="background:#fce7f3;">
                            <i class="mdi mdi-star" style="color:var(--dash-pink);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Đánh giá sản phẩm</div>
                            <div style="font-size:22px; font-weight:700; line-height:1.1;">{{ number_format($totalReviews) }}</div>
                        </div>
                    </div>
                    <div style="font-size:13px;">
                        <span class="stars mr-2">★ {{ $avgRating }}</span>
                        <span class="text-warning font-weight-bold">{{ $totalReviewsNew }}</span>
                        <span class="text-muted"> chờ duyệt</span>
                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="card mini-info-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="mini-info-icon mr-3" style="background:#ccfbf1;">
                            <i class="mdi mdi-folder-multiple-image" style="color:var(--dash-teal);"></i>
                        </div>
                        <div>
                            <div class="kpi-label">Thư viện Media</div>
                            <div style="font-size:22px; font-weight:700; line-height:1.1;">{{ number_format($totalMediaFiles) }} tệp</div>
                            <div style="font-size:12px; color:#9ca3af;">{{ $mediaSizeHuman }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 3: Top Products + Donut Charts ─────────────── --}}
    <div class="row mb-3">

        {{-- Top sản phẩm báo giá --}}
        <div class="col-xl-8 mb-3">
            <div class="card dash-card">
                <div class="card-header">
                    <h5><i class="mdi mdi-trophy-outline text-warning mr-1"></i> Top sản phẩm được báo giá nhiều nhất</h5>
                    <span class="badge-count">{{ $topQuotedProducts->count() }} SP</span>
                </div>
                <div class="card-body p-0">
                    @if($topQuotedProducts->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead style="background:#f9fafb;">
                                <tr>
                                    <th style="width:36px;">#</th>
                                    <th>Sản phẩm</th>
                                    <th>Thương hiệu</th>
                                    <th class="text-center">Lần BG</th>
                                    <th class="text-right">Tổng SL</th>
                                    <th class="text-right">Giá trị</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topQuotedProducts as $i => $item)
                                <tr>
                                    <td class="align-middle">
                                        <span class="rank-num {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-n')) }}">{{ $i+1 }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold text-truncate" style="max-width:200px;" title="{{ $item->product_name }}">
                                            {{ $item->product_name }}
                                        </div>
                                        @if($item->product_sku)<small class="text-muted">{{ $item->product_sku }}</small>@endif
                                    </td>
                                    <td class="align-middle">{{ $item->brand_name ?: '—' }}</td>
                                    <td class="text-center align-middle">
                                        <span class="badge badge-primary">{{ $item->quote_count }}</span>
                                    </td>
                                    <td class="text-right align-middle font-weight-bold">{{ number_format($item->total_qty) }}</td>
                                    <td class="text-right align-middle text-success font-weight-bold">
                                        @php $v=$item->total_value; echo $v>=1_000_000?number_format($v/1_000_000,1).'tr':number_format($v,0,',','.'); @endphp
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có dữ liệu báo giá</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Donut charts --}}
        <div class="col-xl-4 mb-3">
            {{-- Quote status --}}
            <div class="card dash-card mb-3">
                <div class="card-header">
                    <h5><i class="mdi mdi-chart-donut text-primary mr-1"></i> Trạng thái báo giá</h5>
                </div>
                <div class="card-body">
                    <canvas id="quoteStatusChart" style="max-height:180px;"></canvas>
                    <div class="mt-3">
                        @php
                            $statusLabels = ['new'=>['Mới','#4f46e5'],'processing'=>['Đang xử lý','#f59e0b'],'completed'=>['Hoàn thành','#10b981'],'cancelled'=>['Huỷ','#ef4444']];
                            $totalQS = array_sum($quoteByStatus);
                        @endphp
                        @foreach($statusLabels as $key => [$label, $color])
                        @php $cnt = $quoteByStatus[$key] ?? 0; $pct = $totalQS > 0 ? round($cnt/$totalQS*100) : 0; @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                            <span>
                                <span class="d-inline-block rounded mr-1" style="width:10px;height:10px;background:{{ $color }};"></span>
                                {{ $label }}
                            </span>
                            <span><strong>{{ $cnt }}</strong> <span class="text-muted">({{ $pct }}%)</span></span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Contact device --}}
            <div class="card dash-card">
                <div class="card-header">
                    <h5><i class="mdi mdi-devices text-info mr-1"></i> Thiết bị liên hệ</h5>
                </div>
                <div class="card-body">
                    @php
                        $deviceIcons  = ['mobile'=>'cellphone','desktop'=>'monitor','tablet'=>'tablet'];
                        $deviceColors = ['mobile'=>'#10b981','desktop'=>'#4f46e5','tablet'=>'#f59e0b'];
                        $totalCD      = array_sum($contactByDevice) ?: 1;
                    @endphp
                    @forelse($contactByDevice as $device => $count)
                    @php $dpct = round($count/$totalCD*100); @endphp
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1" style="font-size:12px;">
                            <span><i class="mdi mdi-{{ $deviceIcons[$device] ?? 'help-circle-outline' }} mr-1"></i>{{ ucfirst($device ?: 'Không rõ') }}</span>
                            <span class="font-weight-bold">{{ $count }} ({{ $dpct }}%)</span>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar" style="width:{{ $dpct }}%; background:{{ $deviceColors[$device] ?? '#9ca3af' }};"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-3 mb-0" style="font-size:13px;">Chưa có dữ liệu</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 4: Recent Quotes ─────────────────────────── --}}
    <div class="row mb-3">
        <div class="col-12">
            <div class="card dash-card">
                <div class="card-header">
                    <h5><i class="mdi mdi-file-document-box-outline text-primary mr-1"></i> Báo giá gần nhất</h5>
                    <a href="{{ route('admin.quotes.index') }}" class="btn btn-sm btn-outline-primary py-0 px-2">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentQuotes->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead style="background:#f9fafb;">
                                <tr>
                                    <th>Mã BG</th>
                                    <th>Khách hàng</th>
                                    <th>Điện thoại</th>
                                    <th>Công ty</th>
                                    <th class="text-center">Số SP</th>
                                    <th class="text-right">Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentQuotes as $q)
                                <tr>
                                    <td class="font-weight-bold text-primary">#{{ $q->quote_code }}</td>
                                    <td>{{ $q->customer_name ?: '—' }}</td>
                                    <td>{{ $q->customer_phone ?: '—' }}</td>
                                    <td class="text-truncate" style="max-width:150px;">{{ $q->customer_company ?: '—' }}</td>
                                    <td class="text-center"><span class="badge badge-secondary">{{ $q->items_count ?: 0 }}</span></td>
                                    <td class="text-right font-weight-bold text-success">{{ number_format($q->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="status-badge {{ $q->status }}">
                                            {{ ['new'=>'Mới','processing'=>'Đang xử lý','completed'=>'Hoàn thành','cancelled'=>'Huỷ'][$q->status] ?? $q->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $q->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có báo giá nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 5: Recent Contacts + Recent Posts ────────── --}}
    <div class="row mb-3">

        {{-- Contacts --}}
        <div class="col-xl-7 mb-3">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <h5><i class="mdi mdi-email-outline text-warning mr-1"></i> Liên hệ gần nhất</h5>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-sm btn-outline-warning py-0 px-2">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentContacts->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:13px;">
                            <thead style="background:#f9fafb;">
                                <tr>
                                    <th>Tên khách</th>
                                    <th>Điện thoại</th>
                                    <th>Chủ đề</th>
                                    <th>Trạng thái</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentContacts as $c)
                                <tr>
                                    <td class="font-weight-bold">{{ $c->name }}</td>
                                    <td>{{ $c->phone ?: $c->email }}</td>
                                    <td class="text-truncate" style="max-width:180px;">{{ $c->subject ?: '—' }}</td>
                                    <td>
                                        <span class="status-badge {{ $c->status }}">
                                            {{ ['new'=>'Mới','read'=>'Đã đọc','replied'=>'Đã trả lời'][$c->status] ?? $c->status }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $c->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có liên hệ nào</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Posts --}}
        <div class="col-xl-5 mb-3">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <h5><i class="mdi mdi-newspaper mr-1" style="color:var(--dash-purple);"></i> Bài viết gần nhất</h5>
                    <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-outline-secondary py-0 px-2">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentPosts->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($recentPosts as $post)
                        <li class="list-group-item px-3 py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex:1; min-width:0; padding-right:8px;">
                                    <div class="font-weight-bold text-truncate" style="font-size:13px;" title="{{ $post->title }}">{{ $post->title }}</div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        <i class="mdi mdi-eye-outline"></i> {{ number_format($post->views_count ?? 0) }}
                                        &nbsp;&bull;&nbsp; {{ $post->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <span class="status-badge {{ $post->status }}" style="flex-shrink:0;">
                                    {{ $post->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có bài viết</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW 6: Recent Products + Recent Reviews ──────── --}}
    <div class="row mb-3">

        {{-- Products --}}
        <div class="col-xl-6 mb-3">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <h5><i class="mdi mdi-cube-outline text-info mr-1"></i> Sản phẩm mới thêm</h5>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-info py-0 px-2">Xem tất cả</a>
                </div>
                <div class="card-body p-0">
                    @if($recentProducts->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($recentProducts as $prod)
                        <li class="list-group-item px-3 py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex:1; min-width:0; padding-right:8px;">
                                    <div class="font-weight-bold text-truncate" style="font-size:13px;" title="{{ $prod->name }}">{{ $prod->name }}</div>
                                    <div class="text-muted mt-1" style="font-size:11px;">
                                        @if($prod->sku) SKU: {{ $prod->sku }} &bull; @endif
                                        @if($prod->brand) {{ $prod->brand->name }} &bull; @endif
                                        {{ $prod->created_at->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="text-right" style="flex-shrink:0;">
                                    <div class="font-weight-bold text-success" style="font-size:13px;">
                                        {{ $prod->price ? number_format($prod->price, 0, ',', '.') . 'đ' : 'Liên hệ' }}
                                    </div>
                                    <span class="status-badge {{ $prod->status }}">
                                        {{ $prod->status === 'active' ? 'Active' : 'Ẩn' }}
                                    </span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có sản phẩm</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="col-xl-6 mb-3">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <h5><i class="mdi mdi-star-outline text-warning mr-1"></i> Đánh giá gần nhất</h5>
                    <span class="badge-count">{{ $totalReviewsNew }} chờ duyệt</span>
                </div>
                <div class="card-body p-0">
                    @if($recentReviews->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($recentReviews as $review)
                        <li class="list-group-item px-3 py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div style="flex:1; min-width:0; padding-right:8px;">
                                    <div class="d-flex align-items-center mb-1">
                                        <strong style="font-size:13px; margin-right:8px;">{{ $review->author_name }}</strong>
                                        <span class="stars" style="font-size:12px;">
                                            @for($s=1;$s<=5;$s++){{ $s<=$review->rating?'★':'☆' }}@endfor
                                        </span>
                                    </div>
                                    @if($review->product)
                                    <div class="text-muted text-truncate" style="font-size:11px;">
                                        <i class="mdi mdi-cube-outline"></i> {{ $review->product->name }}
                                    </div>
                                    @endif
                                    @if($review->comment)
                                    <div class="text-truncate text-muted" style="font-size:12px;" title="{{ $review->comment }}">{{ $review->comment }}</div>
                                    @endif
                                </div>
                                <span class="status-badge {{ $review->status }}" style="flex-shrink:0;">
                                    {{ ['pending'=>'Chờ duyệt','approved'=>'Đã duyệt','rejected'=>'Từ chối'][$review->status] ?? $review->status }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="mdi mdi-inbox" style="font-size:40px;"></i>
                        <p class="mt-2 mb-0">Chưa có đánh giá</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('vendor_scripts')
<script src="{{ asset('admins/vendor/chart.js/Chart.bundle.min.js') }}"></script>
@endpush

@push('scripts')
<script>
(function () {
    // ── 1. Biểu đồ báo giá 12 tháng ─────────────────────────
    var chartMonths  = @json($chartMonths);
    var chartQuotes  = @json($chartQuotes);
    var chartRevenue = @json($chartRevenue);

    var ctx = document.getElementById('quoteChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: chartMonths,
                datasets: [
                    {
                        label: 'Số báo giá',
                        data: chartQuotes,
                        backgroundColor: 'rgba(79,70,229,0.75)',
                        borderRadius: 5,
                        yAxisID: 'y',
                        order: 2
                    },
                    {
                        label: 'Giá trị (triệu đ)',
                        data: chartRevenue.map(function(v){ return +(v/1000000).toFixed(1); }),
                        type: 'line',
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.08)',
                        borderWidth: 2,
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y2',
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { fontSize: 12 } },
                    tooltip: {
                        callbacks: {
                            label: function(item) {
                                if (item.datasetIndex === 0) return ' ' + item.yLabel + ' báo giá';
                                return ' ' + Number(item.yLabel).toLocaleString('vi-VN') + ' triệu đ';
                            }
                        }
                    }
                },
                scales: {
                    yAxes: [
                        { id:'y',  position:'left',  ticks:{ beginAtZero:true }, gridLines:{ color:'#f3f4f6' } },
                        { id:'y2', position:'right', ticks:{ beginAtZero:true }, gridLines:{ drawOnChartArea:false } }
                    ]
                }
            }
        });
    }

    // ── 2. Donut – Trạng thái báo giá ────────────────────────
    var statusData = @json(array_values($quoteByStatus));
    var statusKeys = @json(array_keys($quoteByStatus));
    var colorMap   = { new:'#4f46e5', processing:'#f59e0b', completed:'#10b981', cancelled:'#ef4444' };
    var statusColors = statusKeys.map(function(k){ return colorMap[k] || '#9ca3af'; });
    var labelMap   = { new:'Mới', processing:'Đang xử lý', completed:'Hoàn thành', cancelled:'Huỷ' };

    var ctx2 = document.getElementById('quoteStatusChart');
    if (ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: statusKeys.map(function(k){ return labelMap[k] || k; }),
                datasets: [{ data: statusData, backgroundColor: statusColors, borderWidth: 2, borderColor: '#fff' }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                legend: { display: false },
                cutoutPercentage: 65,
                tooltips: {
                    callbacks: { label: function(item, data){ return ' ' + data.datasets[0].data[item.index] + ' báo giá'; } }
                }
            }
        });
    }
})();
</script>
@endpush
