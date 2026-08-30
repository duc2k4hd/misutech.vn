@extends('clients.layouts.master')

@section('title', 'Giỏ Hàng - ' . ($settings->company ?? ($settings->name ?? 'Công ty cổ phần Misutech')))
@section('meta_description', 'Giỏ hàng của bạn tại Misutech. Xem lại các sản phẩm và tiến hành thanh toán an toàn, tiện lợi.')

@push('styles')
    <link rel="stylesheet" href="{{ asset('clients/css/cart.css') }}">
@endpush

@section('content')
    <div class="misutech_home_container" style="margin-top: 30px; margin-bottom: 50px;">
        <h1 class="misutech_cart_title">Giỏ Hàng Của Bạn</h1>
        
        <div class="misutech_cart_wrapper">
            <!-- Left: Cart Items -->
            <div class="misutech_cart_items">
                
                <div class="misutech_cart_header">
                    <div class="misutech_cart_header_col misutech_col_product">Sản Phẩm</div>
                    <div class="misutech_cart_header_col misutech_col_price">Đơn Giá</div>
                    <div class="misutech_cart_header_col misutech_col_qty">Số Lượng</div>
                    <div class="misutech_cart_header_col misutech_col_total">Thành Tiền</div>
                    <div class="misutech_cart_header_col misutech_col_action"></div>
                </div>

                @php $totalPrice = 0; @endphp
                @forelse($cartItems as $item)
                    @php 
                        $itemTotal = $item['price'] * $item['quantity']; 
                        $totalPrice += $itemTotal; 
                    @endphp
                    <div class="misutech_cart_item" data-id="{{ $item['id'] }}" data-price="{{ $item['price'] }}">
                        <div class="misutech_cart_item_info">
                            <img src="{{ $item['thumbnail_url'] ?? asset('clients/imgs/no-image.png') }}" alt="{{ $item['name'] }}" class="misutech_cart_item_img" onerror="this.src='https://placehold.co/100x100?text=No+Image'">
                            <div class="misutech_cart_item_details">
                                <a href="{{ route('product.show', $item['slug'] ?? '#') }}" class="misutech_cart_item_name">{{ $item['name'] }}</a>
                            </div>
                        </div>
                        <div class="misutech_cart_item_price_wrapper">
                            <span class="misutech_cart_current_price">{{ number_format($item['price'], 0, ',', '.') }}đ</span>
                        </div>
                        <div class="misutech_cart_item_qty_wrapper">
                            <div class="misutech_cart_qty_controls">
                                <button class="misutech_qty_btn" aria-label="Giảm số lượng">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                                <input type="number" class="misutech_qty_input" value="{{ $item['quantity'] }}" min="1">
                                <button class="misutech_qty_btn" aria-label="Tăng số lượng">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                </button>
                            </div>
                        </div>
                        <div class="misutech_cart_item_total_price">{{ number_format($itemTotal, 0, ',', '.') }}đ</div>
                        <div class="misutech_cart_item_action_wrapper">
                            <button class="misutech_cart_remove_btn" aria-label="Xóa sản phẩm">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="padding: 40px; text-align: center; color: #666;">
                        <p>Giỏ hàng của bạn đang trống.</p>
                        <a href="{{ route('shop.index') }}" style="display: inline-block; margin-top: 15px; color: var(--misutech_cart_primary); text-decoration: none; font-weight: 500;">Tiếp tục mua sắm</a>
                    </div>
                @endforelse

                <div class="misutech_cart_bottom_actions">
                    <a href="{{ route('shop.index') }}" class="misutech_cart_continue_btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                        Tiếp Tục Mua Sắm
                    </a>
                    <button class="misutech_cart_clear_btn" onclick="if(confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')) { fetch('/api/v1/cart/clear', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').content}}).then(() => location.reload()); }">Xóa Toàn Bộ Giỏ Hàng</button>
                </div>
            </div>

            <!-- Right: Order Summary -->
            <div class="misutech_cart_summary">
                <h3 class="misutech_cart_summary_title">Tổng Quan Đơn Hàng</h3>
                
                <div class="misutech_cart_summary_list">
                    <div class="misutech_cart_summary_row">
                        <span class="misutech_cart_summary_label">Tạm tính</span>
                        <span class="misutech_cart_summary_value">{{ number_format($totalPrice ?? 0, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="misutech_cart_summary_row">
                        <span class="misutech_cart_summary_label">Phí vận chuyển</span>
                        <span class="misutech_cart_summary_value misutech_free_shipping">Miễn phí</span>
                    </div>
                    <div class="misutech_cart_summary_row">
                        <span class="misutech_cart_summary_label">Khuyến mãi</span>
                        <span class="misutech_cart_summary_value misutech_discount_value">-0đ</span>
                    </div>
                </div>

                <div class="misutech_cart_summary_total_row">
                    <span class="misutech_cart_summary_total_label">Tổng Tiền</span>
                    <div class="misutech_cart_summary_total_price_box">
                        <span class="misutech_cart_summary_total_price">{{ number_format($totalPrice ?? 0, 0, ',', '.') }}đ</span>
                        <small>(Đã bao gồm VAT nếu có)</small>
                    </div>
                </div>

                <div class="misutech_cart_checkout_wrapper">
                    <a href="javascript:void(0)" onclick="alert('Tính năng thanh toán đang được phát triển, vui lòng quay lại sau!')" class="misutech_cart_checkout_btn">Tiến Hành Thanh Toán</a>
                    <div class="misutech_cart_trust_info">
                        <div class="misutech_cart_trust_item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            Thanh toán an toàn 100%
                        </div>
                        <div class="misutech_cart_trust_item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                            Giao hàng nhanh toàn quốc
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let updateTimeout;
        
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
        }
        
        document.querySelectorAll('.misutech_cart_item').forEach(item => {
            const input = item.querySelector('.misutech_qty_input');
            const btns = item.querySelectorAll('.misutech_qty_btn');
            const productId = item.dataset.id;
            const price = parseFloat(item.dataset.price);
            const totalEl = item.querySelector('.misutech_cart_item_total_price');
            
            function scheduleUpdate(newQty) {
                if (isNaN(newQty) || newQty < 0) newQty = 0;
                if (newQty > 1000) newQty = 1000;
                
                input.value = newQty;
                
                // Optimistic UI update
                const newTotal = price * newQty;
                totalEl.textContent = formatMoney(newTotal);
                
                // Recalculate summary total
                let newSummaryTotal = 0;
                document.querySelectorAll('.misutech_cart_item').forEach(it => {
                    const itPrice = parseFloat(it.dataset.price);
                    const itQty = parseInt(it.querySelector('.misutech_qty_input').value) || 0;
                    newSummaryTotal += itPrice * itQty;
                });
                
                const summaryValueEls = document.querySelectorAll('.misutech_cart_summary_value');
                if (summaryValueEls.length > 0) summaryValueEls[0].textContent = formatMoney(newSummaryTotal);
                
                const totalPriceEls = document.querySelectorAll('.misutech_cart_summary_total_price');
                if (totalPriceEls.length > 0) totalPriceEls[0].textContent = formatMoney(newSummaryTotal);
                
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => {
                    fetch('/api/v1/cart/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: newQty
                        })
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            if (newQty === 0) {
                                item.remove();
                                if (document.querySelectorAll('.misutech_cart_item').length === 0) {
                                    location.reload();
                                }
                            }
                            // Sync with server data
                            if (summaryValueEls.length > 0) summaryValueEls[0].textContent = formatMoney(data.total_price);
                            if (totalPriceEls.length > 0) totalPriceEls[0].textContent = formatMoney(data.total_price);
                            
                            const countEls = document.querySelectorAll('.misutech_home_cart_count');
                            countEls.forEach(el => el.textContent = data.cart_count);
                            const labelEls = document.querySelectorAll('.misutech_home_header_action_copy strong');
                            labelEls.forEach(el => el.textContent = data.cart_count + " sản phẩm");
                        }
                    }).catch(err => console.error(err));
                }, 1000);
            }
            
            if (btns.length >= 2) {
                btns[0].addEventListener('click', () => scheduleUpdate(parseInt(input.value) - 1));
                btns[1].addEventListener('click', () => scheduleUpdate(parseInt(input.value) + 1));
            }
            input.addEventListener('change', () => scheduleUpdate(parseInt(input.value)));
            input.addEventListener('keyup', () => {
                clearTimeout(updateTimeout);
                updateTimeout = setTimeout(() => scheduleUpdate(parseInt(input.value)), 1000);
            });
        });
        
        document.querySelectorAll('.misutech_cart_remove_btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const item = this.closest('.misutech_cart_item');
                const productId = item.dataset.id;
                
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                    fetch('/api/v1/cart/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ product_id: productId, quantity: 0 })
                    }).then(() => location.reload());
                }
            });
        });
    });
</script>
@endpush
