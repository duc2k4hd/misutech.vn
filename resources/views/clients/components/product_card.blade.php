<article class="misutech_home_product_card" data-name="{{ $product->name }}"
    data-price="{{ $product->price }}"
    data-brand="{{ $product->brand ? $product->brand->slug : '' }}" data-availability="in-stock"
    data-category="{{ $product->category ? $product->category->slug : '' }}">

    @if ($product->sale_price && $product->sale_price < $product->price)
        <span class="misutech_home_sale_badge">-{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%</span>
    @endif

    <div class="misutech_home_product_media">
        <a href="{{ route('product.show', $product->slug) }}">
            <img src="{{ $product->thumbnailMedia->first()?->url ?? ($product->thumbnail_url ?? asset('storage/clients/imgs/products/no-image.png')) }}"
                alt="{{ $product->name ?? 'No image' }}"
                loading="lazy"
                decoding="async">
        </a>
        <div class="misutech_home_product_actions">
            <button class="misutech_home_product_action" type="button" data-favorite aria-label="Thêm vào yêu thích">♡</button>
            <button class="misutech_home_product_action" type="button" data-cart data-product-id="{{ $product->id }}" aria-label="Thêm vào giỏ hàng">＋</button>
        </div>
    </div>

    <div class="misutech_home_product_info">
        @if ($product->brand || $product->sku)
            <div class="misutech_home_product_meta_tags">
                @if ($product->brand)
                    <span class="product_meta_brand">{{ $product->brand->name }}</span>
                @endif
                @if ($product->sku)
                    <span class="product_meta_sku">Mã: {{ $product->sku }}</span>
                @endif
            </div>
        @endif

        <h3 class="misutech_home_product_name">
            <a href="{{ route('product.show', $product->slug) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a>
        </h3>

        <div class="misutech_home_product_rating">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= round($product->rating_average ?? 5))
                    <span class="star-filled">★</span>
                @else
                    <span class="star-empty">☆</span>
                @endif
            @endfor
            <span class="rating_text">({{ number_format($product->rating_average ?? 5, 1) }})</span>
        </div>

        {{-- Khung thông tin chỉ hiển thị ở Dạng Danh Sách (1 Cột) --}}
        <div class="misutech_home_product_list_desc">
            @if (!empty($product->short_description))
                <p class="product_short_desc">{{ Str::limit(strip_tags($product->short_description), 140) }}</p>
            @endif
            <ul class="product_feature_badges">
                <li><span class="badge_icon">✓</span> Hàng chính hãng 100%</li>
                <li><span class="badge_icon">✓</span> Bảo hành 12 tháng</li>
                <li><span class="badge_icon">✓</span> Đầy đủ CO/CQ & VAT</li>
                <li><span class="badge_icon">✓</span> Sẵn hàng giao nhanh</li>
            </ul>
        </div>
    </div>

    <div class="misutech_home_product_footer_action">
        <div class="misutech_home_product_stock_badge">
            <span class="stock_dot"></span> Còn hàng
        </div>

        <div class="misutech_home_product_price_wrapper">
            @if ($product->sale_price && $product->sale_price < $product->price)
                <div class="misutech_home_product_price_line">
                    <strong class="misutech_home_product_price">{{ number_format($product->sale_price, 0, ',', '.') }} VNĐ</strong>
                    <del class="misutech_home_product_old_price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</del>
                </div>
            @elseif($product->price > 0)
                <div class="misutech_home_product_price_line">
                    <strong class="misutech_home_product_price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</strong>
                </div>
            @else
                <div class="misutech_home_product_price_line">
                    <strong class="misutech_home_product_price" style="color: var(--misutech_home_primary, #009dde);">Liên hệ báo giá</strong>
                </div>
            @endif
        </div>

        <div class="misutech_home_product_list_buttons">
            <a href="{{ route('product.show', $product->slug) }}" class="btn_view_product">
                Xem chi tiết
            </a>
            <button class="btn_add_to_cart" type="button" data-cart data-product-id="{{ $product->id }}">
                + Thêm giỏ hàng
            </button>
        </div>
    </div>
</article>
